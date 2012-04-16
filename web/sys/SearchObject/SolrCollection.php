<?php
/**
 * Solr Search Object class for collections
 *
 * PHP version 5
 *
 * Copyright (C) Villanova University 2010.
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
 * @package  SearchObject
 * @author   Lutz Biedinger <lutz.biedinger@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_search_object Wiki
 */
require_once 'sys/Proxy_Request.php';   // needed for constant definitions
require_once 'sys/SearchObject/Base.php';
require_once 'RecordDrivers/Factory.php';

/**
 * Solr Search Object class for collections
 *
 * This is a speccial search object class to be used for collection searches.
 *
 * @category VuFind
 * @package  SearchObject
 * @author   Lutz Biedinger <lutz.biedinger@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_search_object Wiki
 */
class SearchObject_SolrCollection extends SearchObject_Solr
{
    /**
     *
     * The field which defines somehting as being a collection
     * this is usually either hierarchy_parent_id or
     * hierarchy_top_id
     * @var string
     */
    private $_collectionField = null;//"hierarchy_parent_id";

    /**
     * Initialise the object from the global
     *  search parameters in $_REQUEST.
     *
     * @return boolean
     * @access public
     */
    public function init()
    {
        global $module;
        global $action;
        
        $searchSettings = getExtraConfigArray('searches');
        //get collection specific sort options
    	if (isset($searchSettings['CollectionModuleSort'])) {
            $this->sortOptions = $searchSettings['CollectionModuleSort'];
        } else {
            $this->sortOptions = array('title' => 'sort_title',
                'year' => 'sort_year', 'year asc' => 'sort_year asc',
                'callnumber' => 'sort_callnumber', 'author' => 'sort_author');
        }
        $this->defaultSort = key($this->sortOptions);//array_slice($this->sortOptions, 0, 1);


        // Call the standard initialization routine in the parent:
        parent::init();

        // Log a special type of search
        $this->searchType = 'collection';
        // We don't spellcheck this screen
        // it's not for free user intput anyway
        $this->spellcheck  = false;

        //$collectionField = "hierarchy_top_id";//hierarchy_parent_id

        // Prepare the search
        if ($this->_collectionField == null) {
            $this->setCollectionField();
        }
        $this->addFilter($this->_collectionField . ":" . $this->collectionID);

        // Sorting - defaults to off with unlimited facets, so let's
        // be explicit here for simplicity.
        if (isset($_REQUEST['sort'])
            && ($_REQUEST['sort'] == $this->_collectionField)
        ) {
            $this->setFacetSortOrder('index');
        } else {
            $this->setFacetSortOrder('count');
        }

        return true;
    } // End init()

    /**
     * Setter for _collectionField
     *
     * @param string $collectionField The collection field
     *
     * @return void
     * @access public
     */
    public function setCollectionField($collectionField = "hierarchy_top_id")
    {
        $this->_collectionField = $collectionField;
    }

    /**
     * Get the base URL for search results (including ? parameter prefix).
     *
     * @return string Base URL
     * @access protected
     */
    protected function getBaseUrl()
    {
        global $action;
        // Base URL is different for author searches:
        if ($this->searchType == 'collection') {
            return $this->serverUrl."/Collection/" .
            $this->collectionID. "/" . $action ."?";
        }
        // If none of the special cases were met, use the default from the parent:
        return parent::getBaseUrl();
    }

    /**
     * Load all recommendation settings from the relevant ini file.  Returns an
     * associative array where the key is the location of the recommendations (top
     * or side) and the value is the settings found in the file (which may be either
     * a single string or an array of strings).
     *
     * @return array associative: location (top/side) => search settings
     * @access protected
     */
    protected function getRecommendationSettings()
    {
        //collection recommendations
        $searchSettings = getExtraConfigArray('searches');
        return isset($searchSettings['CollectionModuleRecommendations'])
                ? $searchSettings['CollectionModuleRecommendations']
                : array('side' => array('ExpandFacets:Collection'));


        // Use default case from parent class the rest of the time:
        return parent::getRecommendationSettings();
    }

    /**
     * Gets the facet limit
     *
     * @return int facetLimit
     * @access public
     */
    public function getFacetLimit()
    {
        return $this->facetLimit;
    }


     /**
     * set the collection id for the collection module
     *
     * @param string $collectionID A collection ID
     *
     * @return void
     * @access public
     */
    public function collectionID($collectionID)
    {
        if (isset($collectionID)) {
            $this->collectionID = $collectionID;
        }
    }

    /**
     * Override the Buildinig of the url for the current search
     * to remove the collection specific fields.
     *
     * @return string URL of a search
     * @access public
     */
    public function renderSearchUrl()
    {
        // Get the base URL and initialize the parameters attached to it:
        $url = $this->getBaseUrl();
        $params = $this->getSearchParams();

        // Add any filters
        if (count($this->filterList) > 0) {
            foreach ($this->filterList as $field => $filter) {
                if ($field != $this->_collectionField) {
                    foreach ($filter as $value) {
                        $params[] = urlencode("filter[]") . '=' .
                               urlencode("$field:\"$value\"");
                    }
                }
            }
        }

        // Sorting
        if ($this->sort != null && $this->sort != $this->getDefaultSort()) {
            $params[] = "sort=" . urlencode($this->sort);
        }

        // Page number
        if ($this->page != 1) {
            // Don't url encode if it's the paging template
            if ($this->page == '%d') {
                $params[] = "page=" . $this->page;
            } else {
                // Otherwise... encode to prevent XSS.
                $params[] = "page=" . urlencode($this->page);
            }
        }

        // View
        if ($this->view != null) {
            $params[] = "view=" . urlencode($this->view);
        }

        // Limit
        if ($this->limit != null && $this->limit != $this->defaultLimit) {
            $params[] = "limit=" . urlencode($this->limit);
        }

        // Join all parameters with an escaped ampersand,
        //   add to the base url and return
        return $url . join("&", $params);
    }
}
?>
