<?php
/**
 * BTJ Descriptions
 *
 * PHP version 5
 *
 * Copyright (C) The National Library of Finland 2012
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
 * @package  BTJ_Descriptions
 * @author   Bjarne Beckmann <bjarne.beckmann@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/use_of_external_content Wiki
 */
require_once 'sys/ConfigArray.php';
require_once 'sys/Proxy_Request.php';
require_once 'sys/Logger.php';
require_once 'sys/ConnectionManager.php';
require_once 'RecordDrivers/Factory.php';
require_once 'sys/Autoloader.php';
spl_autoload_register('vuFindAutoloader');

$configArray = readConfig();

fetchFromRecord($_GET['id']);

/* END OF INLINE CODE */

/**
 * Get URL from Record.
 *
 * @param string $id Record ID
 *
 * @return mixed: true if description available, otherwise false.
 */
function fetchFromRecord($id)
{
    global $configArray;

    if (empty($id)) {
        echo ('false');
        return false;
    }
    
    $localFile = 'interface/cache/description_' . urlencode($id) . '.txt';
    if (is_readable($localFile)) {
        // Load local cache if available
        header('Content-type: text/plain');
        echo readfile($localFile);
        return true;
    } else {    
    
        // Get URL
        $db = ConnectionManager::connectToIndex();
        if (!($record = $db->getRecord($id))) {
            return false;
        }
        $recordDriver = RecordDriverFactory::initRecordDriver($record);
        
        $url = $recordDriver->getDescriptionURL();
        // Get, manipulate, save and display content if available
        if ($url) {
            if ($content = @file_get_contents($url)) {
                $content = preg_replace('/.*<.B>(.*)/', '\1', $content);
                $content = strip_tags($content);
                $content = utf8_encode($content); 
                file_put_contents($localFile, $content);
                echo $content;
                return true;
            } else {
                return false;
            }     
        } else {
            return false;
        }
    }
}

?>
