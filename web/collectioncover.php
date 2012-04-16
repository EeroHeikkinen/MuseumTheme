<?php
/**
 * Collection Cover Generator
 *
 * PHP version 5
 *
 * Copyright (C) Villanova University 2012.
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
 * @package  Cover_Generator
 * @author   Lutz Biedinger <lutz.biedinger@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/use_of_external_content Wiki
 */

require_once 'sys/ConfigArray.php';
require_once 'sys/Solr.php';



// Retrieve values from configuration file
$configArray = readConfig();

// global to hold filename constructed from Title/id
$localFile = '';

// Display a fail image unless our parameters pass inspection and we are able to
// display a title image.
if (!sanitizeParameters()) {
    dieWithFailImage();
} else if (!fetchFromTitle($_GET['title'], $_GET['size'])
) {
    dieWithFailImage();
}

/* END OF INLINE CODE */

/**
 * Sanitize incoming parameters to avoid filesystem attacks.  We'll make sure the
 * provided size matches a whitelist, and we'll strip illegal characters from the
 * ISBN and/or contentType
 *
 * @return  bool       True if parameters ok, false on failure.
 */
function sanitizeParameters()
{
    $validSizes = array('small', 'medium', 'large');
    if (!count($_GET) || !in_array($_GET['size'], $validSizes)) {
        return false;
    }
    return true;
}

/**
 * Load bookcover fom URL from cache or remote provider and display if possible.
 *
 * @param string $isn  ISBN (10 characters preferred)
 * @param string $size Size of cover (large, medium, small)
 *
 * @return bool        True if image displayed, false on failure.
 */
function fetchFromTitle($title, $size)
{
    global $configArray;
    global $localFile;

    if (empty($title)) {
        return false;
    }
    //print_r($title);
    $solr = new solr($configArray['Index']['url']);
    $collections = $solr->getCollectionsFromName($title);
    if (count($collections) > 1 || count($collections) == 0){
    	return false;
    }
    
    $id = $collections[0]['id'];
	//print_r($id);
    // Load local cache if available
    //header('Content-type: text/html');
    //return true;

    if ($id) {
        $localFile = 'images/collection_covers/' . $size . '/' . $id . '.jpg';
    } else {
    	return false;
    }
    
    if (is_readable($localFile)) {
        // Load local cache if available
        header('Content-type: image/jpeg');
        echo readfile($localFile);
        return true;
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