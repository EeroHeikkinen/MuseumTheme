<?php
/**
 * JSON handler for facet requests 
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
 * @package  Controller_Record
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */
require_once 'JSON.php';

/**
 * JSON Facet request action. Returns facets for the given search and hierarchy level.
 *
 * @category VuFind
 * @package  Controller_Record
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */

class JSON_Facets extends JSON
{
    /**
     * Get data and output in JSON
     *
     * @return void
     * @access public
     */
    public function getFacets()
    {
        // Initialize from the current search globals
        $searchObject = SearchObjectFactory::initSearchObject();
        $searchObject->init();
        
        $prefix = explode('/', isset($_REQUEST['facetPrefix']) ? $_REQUEST['facetPrefix'] : '', 2);
        $prefix = isset($prefix[1]) ? $prefix[1] : $prefix[0];
        $facetName = $_REQUEST['facetName'];
        $level = isset($_REQUEST['facetLevel']) ? $_REQUEST['facetLevel'] : false;
        if ($level !== false) {
            $searchObject->addFacetPrefix(array($facetName => "$level/$prefix"));
        } elseif ($prefix) {
            $searchObject->addFacetPrefix(array($facetName => $prefix));
        }
        $result = $searchObject->processSearch(true, true);
        if (PEAR::isError($result)) {
            $this->output("Search failed: $result", JSON::STATUS_ERROR);
            return;
        }
        $facets = $searchObject->getFacetList(array($facetName => $facetName));
        if (!isset($facets[$facetName]['list'])) {
            $this->output(array(), JSON::STATUS_OK);
            return;
        }
        $facets = $facets[$facetName]['list'];
        
        // For hierarchical facets: Now that we have the current facet level, try next level
        // so that we can indicate which facets on this level have children.
        if ($level !== false) {
            ++$level;
            $searchObject->addFacetPrefix(array($facetName => "$level/$prefix"));
            $result = $searchObject->processSearch(true, true);
            if (PEAR::isError($result)) {
                $this->output("Search failed: $result", JSON::STATUS_ERROR);
                return;
            }
            $subFacets = $searchObject->getFacetList(array($facetName => $facetName));
            if (isset($subFacets[$facetName]['list'])) {
                foreach ($subFacets[$facetName]['list'] as $subFacet) {
                    $subFacetCode = implode('/', array_slice(explode('/', $subFacet['untranslated']), 1, $level));
                    foreach ($facets as &$facet) {
                        $facetCode = implode('/', array_slice(explode('/', $facet['untranslated']), 1, $level));
                        if ($facetCode == $subFacetCode) { 
                            $facet['children'] = true;
                            break;
                        }
                    }
                }
            }
        }
        
        $this->output($facets, JSON::STATUS_OK);
    }
}
