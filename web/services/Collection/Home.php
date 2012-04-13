<?php
/**
 * Home action for Collection module
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
 * @package  Controller_Record
 * @author   Lutz Biedinger <lutz.biedinger@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */
require_once 'Collection.php';
require_once 'CollectionList.php';
require_once 'CollectionMap.php';
require_once 'HierarchyTree.php';

/**
 * Home action for Collection module
 *
 * @category VuFind
 * @package  Controller_Record
 * @author   Lutz Biedinger <lutz.biedinger@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */
class Home extends Collection
{
    /**
     * Process incoming parameters and display the page.
     *
     * @return void
     * @access public
     */
    public function launch()
    {
        global $configArray;
        global $interface;
        global $action;


        if (is_array($this->collections) && count($this->collections) >= 2) {
            $this->showDisambiguation();
        } elseif (is_array($this->collections) && count($this->collections) == 1) {
            $this->showDefaultTab();
        } elseif ($action == "Home") {
        	$browseType = (isset($configArray['Collections']['browseType'])) ?
                $configArray['Collections']['browseType'] : 'Index';
            if( $browseType == 'Index'){
            	$this->showBrowseIndex();
            }
            elseif($browseType == 'Alphabetic'){
            	$this->showBrowseAlphabetic();
            }
        	else{
            	$this->showBrowseIndex();
            }

        } else {
            Header(
                "Location:" .
                $configArray['Site']['url'] .
                '/Search/Results?lookfor=&type=AllFields&filter[]=in_collection%3A"'
                . $_REQUEST['collection'] . '"&collection=true'
            );
        }
    }

    /**
     * Show the default
     *
     * @return void
     * @access public
     */
    public function showDefaultTab()
    {
        global $configArray;

        // Choose Default Tab
        $serviceName = (isset($configArray['Collections']['defaultTab'])) ?
                $configArray['Collections']['defaultTab'] : 'CollectionList';

        if ($serviceName == "HierarchyTree" && !$this->hasHierarchyTree) {
            $serviceName = 'CollectionList';
        }

        $service = new $serviceName();
        $service->recordHit();
        $service->launch();
    }

    /**
     * Show the disambiguation details
     *
     * @return void
     * @access public
     */
    public function showDisambiguation()
    {
        global $interface;

        // need to show disambiguation page
        // pass the collections to the interface, along with some of
        // its fields for easier access
        $interface->assign('collections', $collections);
        $interface->setTemplate('disambiguation.tpl');
        $interface->display('layout.tpl');
    }

	/**
     * Show the Browse Menu
     *
     * @return void
     * @access public
     */
    public function showBrowseAlphabetic()
    {
        global $configArray;
        global $interface;
        // Process incoming parameters:
        $source = "hierarchy";
        $from = isset($_GET['from']) ? $_GET['from'] : '';
        $page = (isset($_GET['page']) && is_numeric($_GET['page']))
            ? $_GET['page'] : 0;
        $view = isset($configArray['Collections']['browseView'])
            ? $configArray['Collections']['browseView'] : 'List';
        $limit = isset($configArray['Collections']['browseLimit'])
            ? $configArray['Collections']['browseLimit'] : 20;

        // If required parameters are present, load results:
        if ($source && $from !== false) {
            // Load Solr data or die trying:
            $result = $this->db->alphabeticBrowse(
                $source, $from, $page, $limit, true
            );
            $this->checkError($result);

            // No results?  Try the previous page just in case we've gone past the
            // end of the list....
            if ($result['Browse']['totalCount'] == 0) {
                $page--;
                $result = $this->db->alphabeticBrowse(
                    $source, $from, $page, $limit, true
                );
                $this->checkError($result);
            }

            // Only display next/previous page links when applicable:
            if ($result['Browse']['totalCount'] > $limit) {
                $interface->assign('nextpage', $page + 1);
            }
            if ($result['Browse']['offset'] + $result['Browse']['startRow'] > 1) {
                $interface->assign('prevpage', $page - 1);
            }

            // Send other relevant values to the template:
            $interface->assign('source', $source);
            $interface->assign('from', $from);
            //select only the items to send to the template
            $interface->assign('result', $result['Browse']['items']);
        }

        $legalLetters = $this->getAlphabetList();
        $interface->assign('letters', $legalLetters);

        // Display the page:
        $interface->assign('browseView', 'Collection/browse'. $view . '.tpl');
        $interface->setPageTitle('Browse the Collection Alphabetically');
        $interface->setTemplate('browse.tpl');
        $interface->display('layout.tpl');
    }

    /**
     * Show the Browse Menu
     *
     * @return void
     * @access public
     */
    public function showBrowseIndex()
    {
        global $configArray;
        global $interface;
        // Process incoming parameters:
        $from = strtolower(isset($_GET['from']) ? $_GET['from'] : '');
        $page = (isset($_GET['page']) && is_numeric($_GET['page']))
            ? $_GET['page'] : 0;
        $appliedFilters = isset($_GET['filter'])? $_GET['filter'] : array();
        $view = isset($configArray['Collections']['browseView'])
            ? $configArray['Collections']['browseView'] : 'List';
        $limit = isset($configArray['Collections']['browseLimit'])
            ? $configArray['Collections']['browseLimit'] : 20;

        // If required parameters are present, load results:
        if ($from !== false) {
        	$browseField = "hierarchy_browse";

        	$this->_searchObject = SearchObjectFactory::initSearchObject();
        	$this->_searchObject->init();
        	foreach($appliedFilters as $filter){
				$this->_searchObject->addFilter($filter);
        	}

        	$result = $this->_searchObject->getFacetsForBrowsing(array($browseField));
        	$result = $result[$browseField]['data'];

        	//sort the $results and get the position of the from string once sorted
        	$key = $this->sortFindKeyLocation(&$result, $from);

        	//offset the key by how many pages in we are
        	$key = $key + ($limit * $page);

        	//Only display next/previous page links when applicable:
            if (count($result) > $key + $limit) {
                $interface->assign('nextpage', $page + 1);
            }
            if ($key > 0) {
                $interface->assign('prevpage', $page - 1);
            }

            //catch out of range keys
        	if ($key < 0) {
                $key = 0;
            }
        	if ($key >= count($result)) {
                $key = count($result)-1;
            }

            //select just the records to display
        	$result = array_slice( $result, $key,
        		count($result) > $key + $limit ? $limit: null);

            // Send other relevant values to the template:
            $interface->assign('from', $from);
            $interface->assign('result', $result);

            //because the searchobject returns removal urls for the search module
            //we have to cheat a little bit and do some string manipulation to the
            //removal urls
            $filters = $this->_searchObject->getFilterList(true);
            if (isset($filters) && isset($filters['Other'])){
            	$filtersString = "";
            	foreach ($filters['Other'] as $filterK =>$filter){
            		$filters['Other'][$filterK]['removalUrl'] = str_ireplace(
            			'Search/Results?lookfor=&type=AllFields',
            			'Collection/Home?from=' . $from . '&page=' .
            			$page, $filter['removalUrl']);
            		$filtersString .= "&".urlencode("filter[]") . '=' .
                        urlencode($filter['field'] . ":\"" . $filter['value'] . "\"");
            	}
            	$interface->assign('filtersString', $filtersString);
            	$interface->assign('filterList', $filters);
            }
        }

        $legalLetters = $this->getAlphabetList();
        $interface->assign('letters', $legalLetters);

        // Display the page:
        $interface->assign('browseView', 'Collection/browse'. $view . '.tpl');
        $interface->setPageTitle('Browse the Collection Alphabetically');
        $interface->setTemplate('browse.tpl');
        $interface->display('layout.tpl');
    }

	/**
     *
     * Fucntion to Sort the from array and find the position of the from
     * value in the result set, if the value doesn't exist it's inserted.
     *
     * @param array $array
     * @param string $from
     *
     * @return int $key
     */
    function sortFindKeyLocation(&$result, $from){
    	//normalize the from value so it matches the values we are looking up
    	$from = $this->normalizeForBrowse($from);

    	//key set to 0 at start
    	$key = 0;

    	//sort the values into $facetsNormalized
    	$facetsNormalizedSorted = $this->normalizeAndSortFacets(&$result);

    	//define the from regex to match
    	$regExForFrom = '/^' . preg_quote($from) . '.*/';
    	$foundMatch = false;

        $i = 0;
        foreach($facetsNormalizedSorted as $resVals){
        	if(preg_match($regExForFrom, $resVals)){
        		$key = $i;
        		$foundMatch = true;
        		break;
        	}
        	$i++;
        }
        if (!$foundMatch){
        	//try to insert the value, resort the array, and find the place where
        	//it would be

        	//unset facetsNormalizedSorted before it's reassigned
        	unset($facetsNormalizedSorted);

        	//add from and sort
        	array_push($result, array($from, 0));//giving count of zero to highlight
        	$facetsNormalizedSorted = $this->normalizeAndSortFacets(&$result);

        	$i = 0;
        	foreach($facetsNormalizedSorted as $resVals){
        		if(preg_match($regExForFrom, $resVals)){
        			$key = $i;
        			$foundMatch = true;
        			break;
        		}
        		$i++;
        	}
        }

        //declare array to hold the $result array in the right sort order
        $sorted=array();
    	$i = 0;
    	foreach ($facetsNormalizedSorted as $ii => $va) {
    	    $sorted[$i]=$result[$ii];
    	    unset($result[$ii]);//clear this out of memory
    	    $i++;
    	}
    	unset($facetsNormalizedSorted);
    	$result = $sorted;

    	return $key;
    }

    /**
     *
     * Function to normalize the names so they sort properly
     *
     * @param array $result passed by refference to use less memory
     *
     * @return array $resultOut
     */
    function normalizeAndSortFacets(&$result){
    	$valuesSorted= array();
    	foreach($result as $resKey => $resVal){
    		$valuesSorted[$resKey] = $this->normalizeForBrowse($resVal[0]);
    	}
    	asort($valuesSorted);

    	//now the $valuesSorted is in the right order
    	return $valuesSorted;
    }

    /**
     *
     * Normalize the value for the browse sort
     * @param string $val
     * @return string $valNormalized
     */
    function normalizeForBrowse($val){
    	$valNormalized = $val;
    	//$valNormalized = strtolower($valNormalized);
    	$valNormalized = iconv('UTF-8', 'US-ASCII//TRANSLIT//IGNORE', $valNormalized);
    	$valNormalized = strtolower($valNormalized);
    	$valNormalized = preg_replace("/[^a-zA-Z0-9\s]/", "",$valNormalized);
    	$valNormalized = trim($valNormalized);
    	//$valNormalized = removePrefixes($valNomralized);
    	return $valNormalized;
    }

    /**
     * Get a list of initial letters to display.
     *
     * @return array
     * @access protected
     */
    protected function getAlphabetList()
    {
        return array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
            'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
    }

    /**
     * Given an alphabrowse response, die with an error if necessary.
     *
     * @param array $result Result to check.
     *
     * @return void
     * @access protected
     */
    protected function checkError($result)
    {
        if (isset($result['error'])) {
            // Special case --  missing alphabrowse index probably means the
            // user could use a tip about how to build the index.
            if (strstr($result['error'], 'does not exist')
                || strstr($result['error'], 'no such table')
                || strstr($result['error'], 'couldn\'t find a browse index')
            ) {
                $result['error'] = "Alphabetic Browse index missing.  See " .
                    "http://vufind.org/wiki/alphabetical_heading_browse for " .
                    "details on generating the index.";
            }
            PEAR::raiseError(new PEAR_Error($result['error']));
        }
    }
}

?>
