<?php
/**
 * Hierarchy interface.
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
 * @package  Hierarchy
 * @author   Luke O'Sullivan <l.osullivan@swansea.ac.uk>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_search_object Wiki
 */


/**
 * Hierarchy interface class.
 *
 * Interface Hierarchy based drivers.
 * This should be extended to implement functionality for specific
 * Hierarchy Systems (i.e. Calm etc.).
 *
 * @category VuFind
 * @package  Hierarchy
 * @author   Luke O'Sullivan <l.osullivan@swansea.ac.uk>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_search_object Wiki
 */
abstract class Hierarchy
{
    /**
    * showTree()
    * Whether or not to show the tree
    *
    * @return string
    *
    * @access abstract
    */
    abstract function showTree();

    /**
    * getTreeSource()
    * Returns the Source of the Tree
    *
    * @return string
    *
    * @access abstract
    */
    abstract function getTreeSource();

    /**
    * getTreeGenerator()
    * Returns the Tree Generator
    *
    * @return string
    *
    * @access abstract
    */
    abstract function getTreeGenerator();

    /**
    * getTreeSettings()
    * Get settings associated with displaying the tree
    *
    * @return string
    *
    * @access abstract
    */
    abstract function getTreeSettings();

    /**
    * Is Valid Id
    * Checks if a given ID is valid
    *
    * @param string $id A record ID
    *
    * @return boolean true on success, false on failure
    * @access public
    */
    public function isValidId($id)
    {
        return true;
    }

    /**
    * hasHolding
    * Determines if a particular record has holdings
    *
    * @param string $id     A record ID
    * @param string $patron A patron ID
    *
    * @return boolean true if a record has holdings, false if it does not
    * @access public
    */
    public function hasHolding($id, $patron)
    {
        return false;
    }

    /**
    * Get Holding
    * Gets a record's holdings
    *
    * @param string $id A record ID
    *
    * @return array
    * @access public
    */
    public function getHolding($id)
    {
        return array();
    }

    /**
     * Get Statuses
     *
     * This is responsible for retrieving the status information for a
     * collection of records.
     *
     * @param array $ids The array of record ids to retrieve the status for
     *
     * @return mixed     An array of getStatus() return values on success,
     * An empty array on failure.
     * @access public
     */
    public function getStatuses($ids)
    {
        $status = array();
        foreach ($ids as $id) {
            $status[] = $this->getStatus($id);
        }
        return $status;
    }

    /**
     * Get Status
     *
     * This is responsible for retrieving the status information of a record
     *
     * @param string $id A record ID
     *
     * @return mixed     On success, an associative array with the following keys:
     * id, availability (boolean), status, location, reserve, callnumber;
     *
     * @access public
     */
    public function getStatus($id)
    {
        return array();
    }

    /**
    * Get Holding
    * Gets a record's holdings
    *
    * @param string $id A record ID
    *
    * @return array
    * @access public
    */
    public function getPurchaseHistory($id)
    {
        return array();
    }
}
?>