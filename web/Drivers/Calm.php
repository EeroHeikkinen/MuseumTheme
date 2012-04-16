<?php
/**
 * Calm Collections Driver
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
 * @package  Collection_Drivers
 * @author   Luke O'Sullivan <l.osullivan@swansea.ac.uk>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_an_ils_driver Wiki
 */

/**
 * Calm Collection Driver
 *
 * @category VuFind
 * @package  Collection_Drivers
 * @author   Luke O'Sullivan <l.osullivan@swansea.ac.uk>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_an_ils_driver Wiki
 */
class Calm
{
    protected $config;
    protected $status;

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
            $this->config = parse_ini_file('conf/Calm.ini', true);
        }
        // Mimics ILS Drivers
        $this->status = true;
    }

    /**
     * getCollections
     * Returns a keyed array of collections
     *
     * @return array $collections A keyed array of collection data
     * @access public
     */
    public function getCollections()
    {
        $collections = array();
        foreach ($this->config['Collections']['collection'] as $collection) {
            $collections[$collection] = array(
                "id" =>$collection,
                "title" => $this->config[$collection]['title']
            );
        }
        return $collections;
    }

    /**
     * getCollectionByID
     * Returns an array of collection data
     *
     * @param string $id A collection ID
     *
     * @return array A keyed array of collection data
     * @access public
     */
    public function getCollectionByID($id)
    {
        return $this->config[$id];
    }

    /**
     * getCollectionFromRecordID
     * Returns the Collection Data for a particular Record
     *
     * @param string $recordID A Record ID
     *
     * @return  An Archival Tree ID
     * @access public
     */
    public function getCollectionFromRecordID($recordID)
    {
        $getCollectionID = explode("-", $recordID);
        $collectionID = $getCollectionID[0];
        return $this->getCollectionByID($collectionID);
    }

    /**
    * hasArchivalTree
    * Determines if a particular record has an archival tree
    *
    * @param string $recordID A record ID
    *
    * @return boolean true if a tree exists, false if it does not
    * @access public
    */
    public function hasArchivalTree($recordID)
    {
        $collectionDetails = $this->getCollectionFromRecordID($recordID);
        $collectionID = $collectionDetails['id'];
        $collections = $this->getCollections();
        if (array_key_exists($collectionID, $collections)) {
            return true;
        }
        return false;
    }

    /**
    * hasHoldings
    * Determines if a particular record has holdings
    *
    * @param string $id     A record ID
    * @param string $patron A patron ID
    *
    * @return boolean true if a record has holdings, false if it does not
    * @access public
    */
    public function hasHoldings($id, $patron)
    {
        return false;
    }
}

?>
