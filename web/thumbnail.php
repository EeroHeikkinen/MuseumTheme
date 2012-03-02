<?php
/**
 * Thumbnail Image Generator
 *
 * PHP version 5
 *
 * Copyright (C) Villanova University 2007.
 * Copyright (C) The National Library of Finland 2012.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category VuFind
 * @package  Thumbnail_Generator
 * @author   Andrew S. Nagy <vufind-tech@lists.sourceforge.net>
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/use_of_external_content Wiki
 */
require_once 'sys/ConfigArray.php';
require_once 'sys/Proxy_Request.php';
require_once 'sys/Logger.php';
require_once 'sys/ConnectionManager.php';
require_once 'RecordDrivers/Factory.php';

// Retrieve values from configuration file
$configArray = readConfig();
$logger = new Logger();

// global to hold filename constructed from Record ID
$localFile = '';

// Proxy server settings
if (isset($configArray['Proxy']['host'])) {
    if (isset($configArray['Proxy']['port'])) {
        $proxy_server
            = $configArray['Proxy']['host'].":".$configArray['Proxy']['port'];
    } else {
        $proxy_server = $configArray['Proxy']['host'];
    }
    $proxy = array(
        'http' => array(
            'proxy' => "tcp://$proxy_server", 'request_fulluri' => true
        )
    );
    stream_context_get_default($proxy);
}

// Display a fail image unless our parameters pass inspection and we are able to
// display an ISBN or content-type-based image.
if (!sanitizeParameters()) {
    dieWithFailImage();
} else if (!fetchFromRecord($_GET['id'], $_GET['size'], isset($_GET['index']) ? $_GET['index'] : null)) {
    dieWithFailImage();
}

/* END OF INLINE CODE */

/**
 * Sanitize incoming parameters. File name is not sanitized here. 
 *
 * @return  bool       True if parameters ok, false on failure.
 */
function sanitizeParameters()
{
    $validSizes = array('small', 'medium', 'large');
    if (!count($_GET) || !in_array($_GET['size'], $validSizes)) {
        return false;
    }
    if (isset($_GET['index']) && !ctype_digit($_GET['index'])) {
        return false;
    }
    return true;
}

/**
 * Load bookcover from URL from cache or remote provider and display if possible.
 *
 * @param string $id    Record ID
 * @param string $size  Size of cover (large, medium, small)
 * @param int    $index Image index (null = thumbnail, otherwise zero-based index)
 *
 * @return bool        True if image displayed, false on failure.
 */
function fetchFromRecord($id, $size, $index = null)
{
    global $configArray;

    if (empty($id)) {
        return false;
    }

    $localFile = 'images/covers/' . $size . '/' . urlencode($id) . (isset($index) ? "_$index" : '') . '.jpg';
    if (is_readable($localFile)) {
        // Load local cache if available
        header('Content-type: image/jpeg');
        echo readfile($localFile);
        return true;
    } else {
        // Fetch from url
        $db = ConnectionManager::connectToIndex();
        if (!($record = $db->getRecord($id))) {
            return false;
        }
        $recordDriver = RecordDriverFactory::initRecordDriver($record);
        
        if (!isset($index)) {
            $url = $recordDriver->getThumbnailURL($size);
        } else {
            $images = array_keys($recordDriver->getAllImages());
            if (isset($images[$index])) {
                $url = $images[$index];
            } 
        }
        if ($url) {
            return processImageURL($url, $localFile, $size);
        }
    }
    return false;
}

/**
 * Display the user-specified "cover unavailable" graphic (or default if none
 * specified) and terminate execution.
 *
 * @return void
 * @author Thomas Schwaerzler <vufind-tech@lists.sourceforge.net>
 */
function dieWithFailImage()
{
    global $configArray, $logger;

    // Get "no cover" image from config.ini:
    $noCoverImage = isset($configArray['Content']['noCoverAvailableImage'])
        ? $configArray['Content']['noCoverAvailableImage'] : null;

    // No setting -- use default, and don't log anything:
    if (empty($noCoverImage)) {
        // log?
        dieWithDefaultFailImage();
    }

    // If file defined but does not exist, log error and display default:
    if (!file_exists($noCoverImage) || !is_readable($noCoverImage)) {
        $logger->log(
            "Cannot access file: '$noCoverImage' in directory " . dirname(__FILE__),
            PEAR_LOG_ERR
        );
        dieWithDefaultFailImage();
    }

    // Array containing map of allowed file extensions to mimetypes (to be extended)
    $allowedFileExtensions = array(
        "gif" => "image/gif",
        "jpeg" => "image/jpeg", "jpg" => "image/jpeg",
        "png" => "image/png",
        "tiff" => "image/tiff", "tif" => "image/tiff"
    );

    // Log error and bail out if file lacks a known image extension:
    $fileExtension = strtolower(end(explode('.', $noCoverImage)));
    if (!array_key_exists($fileExtension, $allowedFileExtensions)) {
        $logger->log(
            "Illegal file-extension '$fileExtension' for image '$noCoverImage'",
            PEAR_LOG_ERR
        );
        dieWithDefaultFailImage();
    }

    // Get mime type from file extension:
    $mimeType = $allowedFileExtensions[$fileExtension];

    // Display the image and die:
    header("Content-type: $mimeType");
    echo readfile($noCoverImage);
    exit();
}

/**
 * Display the default "cover unavailable" graphic and terminate execution.
 *
 * @return void
 */
function dieWithDefaultFailImage()
{
    header('Content-type: image/gif');
    echo readfile('images/noCover2.gif');
    exit();
}

/**
 * Load image from URL, store in cache if requested, display if possible.
 *
 * @param string $url   URL to load image from
 * @param string $cache Boolean -- should we store in local cache?
 *
 * @return bool         True if image displayed, false on failure.
 */
function processImageURL($url, $localFile, $size, $cache = true)
{
    if ($image = @file_get_contents($url)) {
        // Figure out file paths -- $tempFile will be used to store the downloaded
        // image for analysis.  $finalFile will be used for long-term storage if
        // $cache is true or for temporary display purposes if $cache is false.
        $tempFile = str_replace('.jpg', uniqid(), $localFile);
        $finalFile = $cache ? $localFile : $tempFile . '.jpg';

        // If some services can't provide an image, they will serve a 1x1 blank
        // or give us invalid image data.  Let's analyze what came back before
        // proceeding.
        if (!@file_put_contents($tempFile, $image)) {
            die("Unable to write to image directory.");
        }
        list($width, $height, $type) = @getimagesize($tempFile);

        // File too small -- delete it and report failure.
        if ($width < 2 && $height < 2) {
            @unlink($tempFile);
            return false;
        }
        
        // Convert to JPEG and downsize the image if necessary.
        switch ($size) {
            case 'small': $maxWidth = $maxHeight = 200; break;
            case 'medium': $maxWidth = 350; $maxHeight = 250; break;
            default: $maxWidth = $maxHeight = 2048; 
        }
        if ($type != IMAGETYPE_JPEG || $width > $maxWidth || $height > $maxHeight) {
            // We no longer need the temp file:
            @unlink($tempFile);

            // We can't proceed if we don't have image conversion functions:
            if (!is_callable('imagecreatefromstring')) {
                return false;
            }

            // Try to create a GD image and rewrite as JPEG, fail if we can't:
            if (!($imageGD = @imagecreatefromstring($image))) {
                return false;
            }
            
            $ratio = $width / $height;
            if ($width > $height) {
                $newWidth = $maxWidth;
                $newHeight = $newWidth / $ratio;
            } else {
                $newHeight = $maxHeight;
                $newWidth = $newHeight * $ratio;
            }
            $imageGDResized = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($imageGDResized, $imageGD, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);            
            if (!@imagejpeg($imageGDResized, $finalFile)) {
                return false;
            }
        } else {
            // If $tempFile is already a suitable JPEG, let's store it in the cache.
            @rename($tempFile, $finalFile);
        }

        // Display the image:
        header('Content-type: image/jpeg');
        readfile($finalFile);

        // If we don't want to cache the image, delete it now that we're done.
        if (!$cache) {
            @unlink($finalFile);
        }

        return true;
    } else {
        return false;
    }
}
?>
