<?php
/**
 * Marc Hierachy Record Driver
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
 * @package  RecordDrivers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @author   Luke O'Sullivan <l.osullivan@swansea.ac.uk>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/other_than_marc Wiki
 */
require_once 'RecordDrivers/MarcRecord.php';
require_once 'Drivers/Hierarchy/HierarchyFactory.php';

/**
 * Marc Hierachy Record Driver
 *
 * This class is designed to handle Archival Records in a MARC format. Most of its
 * functionality is inherited from the default index-based driver and marc record
 * driver. It's primary purpose is to provide connections to archival record
 * drivers.
 *
 * @category VuFind
 * @package  RecordDrivers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @author   Eoghan O'Carragain <Eoghan.OCarragan@gmail.com>
 * @author   Luke O'Sullivan <l.osullivan@swansea.ac.uk>
 * @author   Lutz Biedinger <lutz.Biedinger@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/other_than_marc Wiki
 */
class MarcHierarchyRecord extends MarcRecord
{

    /**
     * Constructor.  We build the object using all the data retrieved
     * from the (Solr) index (which also happens to include the
     * 'fullrecord' field containing raw metadata).  Since we have to
     * make a search call to find out which record driver to construct,
     * we will already have this data available, so we might as well
     * just pass it into the constructor.
     *
     * @param array $record All fields retrieved from the index.
     *
     * @access public
     */
    public function __construct($record)
    {
         global $configArray;

        // Call the parent's constructor...
        parent::__construct($record);
    }

    /**
     * Get an array of information about record history, obtained in real-time
     * from the Hierarchy Driver.
     *
     * @return array
     * @access protected
     */
    protected function getRealTimeHistory()
    {
        global $configArray;

        // Get Acquisitions Data
        $id = $this->getUniqueID();
        $hierarchyType = $this->getHierarchyType();
        if ($hierarchy = HierarchyFactory::initHierarchy($hierarchyType)) {
            $result = $hierarchy->getPurchaseHistory($id);
            if (PEAR::isError($result)) {
                PEAR::raiseError($result);
            }
            return $result;
        }
        return array();
    }

    /**
     * Get an array of information about record holdings, obtained in real-time
     * from the Hierarchy Driver.
     *
     * @param array $patron An array of patron data
     *
     * @return array
     * @access protected
     */
    protected function getRealTimeHoldings($patron = false)
    {
        global $configArray;

        // Get ID and connect to catalog
        $id = $this->getUniqueID();
        $hierarchyType = $this->getHierarchyType();
        if ($hierarchy = HierarchyFactory::initHierarchy($hierarchyType)) {
            include_once 'sys/HoldLogic.php';
            $holdLogic = new HoldLogic($hierarchy);
            return $holdLogic->getHoldings($id, $patron);
        }
        return false;
    }

    /**
     * Get an array of status Information
     *
     * @return array
     * @access protected
     */
    public function getRealTimeStatus()
    {
        global $configArray;

        // Get ID and connect to catalog
        $id = $this->getUniqueID();
        $hierarchyType = $this->getHierarchyType();
        if ($hierarchy = HierarchyFactory::initHierarchy($hierarchyType)) {
            return $hierarchy->getStatus($id);
        }
        return false;
    }

    /**
     * Check if an item has holdings in order to show or hide the holdings tab
     *
     * @param array $patron An array for patron information
     *
     * @return bool
     * @access public
     */
    public function hasRealTimeHoldings($patron = false)
    {
        global $configArray;

        // Get ID and connect to catalog
        $id = $this->getUniqueID();
        $hierarchyType = $this->getHierarchyType();
        if ($hierarchy = HierarchyFactory::initHierarchy($hierarchyType)) {
            return $hierarchy->hasHolding($id, $patron);
        }
        return false;
    }

    /**
     * Get the collection data to display.
     *
     * @return string The name of the template file
     * @access public
     */
    public function getCoreMetadata()
    {
        global $interface;
        parent::getCoreMetadata();

        $interface->assign("coreRepositoryCode", $this->getRepositoryCode());
        $interface->assign("coreExtent", $this->getExtent());
        $interface->assign("coreDateDescription", $this->getDateDescription());
        $interface->assign(
            "coreBiographicalHistory", $this->getBiographicalHistory()
        );
        $interface->assign("coreActionNotes", $this->getActionNotes());
        $interface->assign("coreCopiesNotes", $this->getExistenceOfCopies());
        $interface->assign("coreOriginalNotes", $this->getExistenceOfOriginals());

        // Send back the template name:
        return 'RecordDrivers/Hierarchy/core.tpl';
    }

    /**
     * Get the collection data to display.
     *
     * @return string The name of the template file
     * @access public
     */
    public function getCollectionMetadata()
    {
        global $interface;
        parent::getCollectionMetadata();

        $interface->assign("collRepositoryCode", $this->getRepositoryCode());
        $interface->assign("collExtent", $this->getExtent());
        $interface->assign("collDateDescription", $this->getDateDescription());
        $interface->assign(
            "collBiographicalHistory", $this->getBiographicalHistory()
        );
        $interface->assign('collAccess', $this->getAccessRestrictions());
        $interface->assign('collRelated', $this->getRelationshipNotes());
        $interface->assign('collPublicationNotes', $this->getPublicationNotes());
        $interface->assign("collActionNotes", $this->getActionNotes());
        $interface->assign("collCopiesNotes", $this->getExistenceOfCopies());
        $interface->assign("collOriginalNotes", $this->getExistenceOfOriginals());

        // Send back the template name:
        return 'RecordDrivers/Hierarchy/collection-info.tpl';
    }

    /**
     * Get the collection data to display.
     *
     * @return string The name of the template file
     * @access public
     */
    public function getCollectionRecord()
    {
        global $interface;

        parent::getCollectionRecord();
        $interface->assign("collRecordRepositoryCode", $this->getRepositoryCode());
        $interface->assign("collRecordExtent", $this->getExtent());
        $interface->assign("collRecordDateDescription", $this->getDateDescription());
        $interface->assign(
            "collRecordBiographicalHistory", $this->getBiographicalHistory()
        );
        $interface->assign('collRecordAccess', $this->getAccessRestrictions());
        $interface->assign('collRecordRelated', $this->getRelationshipNotes());
        $interface->assign(
            'collRecordPublicationNotes', $this->getPublicationNotes()
        );
        $interface->assign("collRecordActionNotes", $this->getActionNotes());
        $interface->assign("collRecordCopiesNotes", $this->getExistenceOfCopies());
        $interface->assign(
            "collRecordOriginalNotes", $this->getExistenceOfOriginals()
        );

        // Send back the template name:
        return 'RecordDrivers/Hierarchy/collection-record.tpl';
    }

    /**
     * Get the repository code of the repository
     *
     * TODO - Check 710 vs 003
     *
     * @return string
     * @access protected
     */
    protected function getRepositoryCode()
    {
        return $this->getFirstFieldValue('710', array('5'));
    }

    /**
     * Get the text of the part/section portion of the Date Description.
     *
     * @return array
     * @access protected
     */
    protected function getDateDescription()
    {
        return $this->getFieldArray('518', array('a'));
    }

    /**
     * Get the extent
     *
     * @return array
     * @access protected
     */
    protected function getExtent()
    {
        return $this->getFieldArray('300', array('a'));
    }

    /**
     * Get the biographical history
     *
     * @return array
     * @access protected
     */
    protected function getBiographicalHistory()
    {
        return $this->getFieldArray('545', array('a'));
    }

    /**
     * Get an array of all the languages associated with the record.
     *
     * @return array
     * @access protected
     */
    protected function getLanguages()
    {
        $codedLanguages = parent::getLanguages();
        $freetextLanguages = $this->getFieldArray('546', array('a'));
        return array_merge($codedLanguages, $freetextLanguages);

    }

    /**
     * Get the publication notes
     *
     * @return array
     * @access protected
     */
    protected function getPublicationNotes()
    {
        return $this->getFieldArray('581', array('a'));
    }

    /**
     * Get the action notes
     *
     * @return array
     * @access protected
     */
    protected function getActionNotes()
    {
        return $this->getFieldArray('583', array('a'));
    }

    /**
     * Get Existence of Originals
     *
     * TODO - Grab addtional data from 535
     *
     * @return array
     * @access protected
     */
    protected function getExistenceOfOriginals()
    {
        return $this->getFieldArray('534', array('a'));
    }

    /**
     * Get Existence of Copies
     *
     * TODO - Grab addtional data from 535
     *
     * @return array
     * @access protected
     */
    protected function getExistenceOfCopies()
    {
        return $this->getFieldArray('533', array('a'));
    }

}
?>
