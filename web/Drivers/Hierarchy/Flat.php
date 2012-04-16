<?php
/**
 * Flat Hierarchy Driver
 *
 * PHP version 5
 *
 * Copyright (C) Villanova University 2012.
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
 * @package  Hierarchy_Drivers
 * @author   Lutz Biedinger <lutz.biedinger@gmail.com>
 * @author   Luke O'Sullivan <l.osullivan@swansea.ac.uk>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_an_ils_driver Wiki
 */

require_once 'Drivers/Hierarchy/HierarchyBase.php';

/**
 * Flat Hierarchy Driver; a hierarchy driver for collections without hierarchichal
 * trees.
 *
 * @category VuFind
 * @package  Hierarchy_Drivers
 * @author   Lutz Biedinger <lutz.biedinger@gmail.com>
 * @author   Luke O'Sullivan <l.osullivan@swansea.ac.uk>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_an_ils_driver Wiki
 */
class Hierarchy_Flat extends Hierarchy
{
    protected $config;
    public $status;

    /**
     * Constructor
     *
     * @param string $configFile The location of an alternative config file
     *
     * @access public
     */
    public function __construct($configFile = false)
    {
        if ($configFile) {
            // Load Configuration passed in
            $this->config = parse_ini_file('conf/'.$configFile, true);
        } else {
            // Hard Coded Configuration
            $this->config = parse_ini_file('conf/HierarchyFlat.ini', true);
        }
        // Mimics ILS Drivers
        $this->status = true;
    }

    /**
     * Show Tree
     *
     * Returns the configuration setting for displaying a hierarchy tree
     *
     * @return boolean The boolean value of the configuration setting
     * @access public
     */
    public function showTree()
    {
        return isset($this->config['HierarchyTree']['show'])
            ? $this->config['HierarchyTree']['show'] : false;
    }

    /**
     * Get Tree Generator
     *
     * Returns the configuration setting for generating a hierarchy tree
     *
     * @return string The value of the configuration setting
     * @access public
     */
    public function getTreeGenerator()
    {
        return $this->config['HierarchyTree']['treeGenerator'];
    }

    /**
     * Get Tree Source
     *
     * Returns the configuration setting for hierarchy tree source
     *
     * @return string The value of the configuration setting
     * @access public
     */
    public function getTreeSource()
    {
        return $this->config['HierarchyTree']['treeSource'];
    }

    /**
     * Get Tree Source
     *
     * Returns the configuration setting for hierarchy tree caching time when
     * using solr to build the tree
     *
     * @return int The value of the configuration setting
     * @access public
     */
    public function getTreeCacheTime()
    {
        return isset($this->config['HierarchyTree']['solrCacheTime'])?
         $this->config['HierarchyTree']['solrCacheTime'] : 43200;
    }

    /**
     * Check if sorting is enabled in the hierarchy Options
     *
     * Returns the configuration setting for hierarchy tree sorting
     *
     * @return bool The value of the configuration setting
     * @access public
     */
    public function treeSorting()
    {
        return isset($this->config['HierarchyTree']['sorting'])?
         $this->config['HierarchyTree']['sorting'] : false;
    }

    /**
     * Get Tree Source
     *
     * Returns all the configuration settings for a hierarchy tree
     *
     * @return array The values of the configuration setting
     * @access public
     */
    public function getTreeSettings()
    {
        return $this->config['HierarchyTree'];
    }

    /**
     * Get Status
     *
     * This is responsible for retrieving the status information of a certain
     * record.
     *
     * @param string $id The record id to retrieve the holdings for
     *
     * @return mixed     On success, an associative array with the following keys:
     * id, availability (boolean), status, location, reserve, callnumber; on
     * failure, a PEAR_Error.
     * @access public
     */
    public function getStatus($id)
    {
        $hierarchyData = $this->config['defaultStatus'];
        $hierarchyStatus = array();

        $hierarchyStatus[] = array(
            'id'           => $id,
            'number'       => "",
            'barcode'      => "",
            'availability' => isset($hierarchyData['availability'])
                ? $hierarchyData['availability'] : true,
            'status'       => isset($hierarchyData['status'])
                ? $hierarchyData['status'] : "",
            'location'     => isset($hierarchyData['location'])
                ? $hierarchyData['location'] : "Unknown",
            'reserve'      => 'N',
            'callnumber'   => isset($hierarchyData['callnumber'])
                ? $hierarchyData['callnumber'] : "",
            'duedate'      => '',
            'is_holdable'  => false,
            'addLink'      => false
        );
        return $hierarchyStatus;
    }

    /**
    * Get Collection Identier
    *
    * @return $identifier the setting from the config file
    *
    * @access public
    */
    public function getCollectionIdentifier()
    {
        return isset( $this->config['Collections']['identifier'])?
            $this->config['Collections']['identifier']: "All Containers";
    }
}

?>