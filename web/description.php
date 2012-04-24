<?php
/**
 * BTJ Descriptions
 *
 * PHP version 5
 *
 * Copyright (C) Villanova University 2007.
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
 * @package  BTJ Descriptions
 * @author   
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

$url = fetchFromRecord($_GET['id']);

if ($url) {
    if ($description = getDescription($url)){
	    echo $description;
        return true; 
    }
    } 
else {
    return false;
}


/* END OF INLINE CODE */

/**
 * Get URL from Record.
 *
 * @param string $id    Record ID
 *
 * @return mixed: Return URL if available, otherwise false.
 */
function fetchFromRecord($id)
{
    global $configArray;

    if (empty($id)) {
        return false;
    }
    // Get URL
    $db = ConnectionManager::connectToIndex();
    if (!($record = $db->getRecord($id))) {
        return false;
    }
    $recordDriver = RecordDriverFactory::initRecordDriver($record);
    
    $url = $recordDriver->getDescriptionURL();
    if ($url) {
        return $url;
    } else {
        return false;
    }
}

/**
* Return a string of the content of the BTJ description, if available; false otherwise.
*
* @return boolean
*/
     
function getDescription($url)
{
	if ($url) {
	    if ($content = @file_get_contents($url)) {
	        $content = preg_replace('/.*<.B>(.*)/', '\1', $content);
		    $content = strip_tags($content);
		    $content = utf8_encode($content);	    
			return $content;
		    } else {
			    return false;
		    }		    
	    } else {
		    return false;
	}
}


?>
