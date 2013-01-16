<?php
/**
 * translatePrefix Smarty plugin
 *
 * PHP version 5
 *
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
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_plugin Wiki
 */

/**
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     translatePrefix
 * Purpose:  Performs translation with the given prefix
 * -------------------------------------------------------------
 *
 * @param string $str    String to translate
 * @param string $prefix Prefix to prepend
 *
 * @return string        Extracted substring
 */ // @codingStandardsIgnoreStart
function smarty_modifier_translate_prefix($str, $prefix)
{   // @codingStandardsIgnoreEnd
    return translate(array('text' => $str, 'prefix' => $prefix));
}
?>