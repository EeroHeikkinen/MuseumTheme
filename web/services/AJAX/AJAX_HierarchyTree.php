<?php
/**
 * Ajax page for Hierarchy Tree
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
 * @package  Controller_AJAX
 * @author   Luke O'Sullivan <l.osullivan@swansea.ac.uk>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */
require_once 'Action.php';
/**
 * Home action for Record module
 *
 * @category VuFind
 * @package  Controller_Record
 * @author   Luke O'Sullivan <l.osullivan@swansea.ac.uk>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */
class AJAX_HierarchyTree extends Action
{

    /**
     * Constructor.
     *
     * @access public
     */
    public function __construct()
    {
        parent::__construct();
        // Setup Search Engine Connection
        $this->db = ConnectionManager::connectToIndex();
    }

    /**
     * Process parameters and display the response.
     *
     * @return void
     * @access public
     */
    public function launch()
    {
        // Call the method specified by the 'method' parameter as long as it is
        // valid and will not result in an infinite loop!
        if ($_GET['method'] != 'launch'
            && $_GET['method'] != '__construct'
            && is_callable(array($this, $_GET['method']))
        ) {
            $this->$_GET['method']();
        } else {
            // Error
        }
    }

    /**
     * Output XML
     *
     * @param string $xml An xml string
     *
     * @return void
     * @access public
     */
    public function output($xml)
    {
        header("Content-Type:text/xml");
        echo $xml;
    }

    /**
     * Output JSON
     *
     * @param string $json A JSON string
     *
     * @return void
     * @access public
     */
    public function outputJSON($json)
    {
        header("Content-Type: application/json");
        echo $json;
    }

    /**
     * Search the tree and echo a json result of items that
     * matched the keywords.
     *
     * @return void
     * @access public
     */
    public function searchTree()
    {
    	global $configArray;
    	
    	$limit = isset($configArray['Hierarchy']['treeSearchLimit'])?
    		$configArray['Hierarchy']['treeSearchLimit']: -1;
        $resultIDs = array();
        $hierarchyID = $_GET['hierarchyID'];
        $lookfor = isset($_GET['lookfor'])? $_GET['lookfor']: "";
        $searchType = isset($_GET['type'])? $_GET['type'] : "AllFields";

        //print_r($results);
        $this->searchObject
            = SearchObjectFactory::initSearchObject();
        // Set the searchobjects collection id to the collection id
        $this->searchObject->init();
        $this->searchObject->addFilter('hierarchy_top_id:' . $hierarchyID);
        $facets = $this->searchObject->getFullFieldFacets(array('id'), false, $limit+1);

        if (isset($facets)) {
            //print_r($ids);
            foreach ($facets['id']['data'] as $id) {
                $resultIDs[] =is_array($id)? $id[0]: $id;
                unset($id);
            }
        }
        if (count($resultIDs) > $limit){
        	$limitReached = true;
        }
        else {
        	$limitReached = false;
        }
        $returnArray = array("limitReached" => $limitReached, "results" => array_slice($resultIDs, 0, $limit));
        $jsonString = json_encode($returnArray);
        $this->outputJSON($jsonString);
    }

    /**
     * Gets a Hierarchy Tree
     *
     * @return void
     * @access public
     */
    public function getHierarchyTree()
    {
        global $interface;
        global $configArray;

        $hierarchyID = $_GET['hierarchyID'];
        $context = $_GET['context'];
        $mode = $_GET['mode'];

        // Retrieve the record from the index
        if ($record = $this->db->getRecord($_GET['id'])) {
            $recordDriver = RecordDriverFactory::initRecordDriver($record);
            $results = $recordDriver->getHierarchyTree(
                false, $context, $mode, $hierarchyID
            );
            if (PEAR::isError($results)) {
                $this->output(
                    "<error>" . translate("hierarchy_tree_error") . "</error>"
                );
            } else {
                $this->output($results);
            }
        } else {
            $this->output(
                "<error>" . translate("hierarchy_tree_error") . "</error>"
            );
        }
    }

    /**
     * Outputs HTML for an individual Collection Record
     *
     * @return void
     * @access public
     */
    public function getRecord()
    {
        global $interface;

        // Retrieve the record from the index
        if (!($record = $this->db->getRecord($_GET['id']))) {
            $interface->assign("recordID", $_GET['id']);
            echo $interface->fetch("Collection/collection-record-error.tpl");
        } else {
            $recordDriver = RecordDriverFactory::initRecordDriver($record);
            $collectionRecord = $recordDriver->getCollectionRecord();
            echo $interface->fetch($collectionRecord);
        }
    }
}

?>
