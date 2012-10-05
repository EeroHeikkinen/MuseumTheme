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
     * 
     * @param string $hierarchyTopID Hierarchy ID 
     * 
     * @return boolean Success
     */
    public function generateXMLfromSolr($hierarchyTopID)
    {
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
    * @param string  $hierarchyTopID hierarchy_top_id form Solr
    * @param boolean $createNew      Bypass cache     
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
            
        // Check if create new is set, if so don't bother looking up the config
        if (!$createNew) {
            $cacheTime = $this->recordDriver->getTreeCacheTime();
        } else {
            $cacheTime = -1;
        }
        
        if (file_exists($cacheFile)
            && filemtime($cacheFile) > (time() - $cacheTime)
        ) {
            $xml = file_get_contents($cacheFile);
        } else {
            $starttime = microtime(true);
            $count = 0;
            $xml = $this->getTree($hierarchyTopID, &$count);
            file_put_contents($cacheFile, $xml);
            error_log(
                "Hierarchy of $count records built in " .
                abs(microtime(true) - $starttime)
            );
        }
        return $xml;
    }

    /**
     * Get the tree from Solr
     *
     * @param string $hierarchyID Hierarchy ID (equivalent to Solr 
     *                            field hierarchy_top_id)
     * @param string &$count      The total count of items in the tree
     *
     * @return string XML
     */
    protected function getTree($hierarchyID, &$count)
    {
        $query = 'hierarchy_top_id:"' . addcslashes($hierarchyID, '"') . '"';
        $results = $this->_db->search($query, null, null, 0, 10000);
        if ($results === false) {
            return '';
        }
        if ($results['response']['numFound'] > 30000) {
            error_log("Hierarchy '$hierarchyID' too large");
            return '<root>' .
                '<item id="' . $this->xmlencode($hierarchyID) .
                '" isHierarchy="true"><content><name>Hierarchy too large</name></content>' .
                '</item></root>';
        }
        $docs = array();
        foreach ($results['response']['docs'] as $doc) {
            $item = array('id' => $doc['id'], 'title_full' => $doc['title_full']);
            if (isset($doc['hierarchy_parent_id']) && !empty($doc['hierarchy_parent_id'])) {
                $item['hierarchy_parent_id'] = $doc['hierarchy_parent_id']; 
            }
            if (isset($doc['is_hierarchy_id']) && !empty($doc['is_hierarchy_id'])) {
                $item['is_hierarchy_id'] = $doc['is_hierarchy_id']; 
            }
            if (isset($doc['hierarchy_sequence'])) {
                $item['hierarchy_sequence'] = $doc['hierarchy_sequence']; 
            }
            $docs[$doc['id']] = $item;
        }
        $tree = array();
        foreach ($docs as &$doc) {
            $id = $doc['id'];
            if (!isset($doc['hierarchy_parent_id'])) {
                $tree[$id] = &$doc;
            } else {
                foreach ($doc['hierarchy_parent_id'] as $parentId) {
                    if (!isset($docs[$parentId]['children'])) {
                        $docs[$parentId]['children'] = array();
                    }
                    $docs[$parentId]['children'][$id] = &$doc;
                }
                ++$count;
            }
        }
        $xml = '<root>';
        $xml .= $this->treeToXML($tree);
        $xml .= '</root>';
        return $xml;
    }
    
    /**
     * Convert an array tree to XML
     * 
     * @param array $tree Nested array
     * 
     * @return string XML
     */
    protected function treeToXML($tree)
    {
        $idx = 0;
        $xml = array();
        $sorting = $this->recordDriver->treeSorting();
        foreach ($tree as $doc) {
            if (isset($doc['hierarchy_parent_id']) && $sorting) {
                foreach ($doc['hierarchy_parent_id'] as $key => $val) {
                    if ($val == isset($doc['hierarchy_parent_id']) && isset($doc['hierarchy_sequence'])) {
                        $sequence = $doc['hierarchy_sequence'][$key];
                    }
                }
            }
            if (!isset($sequence)) {
                $sequence = $doc['id'];
            }
            $isHierarchy = isset($doc['is_hierarchy_id']) ? 'true' : 'false';
            $xmlNode = '';
            $xmlNode .= '<item id="' . $this->xmlencode($doc['id']) .
                '" isHierarchy="' . $isHierarchy . '"><content><name>' .
                $this->xmlencode($doc['title_full']) . '</name></content>';
            if (isset($doc['children'])) {
                $xmlNode .= $this->treeToXML($doc['children']);
            }
            $xmlNode .= '</item>';
            ++$idx;
            $xml[$sequence . '-' . $idx] = $xmlNode; 
        }

        ksort($xml);

        $xmlReturnString = '';
        foreach ($xml as $node) {
            $xmlReturnString .= $node;
        }
        return $xmlReturnString;
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
