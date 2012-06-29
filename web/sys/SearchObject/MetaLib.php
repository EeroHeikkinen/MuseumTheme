<?php
/**
 * A derivative of the Search Object for use with MetaLib.
 *
 * PHP version 5
 *
 * Copyright (C) Villanova University 2010.
 * Copyright (C) Ere Maijala, The National Library of Finland 2012.
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
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_search_object Wiki
 */
require_once 'sys/SearchObject/Base.php';

/**
 * A derivative of the Search Object for use with MetaLib.
 *
 * @category VuFind
 * @package  SearchObject
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_search_object Wiki
 */
class SearchObject_MetaLib extends SearchObject_Base
{
    // OTHER VARIABLES
    private $_metaLib;      // MetaLib API
    private $_indexResult;  // MetaLib API Response
    
    // In the MetaLib configuration, facets may have extra parameters appended;
    // in most cases, we want to strip these off, but this array lets us store
    // all the extra parameters so they can be passed to the MetaLib class.
    private $_fullFacetSettings = array();

    protected $_searchSets = array();
    protected $_set = '';
    
    /**
     * Constructor. Initialise some details about the server
     *
     * @access public
     */
    public function __construct()
    {
        global $configArray;

        // Standard logic from parent class:
        parent::__construct();

        // Set up appropriate results action:
        $this->resultsModule = 'MetaLib';
        $this->resultsAction = 'Search';

        // Set up basic and advanced MetaLib search types; default to basic.
        $this->searchType = $this->basicSearchType = 'MetaLib';
        $this->advancedSearchType = 'MetaLibAdvanced';

        $config = getExtraConfigArray('MetaLib');
        if (empty($config)) {
            return;
        }
        
        // No facet config

        // Set up spelling preference
        if (isset($config['Spelling']['enabled'])) {
            $this->spellcheck    = $config['Spelling']['enabled'];
        }

        // Set up sort options
        $this->sortOptions = $config['Sorting'];
        if (isset($config['General']['default_sort'])) {
            $this->defaultSort = $config['General']['default_sort'];
        }

        // Set up search options
        $this->basicTypes = $config['Basic_Searches'];
        if (isset($config['Advanced_Searches'])) {
            $this->advancedTypes = $config['Advanced_Searches'];
        }

        $this->_searchSets = getExtraConfigArray('MetaLibSets');
        reset($this->_searchSets);
        $this->_set = key($this->_searchSets);
        
        // Set up recommendations options -- settings are found in MetaLib.ini:
        $this->recommendIni = 'MetaLib';

        // Load limit preferences (or defaults if none in .ini file):
        if (isset($config['General']['limit_options'])) {
            $this->limitOptions
            = explode(",", $config['General']['limit_options']);
        } elseif (isset($config['General']['default_limit'])) {
            $this->limitOptions = array($this->defaultLimit);
        } else {
            $this->limitOptions = array(20);
        }
        
        // Connect to MetaLib
        $this->_metaLib = new MetaLib();
    }

    /**
     * Initialise the object from the global
     *  search parameters in $_REQUEST.
     *
     * @return boolean
     * @access public
     */
    public function init()
    {
        // Call the standard initialization routine in the parent:
        parent::init();

        //********************
        // Check if we have a saved search to restore -- if restored successfully,
        // our work here is done; if there is an error, we should report failure;
        // if restoreSavedSearch returns false, we should proceed as normal.
        $restored = $this->restoreSavedSearch();
        if ($restored === true) {
            return true;
        } else if (PEAR::isError($restored)) {
            return false;
        }

        $this->initView();
        $this->initPage();
        $this->initSort();
        $this->initFilters();
        $this->initLimit();
        
        // Try to find a basic search first; check for advanced if no basic found.
        if (!$this->initBasicSearch()) {
            $this->initAdvancedSearch();
        }
        
        $this->_set = isset($_REQUEST['set']) ? $_REQUEST['set'] : '';  
        if (!isset($this->_searchSets[$this->_set]) && strncmp($this->_set, '_ird:', 5) != 0) {
            reset($this->_searchSets);
            $this->_set = key($this->_searchSets);
        }
        
        return true;
    }

    public function getSearchSet()
    {
        return $this->_set;
    }

    public function setSearchSet($set)
    {
        $this->_set = $set;
    }
    
    public function getSearchSets()
    {
        $result = array();
        foreach ($this->_searchSets as $key => $set) {
            $result[$key] = $set['name'];
        }
        return $result;
    }
    
    /**
     * Add a field to facet on.
     *
     * @param string $newField Field name
     * @param string $newAlias Optional on-screen display label
     *
     * @return void
     * @access public
     */
    public function addFacet($newField, $newAlias = null)
    {
        // Save the full field name (which may include extra parameters);
        // we'll need these to do the proper search using the MetaLib class:
        $this->_fullFacetSettings[] = $newField;

        // Strip parameters from field name if necessary (since they get
        // in the way of most Search Object functionality):
        $newField = explode(',', $newField);
        $newField = trim($newField[0]);
        parent::addFacet($newField, $newAlias);
    }

    /**
     * Returns the stored list of facets for the last search
     *
     * @param array $filter         Array of field => on-screen description listing
     * all of the desired facet fields; set to null to get all configured values.
     * @param bool  $expandingLinks If true, we will include expanding URLs (i.e.
     * get all matches for a facet, not just a limit to the current search) in the
     * return array.
     *
     * @return array                Facets data arrays
     * @access public
     */
    public function getFacetList($filter = null, $expandingLinks = false)
    {
        return array();
    }

    /**
     * Process spelling suggestions from the results object
     *
     * @return void
     * @access private
     */
    private function _processSpelling()
    {
        if (isset($this->_indexResult['didYouMeanSuggestions'])
            && is_array($this->_indexResult['didYouMeanSuggestions'])
        ) {
            foreach ($this->_indexResult['didYouMeanSuggestions'] as $current) {
                if (!isset($this->suggestions[$current['originalQuery']])) {
                    $this->suggestions[$current['originalQuery']] = array(
                        'suggestions' => array()
                    );
                }
                $this->suggestions[$current['originalQuery']]['suggestions'][]
                    = $current['suggestedQuery'];
            }
        }
    }

    /**
     * Actually process and submit the search
     *
     * @param bool $returnIndexErrors Should we die inside the index code if we
     * encounter an error (false) or return it for access via the getIndexError()
     * method (true)?
     * @param bool $recommendations   Should we process recommendations along with
     * the search itself?
     *
     * @return object                 MetaLib result structure (for now)
     * @access public
     */
    public function processSearch(
        $returnIndexErrors = false, $recommendations = false
    ) 
    {
        // Build a recommendations module appropriate to the current search:
        if ($recommendations) {
            $this->initRecommendations();
        }

        // Get time before the query
        $this->startQueryTimer();

        // The "relevance" sort option is a VuFind reserved word; we need to make
        // this null in order to achieve the desired effect with MetaLib:
        $finalSort = ($this->sort == 'relevance') ? null : $this->sort;

        // Perform the actual search
        $irds = strncmp($this->_set, '_ird:', 5) == 0
            ? substr($this->_set, 5) 
            : $this->_searchSets[$this->_set]['ird_list'];
        $this->_indexResult = $this->_metaLib->query(
            $irds, $this->searchTerms, $this->getFilterList(), $this->page, $this->limit,
            $finalSort, $this->_fullFacetSettings, $returnIndexErrors
        );
        if (PEAR::isError($this->_indexResult)) {
            PEAR::raiseError($this->_indexResult);
        }

        // Save spelling details if they exist.
        if ($this->spellcheck) {
            $this->_processSpelling();
        }

        // Get time after the query
        $this->stopQueryTimer();

        // Store relevant details from the search results:
        $this->resultsTotal = $this->_indexResult['recordCount'];

        // If extra processing is needed for recommendations, do it now:
        if ($recommendations && is_array($this->recommend)) {
            foreach ($this->recommend as $currentSet) {
                foreach ($currentSet as $current) {
                    $current->process();
                }
            }
        }

        // Send back all the details:
        return $this->_indexResult;
    }

    /**
     * Get error message from index response, if any.  This will only work if
     * processSearch was called with $returnIndexErrors set to true!
     *
     * @return mixed false if no error, error string otherwise.
     * @access public
     */
    public function getIndexError()
    {
        return isset($this->_indexResult['errors']) ?
            $this->_indexResult['errors'] : false;
    }

    /**
     * Get database recommendations from MetaLib, if any.
     *
     * @return mixed false if no recommendations, detailed array otherwise.
     * @access public
     */
    public function getDatabaseRecommendations()
    {
        return isset($this->_indexResult['recommendationLists']['database']) ?
            $this->_indexResult['recommendationLists']['database'] : false;
    }

    /**
     * Build a url for the current search
     *
     * @return string URL of a search
     * @access public
     */
    public function renderSearchUrl()
    {
        $result = parent::renderSearchUrl();
        $result .= '&set=' . urlencode($this->_set);
        return $result;
    }
    
    /**
     * Generate a URL for a basic MetaLib "all fields" search for a specific query.
     *
     * @param string $lookfor The search query.
     *
     * @return string         The search URL.
     * @access private
     */
    private function _renderBasicMetaLibSearch($lookfor)
    {
        // Save original settings:
        $oldType = $this->searchType;
        $oldTerms = $this->searchTerms;
        $oldPage = $this->page;

        // Create a basic search:
        $this->page = 1;
        $this->searchType = $this->basicSearchType;
        $this->searchTerms = array(array('lookfor' => $lookfor));
        $url = $this->renderSearchUrl();

        // Restore original settings:
        $this->page = $oldPage;
        $this->searchTerms = $oldTerms;
        $this->searchType = $oldType;

        // Send back generated URL:
        return $url;
    }

    /**
     * Turn the list of spelling suggestions into an array of urls
     *   for on-screen use to implement the suggestions.
     *
     * @return array Spelling suggestion data arrays
     * @access public
     */
    public function getSpellingSuggestions()
    {
        $returnArray = array();

        foreach ($this->suggestions as $term => $details) {
            foreach ($details['suggestions'] as $word) {
                // Strip escaped characters in the search term (for example, "\:")
                $term = stripcslashes($term);
                $word = stripcslashes($word);
                $returnArray[$term]['suggestions'][$word] = array(
                    'replace_url' => $this->_renderBasicMetaLibSearch($word)
                );
            }
        }
        return $returnArray;
    }

    /**
     * Load all available facet settings.  This is mainly useful for showing
     * appropriate labels when an existing search has multiple filters associated
     * with it.
     *
     * @param string $preferredSection Section to favor when loading settings; if
     * multiple sections contain the same facet, this section's description will be
     * favored.
     *
     * @return void
     * @access public
     */
    public function activateAllFacets($preferredSection = false)
    {
        // All MetaLib facets are loaded through recommendations modules; we can
        // activate the settings by starting up recommendations.  This is not a
        // very elegant solution to the problem, and we should probably revisit
        // this in the future.
        $this->initRecommendations();
    }

    /**
     * Get a user-friendly string to describe the provided facet field.
     *
     * @param string $field Facet field name.
     *
     * @return string       Human-readable description of field.
     * @access public
     */
    public function getFacetLabel($field)
    {
        // The default use of "Other" for undefined facets doesn't work well with
        // checkbox facets -- we'll use field names as the default within the MetaLib
        // search object.
        return isset($this->facetConfig[$field]) ?
            $this->facetConfig[$field] : $field;
    }

    /**
     * Get information on the current state of the boolean checkbox facets.
     *
     * @return array
     * @access public
     */
    public function getCheckboxFacets()
    {
        // Grab checkbox facet details using the standard method:
        $facets = parent::getCheckboxFacets();

        // Special case -- if we have a "holdings only" facet, we want this to
        // always appear, even on the "no results" screen, since setting this
        // facet actually EXPANDS the result set, rather than reducing it:
        if (isset($facets['holdingsOnly'])) {
            $facets['holdingsOnly']['alwaysVisible'] = true;
        }

        // Return modified list:
        return $facets;
    }
    
    /**
     * Use the record driver to build an HTML display from the search
     * result suitable for use on a user's "favorites" page.
     *
     * @param array  $record    Record data.
     * @param object $user      User object owning tag/note metadata.
     * @param int    $list      ID of list containing desired tags/notes (or
     * null to show tags/notes from all user's lists).
     * @param bool   $allowEdit Should we display edit controls?
     *
     * @return string           HTML chunk for individual records.
     * @access public
     */
    public function getResultHTML($record, $user, $listId = null, $allowEdit = true)
    {
        global $interface;
    
        $interface->assign(array('record' => $record));
        return $interface->fetch('MetaLib/listentry.tpl');
    }
    
    /**
     * Get information regarding the IRD
     * 
     * @param string $ird  IRD ID
     * 
     * @return array       Array with e.g. 'name' and 'access'
     * @access public
     */
    public function getIRDInfo($ird)
    {
        return $this->_metaLib->getIRDInfo($ird);
    }
}

