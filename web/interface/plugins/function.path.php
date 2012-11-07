<?php
/**
 * path function Smarty plugin
*
* PHP version 5
*
* Copyright (C) Villanova University 2010.
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
* @package  Smarty_Plugins
* @author   Tuan Nguyen <tuan@yorku.ca>
* @author   Ere Maijala <ere.maijala@helsinki.fi>
* @author   Kalle Pyykkönen <kalle.pyykkonen@helsinki.fi>
* @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
* @link     http://vufind.org/wiki/building_a_plugin Wiki
*/

/**
 * Smarty plugin
* -------------------------------------------------------------
* File:     function.path.php
* Type:     function
* Name:     path
* Purpose:  Returns path to right threme directory
*           Supports one parameter:
*              filename (required) - filename including subfolder(s) 
*                  to load from interface/themes/[theme]/ folder.
* -------------------------------------------------------------
*
* @param array      $params Incoming parameter array
* @param object     &$smarty Smarty object
* @return string    path
*/ // @codingStandardsIgnoreStart
function smarty_function_path($params, &$smarty)
{   // @codingStandardsIgnoreEnd
    // Extract details from the config file, Smarty interface and parameters
    global $configArray;
    
    $url = $configArray['Site']['url'];
    $path = $configArray['Site']['path'];
    $local = $configArray['Site']['local'];
    $themes = explode(',', $smarty->getVuFindTheme());
    $filename = $params['filename'];
    // Loop through the available themes looking for the requested JS file:
    $path = false;
    foreach ($themes as $theme) {
        $theme = trim($theme);
    
        // If the file exists on the local file system, set $image to the relative
        // path needed to link to it from the web interface.
        if (file_exists("{$local}/interface/themes/{$theme}/{$filename}")) {
            $path = "{$url}{$path}/interface/themes/{$theme}/{$filename}";
            break;
        }
    }
    
    // If we couldn't find the file, check the global area; if that
    // still doesn't help, we shouldn't try to link to it:
    if (!$path) {
        if (file_exists("{$local}/{$filename}")) {
            $path = "{$url}{$path}/{$filename}";
        } else {
            return '';
        }
    }
    
    // File exists, return path.
    
    return $path;
}