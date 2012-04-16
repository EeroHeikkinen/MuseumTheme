<?php
/**
 * Hierarchy Tree Generator
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
 * @package  HierarchyTreeGenerator
 * @author   Luke O'Sullivan <l.osullivan@swansea.ac.uk>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_an_authentication_handler Wiki
 */

/**
 * Hierarchy Tree Generator
 *
 * This is a base helper class for producing hierarchy Trees.
 *
 * @category VuFind
 * @package  HierarchyTreeGenerator
 * @author   Luke O'Sullivan <l.osullivan@swansea.ac.uk>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_an_authentication_handler Wiki
 */
class HierarchyTreeGenerator
{
    /**
     * Constructor. Loads the record Driver.
     *
     * @param object $recordDriver A Record Driver Object
     *
     * @access public
     */
    public function __construct($recordDriver)
    {
        $this->recordDriver = $recordDriver;
        $this->_db = ConnectionManager::connectToIndex();
    }

    /**
     * Has Hierarchy Tree
     *
     * @param string $source      The source of the Hierarchy Tree
     * @param string $hierarchyID The hierarchy ID to check for (optional)
     *
     * @return bool false
     * @access public
     */
    public function hasHierarchyTree($source, $hierarchyID = false)
    {
        return false;
    }

    /**
     * Get Hierarchy Tree
     *
     * @param string $source      The source of the Hierarchy Tree
     * @param string $context     The context from which the call has been made
     * @param string $mode        The mode in which the tree should be generated
     * @param string $hierarchyID The hierarchy ID of the tree to fetch (optional)
     * @param string $recordID    The current record ID (optional)
     *
     * @return bool false
     * @access public
     */
    public function getHierarchyTree(
        $source, $context, $mode, $hierarchyID = false, $recordID = false
    ) {
        return false;
    }
    
    /**
     * Generates the xml for this tree
     */
    public function generateXMLfromSolr($hierarchyTopID){
    	$this->getXMLFromSolr($hierarchyTopID, true);
    	return true;
    }
    
    /**
    * Get XML From Solr
    *
    * Build the XML file from the Solr fields
    *
    * TODO: this should return false if it fails.
    *
    * @param string $hierarchyTopID hierarchy_top_id form Solr
    *
    * @return string The XML
    * @access protected
    */
    protected function getXMLFromSolr($hierarchyTopID, $createNew = false)
    {
    	global $configArray;
        $top = $this->_db->getRecord($hierarchyTopID);
        $topRecord = RecordDriverFactory::initRecordDriver($top);
        $cacheFile = $configArray['Site']['local'] . '/interface/cache/hierarchyTree_' .
            urlencode($hierarchyTopID) . '.xml';
            
        //check if create new is set, if so don't bother looking up the config
        if(!$createNew){
        	$cacheTime = $this->recordDriver->getTreeCacheTime();
        }
        else {
            error_log("Create new");
        	$cacheTime = -1;
        }
        
        if (file_exists($cacheFile )
            && filemtime($cacheFile) > (time() - $cacheTime )
        ) {
            error_log("Using cached data from $cacheFile");
            $xml = file_get_contents($cacheFile);
        } else {
            $starttime = microtime(true);
            $count = 0;
            $isHierarchyId = $topRecord->getIsHierarchy() ? "true" : "false";
            $xml = '<root><item id="' .
                $this->xmlencode(htmlentities($hierarchyTopID)) .
                '" isHierarchy="' . $isHierarchyId . '">' .
                '<content><name>' . $this->xmlencode($top['title']) .
                '</name></content>';
            $xml .= $this->getChildren($hierarchyTopID, &$count);
            $xml .= '</item></root>';
            file_put_contents($cacheFile, $xml);
            error_log(
                "Hierarchy of $count records built in " .
                abs(microtime(true) - $starttime)
            );
        }
        return $xml;
    }

    /**
     * Get Solr Children
     *
     * @param string $parentID The starting point for the current recursion
     * (equivlent to Solr field hierarchy_parent_id)
     * @param string $count    The total count of items in the tree
     * before this recursion
     *
     * @return bool false
     * @access public
     */
    protected function getChildren($parentID, $count)
    {
        $query = 'hierarchy_parent_id:"' . addcslashes($parentID, '"') . '"';
        $results = $this->_db->search($query, null, null, 0, 10000);
        if ($results === false) {
            return '';
        }
        $xml = array();
        $sorting = $this->recordDriver->treeSorting();

        foreach ($results['response']['docs'] as $doc) {
            ++$count;
            if ($sorting) {
                foreach ($doc['hierarchy_parent_id'] as $key => $val) {
                    if ($val == $parentID && isset($doc['hierarchy_sequence'])) {
                        $sequence = $doc['hierarchy_sequence'][$key];
                    }
                }
            }

            $topRecord = RecordDriverFactory::initRecordDriver($doc);
            error_log("$parentID: " . $doc['id']);
            $xmlNode = '';
            $isHierarchyId = $topRecord->getIsHierarchy() ? "true" : "false";
            $xmlNode .= '<item id="' . $this->xmlencode($doc['id']) .
                '" isHierarchy="' . $isHierarchyId . '"><content><name>' .
                $this->xmlencode($doc['title_full']) . '</name></content>';
            $xmlNode .= $this->getChildren($doc['id'], &$count);
            $xmlNode .= '</item>';
            array_push($xml, array((isset($sequence)?$sequence: 0),$xmlNode));
        }

        if ($sorting) {
            $this->sortNodes(&$xml, 0);
        }

        $xmlReturnString = '';
        foreach ($xml as $node) {
            $xmlReturnString .= $node[1];
        }
        return $xmlReturnString;
    }

    /**
     * Sort Nodes
     *
     * @param array  &$array The Array to Sort
     * @param string $key    The key to sort on
     *
     * @return void
     * @access public
     */
    function sortNodes (&$array, $key)
    {
        $sorter=array();
        $ret=array();
        reset($array);
        foreach ($array as $ii => $va) {
            $sorter[$ii]=$va[$key];
        }
        asort($sorter);
        foreach ($sorter as $ii => $va) {
            $ret[$ii]=$array[$ii];
        }
        $array=$ret;
    }


    /**
     * XML Encode
     *
     * @param string $str The xml string to encode
     *
     * @return string $str The encoded xml string
     * @access public
     */
    protected function xmlencode($str)
    {
        $str = str_replace('&', '&amp;', $str);
        $str = str_replace('"', '&quot;', $str);
        $str = str_replace('<', '&lt;', $str);
        $str = str_replace('>', '&gt;', $str);
        return $str;
    }

    /**
     * Get Hierarchy Name
     *
     * @param string $hierarchyID        The hierarchy ID to find the title for
     * @param string $inHierarchies      An array of hierarchy IDs
     * @param string $inHierarchiesTitle An array of hierarchy Titles
     *
     * @return string A hierarchy title
     * @access public
     */
    public function getHierarchyName(
        $hierarchyID, $inHierarchies, $inHierarchiesTitle
    ) {
        $keys = array_flip($inHierarchies);
        $key = $keys[$hierarchyID];
        return $inHierarchiesTitle[$key];
    }
}

?>