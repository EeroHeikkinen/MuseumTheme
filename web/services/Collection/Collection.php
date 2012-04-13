<?php
/**
 * Collection Controller.
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
require_once 'Action.php';
require_once 'sys/Language.php';
require_once 'RecordDrivers/Factory.php';
require_once 'sys/VuFindDate.php';

/**
 * Collection module.
 *
 * @category VuFind
 * @package  Controller_Collection
 * @author   Lutz Biedinger <lutz.biedinger@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */
class Collection extends Action
{
    protected $recordDriver;
    protected $cacheId;
    protected $db;
    protected $catalog;
    protected $errorMsg;
    protected $infoMsg;
    protected $collections;
    protected $id;
    protected $collectionID;
    protected $collectionTitle;
    protected $hasHierarchyTree;

    /**
     * Constructor
     *
     * @access public
     */
    public function __construct()
    {
        global $action;
        global $interface;
        $interface->assign('collectionAction', $action);
        $interface->assign('disableKeepFilterControl', true);
        // Setup Search Engine Connection
        $this->db = ConnectionManager::connectToIndex();
        // Set up object for formatting dates and times:
        $this->dateFormat = new VuFindDate();
        // Assign Collection Values
        $this->collections = $this->getCollections($_REQUEST['collection']);
        $this->init();
    }

    /**
     * Initialise shared settings
     *
     * @return void
     * @access public
     */
    public function init()
    {
        global $interface;
        // Initialise this Collection
        if (is_array($this->collections) && count($this->collections) == 1) {

            $collection = $this->collections[0];
            $this->id = $collection['id'];
            $this->collectionID = $collection['is_hierarchy_id'];
            $this->collectionTitle = $collection['is_hierarchy_title'];

            // Retrieve the record from the index
            if (!($record = $this->db->getRecord($this->id))) {
                PEAR::raiseError(new PEAR_Error('Record Does Not Exist'));
            }
            $this->recordDriver = RecordDriverFactory::initRecordDriver($record);

            $this->hasHierarchyTree
                = $this->recordDriver->hasHierarchyTree(false);

            //get the collection identefier for this record
            $this->collectionIdentifier
                = $this->recordDriver->getCollectionRecordIdentifier();
            // Get the records part of this collection
            $this->searchObject
                = SearchObjectFactory::initSearchObject("SolrCollection");
            // Set the searchobjects collection id to the collection id
            $this->searchObject->collectionID($this->collectionID);
            $this->searchObject->setCollectionField($this->collectionIdentifier);
            $this->searchObject->init();
        }

        // Register Library Catalog Account
        if (isset($_POST['submit']) && !empty($_POST['submit'])) {
            if (isset($_POST['cat_username'])
                && isset($_POST['cat_password'])
            ) {
                $result = UserAccount::processCatalogLogin(
                    $_POST['cat_username'], $_POST['cat_password']
                );
                if ($result) {
                    $interface->assign('user', $user);
                } else {
                    $interface->assign('loginError', 'Invalid Patron Login');
                }
            }
        }

        // Get Messages
        $this->infoMsg = isset($_GET['infoMsg']) ? $_GET['infoMsg'] : false;
        $this->errorMsg = isset($_GET['errorMsg']) ? $_GET['errorMsg'] : false;
        // Set Messages
        $interface->assign('infoMsg', $this->infoMsg);
        $interface->assign('errorMsg', $this->errorMsg);
    }

    /**
     * Set Up Collection Record
     *
     * @return void
     * @access public
     */
    public function assignCollection()
    {
        global $configArray;
        global $interface;

        // Pass collection fields to the  interface
        $interface->assign('id', $this->id);
        $interface->assign('collectionName', urlencode($this->collectionTitle));
        $interface->assign('collectionID', $this->collectionID);
        //$interface->assign('collectionAction', $action);

        if ($this->recordDriver->hasRDF()) {
            $interface->assign(
                'addHeader', '<link rel="alternate" type="application/rdf+xml" ' .
                'title="RDF Representation" href="' . $configArray['Site']['url'] .
                '/Record/' . urlencode($this->id) . '/RDF" />' . "\n"
            );
        }

        //Set the page Title
        $interface->setPageTitle(
            translate('Collection') . ': ' . $this->recordDriver->getBreadcrumb()
        );

        $interface->assign('info', $this->recordDriver->getCollectionMetadata());

        // Set flags that control which tabs are displayed:
        //archival tree
        if (isset($configArray['Content']['showHierarchyTree'])) {
            $interface->assign('hasHierarchyTree', $this->hasHierarchyTree);
        }

        // Retrieve User Search History
        $interface->assign(
            'lastsearch',
            isset($_SESSION['lastSearchURL'])
            ? $_SESSION['lastSearchURL'] : false
        );

        // Send down text for inclusion in breadcrumbs
        $interface->assign('breadcrumbText', $this->recordDriver->getBreadcrumb());

        // Send down OpenURL for COinS use:
        $interface->assign('openURL', $this->recordDriver->getOpenURL());

        // Set AddThis User
        $interface->assign(
            'addThis', isset($configArray['AddThis']['key'])
            ? $configArray['AddThis']['key'] : false
        );

        // Set Default View Template
        $interface->setTemplate('view.tpl');
    }

    /**
     * Set Up Collection Facets
     *
     * @return void
     * @access public
     */
    public function assignCollectionFacets()
    {
        global $interface;
        global $configArray;

        $result = $this->searchObject->processSearch(false, true);
        if (PEAR::isError($result)) {
            PEAR::raiseError($result->getMessage());
        }

        $interface->assign(
            'topRecommendations',
            $this->searchObject->getRecommendationsTemplates('top')
        );
        $interface->assign(
            'sideRecommendations',
            $this->searchObject->getRecommendationsTemplates('side')
        );
        $interface->assign(
            'recordSet', $this->searchObject->getResultRecordHTML()
        );

        // We can get the facets now
        $facetSet = $this->searchObject->getFacetList();
        $searchParams = '?lookfor=';
        $filterList = $this->searchObject->getFilters();
        if (count($filterList) > 0) {
            foreach ($filterList as $field => $filter) {
                if ($field != "hierarchy_top_id") {
                    foreach ($filter as $value) {
                        $searchParams
                            .= "&filter[]=" . urlencode("$field:\"$value\"");
                    }
                }
            }
        }

        $interface->assign('filters', $searchParams);
        $interface->assign('facetSet', $facetSet);

        // Set Proxy URL
        if (isset($configArray['EZproxy']['host'])) {
               $interface->assign('proxy', $configArray['EZproxy']['host']);
        }
    }

    /**
     * get the collection ID, this will return an array
     *
     * @param string $name The Name of the Collection
     *
     * @return array $collections An array of Collection Information
     * @access protected
     */
    protected function getCollections($name)
    {
        $collections = $this->db->getCollectionsFromName($name);
        return $collections;
    }

    /**
     * Gets An individual Collection Record
     *
     * @param string $id The Collection Record ID
     *
     * @return mixed "unknown" if the record doesn't exist, the location of a
     * template file on success
     * @access protected
     */
    public function getRecord($id)
    {
        global $interface;
        // Retrieve the record from the index
        if (!($record = $this->db->getRecord($id))) {
            return "unknown";
        } else {
            $recordDriver = RecordDriverFactory::initRecordDriver($record);
            return $recordDriver->getCollectionRecord();
        }
    }

    /**
     * Record a record hit to the statistics index when stat tracking is enabled;
     * this is called by the Home action.
     *
     * @return void
     * @access public
     */
    public function recordHit()
    {
        global $configArray;

        if ($configArray['Statistics']['enabled']) {
            // Setup Statistics Index Connection
            $solrStats = ConnectionManager::connectToIndex('SolrStats');

            // Save Record View
            $solrStats->saveRecordView($this->recordDriver->getUniqueID());
            unset($solrStats);
        }
    }

}
?>