<?php
/**
 * Common AJAX functions for the Recommender Visualisation module using JSON as
 * output format.
 *
 * PHP version 5
 *
 * Copyright (C) Till Kinstler 2011.
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
 * @author   Till Kinstler <kinstler@gbv.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */

require_once 'JSON.php';
require_once 'RecordDrivers/Factory.php';

/**
 * Common AJAX functions for the Recommender Visualisation module using JSON as
 * output format.
 *
 * @category VuFind
 * @package  Controller_AJAX
 * @author   Till Kinstler <kinstler@gbv.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */
class JSON_Vis extends JSON
{
    private $_searchObject;
    private $_dateFacets = array();

    /**
     * Constructor.
     *
     * @access public
     */
    public function __construct()
    {
        global $action;
        parent::__construct();
        if (isset($_REQUEST['collection'])) {
            $this->_searchObject
                = SearchObjectFactory::initSearchObject('SolrCollection');
            $action = isset($_REQUEST['collectionAction'])
                ? $_REQUEST['collectionAction']: 'Home';
        } else {
            $this->_searchObject = SearchObjectFactory::initSearchObject();
        }
        // Load the desired facet information...
        $config = getExtraConfigArray('facets');
        if (isset($config['SpecialFacets']['dateVis'])) {
            $this->_dateFacets = is_array($config['SpecialFacets']['dateVis'])
                ? $config['SpecialFacets']['dateVis']
                : array($config['SpecialFacets']['dateVis']);
        }
    }

    /**
     * Get data and output in JSON
     *
     * @param array $fields Fields to process
     *
     * @return void
     * @access public
     */
    public function getVisData($fields = array('publishDate'))
    {
        global $interface;

        if (is_a($this->_searchObject, 'SearchObject_Solr')) {
            if (isset($_REQUEST['collection'])) {
                //ID of the collection
                $collection = $_REQUEST['collection'];

                // Retrieve the record for this collection from the index
                $this->db = ConnectionManager::connectToIndex();
                if (!($record = $this->db->getRecord($collection))) {
                    PEAR::raiseError(new PEAR_Error('Record Does Not Exist'));
                }
                $this->recordDriver = RecordDriverFactory::initRecordDriver($record);

                //get the collection identefier for this record
                $this->collectionIdentifier
                    = $this->recordDriver->getCollectionRecordIdentifier();
                $this->_searchObject->setCollectionField(
                    $this->collectionIdentifier
                );

                // Set the searchobjects collection id to the collection id
                $this->_searchObject->collectionID($collection);

            }
            $this->_searchObject->init();
            $filters = $this->_searchObject->getFilters();
            $fields = $this->_processDateFacets($filters);
            $facets = $this->_processFacetValues($fields);
            foreach ($fields as $field => $val) {
                $facets[$field]['min'] = $val[0] > 0 ? $val[0] : 0;
                $facets[$field]['max'] = $val[1] > 0 ? $val[1] : 0;
                $facets[$field]['removalURL']
                    = $this->_searchObject->renderLinkWithoutFilter(
                        isset($filters[$field][0])
                        ? $field .':' . $filters[$field][0] : null
                    );
                if (isset($_REQUEST['collection'])) {
                    $collection = $_REQUEST['collection'];
                    $facets[$field]['removalURL']
                        = str_replace(
                            'Search/Results',
                            'Collection/' . $collection .
                            '/' .$_REQUEST['collectionAction'],
                            $facets[$field]['removalURL']
                        );
                }
            }
            $this->output($facets, JSON::STATUS_OK);
        } else {
            $this->output("", JSON::STATUS_ERROR);
        }
    }

    /**
     * Support method for getVisData() -- filter bad values from facet lists.
     *
     * @param array $fields Processed date information from _processDateFacets()
     *
     * @return array
     * @access private
     */
    private function _processFacetValues($fields)
    {
        $this->_searchObject->setFacetSortOrder('index');
        $facets = $this->_searchObject->getFullFieldFacets(array_keys($fields));
        $retVal = array();
        foreach ($facets as $field => $values) {
            $newValues = array('data' => array());
            foreach ($values['data'] as $current) {
                // Only retain numeric values!
                if (preg_match("/^[0-9]+$/", $current[0])) {
                    $newValues['data'][] = $current;
                }
            }
            $retVal[$field] = $newValues;
        }
        return $retVal;
    }

    /**
     * Support method for getVisData() -- extract details from applied filters.
     *
     * @param array $filters Current filter list
     *
     * @return array
     * @access private
     */
    private function _processDateFacets($filters)
    {
        $result = array();
        foreach ($this->_dateFacets as $current) {
            $from = $to = '';
            if (isset($filters[$current])) {
                foreach ($filters[$current] as $filter) {
                    if ($range = VuFindSolrUtils::parseRange($filter)) {
                        $from = $range['from'] == '*' ? '' : $range['from'];
                        $to = $range['to'] == '*' ? '' : $range['to'];
                        break;
                    }
                }
            }
            $result[$current] = array($from, $to);
        }
        return $result;
    }
}
?>
