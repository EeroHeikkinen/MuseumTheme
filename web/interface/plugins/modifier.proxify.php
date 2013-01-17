<?php
/**
 * URL proxifier Smarty plugin
 *
 * PHP version 5
 *
 * Copyright (C) The National Library of Finland 2013
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
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_plugin Wiki
 */

/**
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     proxify
 * Purpose:  Prepend a URL with a proxy address
 * -------------------------------------------------------------
 *
 * @param string $str    URL to proxify
 *
 * @return string        Proxified URL
 */ // @codingStandardsIgnoreStart
function smarty_modifier_proxify($str)
{   // @codingStandardsIgnoreEnd
    global $configArray;
    
    if (!isset($configArray['EZproxy']['host']) || !$configArray['EZproxy']['host']) {
        return $str;
    }

    if (isset($configArray['EZproxy']['include_url']) || isset($configArray['EZproxy']['include_url_re'])) {
        if (isset($configArray['EZproxy']['include_url'])) {
            $pass = false;
            foreach ($configArray['EZproxy']['include_url'] as $mask) {
                if (strstr($str, $mask)) {
                    $pass = true;
                    break;
                }
            }
        }
    
        if (!$pass && isset($configArray['EZproxy']['include_url_re'])) {
            $pass = false;
            foreach ($configArray['EZproxy']['include_url_re'] as $mask) {
                if (preg_match($mask, $str)) {
                    $pass = true;
                    break;
                }
            }
        }
        
        if (!$pass) {
            return $str;
        }
    }
    
    if (isset($configArray['EZproxy']['exclude_url'])) {
        foreach ($configArray['EZproxy']['exclude_url'] as $mask) {
            if (strstr($str, $mask)) {
                return $str;
            }
        }
    }

    if (isset($configArray['EZproxy']['exclude_url_re'])) {
        foreach ($configArray['EZproxy']['exclude_url_re'] as $mask) {
            if (preg_match($mask, $str)) {
                return $str;
            }
        }
    }
    
    return $configArray['EZproxy']['host'] . '/login?qurl=' . urlencode($str);
}
