<?php
/**
 * Archival Tree Generator for the JS_Tree plugin
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
 * @package  ArchivalTreeGenerator
 * @author   Luke O'Sullivan <l.osullivan@swansea.ac.uk>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_an_authentication_handler Wiki
 */

require_once 'ArchivalTreeGenerator.php';

/**
 * Archival Tree Generator
 *
 * This is a helper class for producing archival Trees.
 *
 * @category VuFind
 * @package  ArchivalTreeGenerator
 * @author   Luke O'Sullivan <l.osullivan@swansea.ac.uk>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_an_authentication_handler Wiki
 */

class ArchivalTreeGenerator_JSTree extends ArchivalTreeGenerator
{
    /**
     * Has Archival Tree
     *
     * @return bool true if an archive tree exists, false if it does not
     * @access public
     */
    public function hasArchivalTree()
    {
        $id = $this->recordDriver->getUniqueID();
        $parentID = $this->recordDriver->getAbsoluteParent();
        if (!empty($parentID)
            && file_exists("services/Collection/xml/" .$parentID . ".xml")
        ) {
            return true;
        }
        return false;
    }

    /**
     * Get Archival Tree
     *
     * @param string $context The context from which the call has been made
     * @param string $mode    The mode in which the tree should be generated
     *
     * @return mixed The desired archival tree output
     * @access public
     */
    public function getArchivalTree($context, $mode)
    {
        $method = "getArchival" . $context . $mode;
        if (method_exists($this, $method)) {
            return $this->$method();
        }
        return false;
    }

    /**
     * Get an XML version of the archival tree this record belongs too
     * from the ILS and provides links in the record module.
     *
     * @return xml
     * @access public
     */
    public function getArchivalCollectionTree()
    {
        $id = $this->recordDriver->getUniqueID();
        $archivalTree = $this->transfromCollectionXML($id, 'CollectionTree');
        return $archivalTree;
    }

    /**
     * Get an XML version of the archival tree this record belongs too
     * from the ILS and provides links in the record module.
     *
     * @return xml
     * @access public
     */
    public function getArchivalRecordTree()
    {
        $id = $this->recordDriver->getUniqueID();
        $archivalTree = $this->transfromCollectionXML($id, 'RecordTree');
        return $archivalTree;
    }

    /**
     * Get a HTML version of the archival tree this record belongs to
     * from the ILS and provides links in the record module.
     *
     * @return xml
     * @access public
     */
    public function getArchivalRecordList()
    {
        $id = $this->recordDriver->getUniqueID();
        $archivalTree = $this->transfromCollectionXML($id, 'RecordList');
        return $archivalTree;
    }

    /**
     * Get an XML version of the archival tree this record belongs too
     * from the ILS and provides links in the record module.
     *
     * @param string $recordID A record ID (Optional)
     *
     * @return xml
     * @access public
     */
    public function getArchivalCollectionList($recordID = false)
    {
        $id = $this->recordDriver->getUniqueID();
        $archivalTree = $this->transfromCollectionXML($id, 'CollectionList');
        return $archivalTree;
    }

    /**
    * transfromCollectionXML
    *
    * Transforms Collection XML to Desired Format
    *
    * @param string $recordID       A record ID
    * @param string $transformation The XSL transformation to apply
    *
    * @return string A HTML List
    * @access public
    */
    protected function transfromCollectionXML($recordID, $transformation)
    {
        $parentID = $this->recordDriver->getAbsoluteParent();
        $parentTitle = $this->recordDriver->getAbsoluteParentTitle();
        $xmlFile = "services/Collection/xml/" .$parentID . ".xml";
        $xslFile = "services/Collection/xsl/Storeto" . $transformation . ".xsl";
        if (!file_exists($xslFile) || !file_exists($xmlFile)) {
            return false;
        }
        $doc = new DOMDocument();
        $xsl = new XSLTProcessor();

        $doc->load($xslFile);
        $xsl->importStyleSheet($doc);

        $doc->load($xmlFile);

        // Append Collection ID, Collection Title && Record ID
        $collNode = new DOMElement("collectionID", $parentID);
        $doc->appendChild($collNode);
        $collTitle = new DOMElement("collectionTitle", $parentTitle);
        $doc->appendChild($collTitle);
        $record = new DOMElement("recordID", $recordID);
        $doc->appendChild($record);

        $xsl->registerPHPFunctions();
        return  $xsl->transformToXML($doc);
    }

    /**
    * getTreeBaseURL
    * Stub Function used to provide a base URL to XSL Files
    *
    * @param string $module A VuFind Module Name
    *
    * @return string A URL
    * @access public
    */
    static function getTreeBaseURL($module)
    {
        global $configArray;

        $base = $configArray['Site']['url'];
        if ($module == "collection") {
            return $base . "/Collection";
        } else {
            return $base . "/Record";
        }
    }

    /**
    * getTitleText
    * Stub Function used to translate Title Text
    *
    * @return string A translated title string
    * @access public
    */
    static function getTreeTitleText()
    {
        return translate("collection_view_record");
    }

    /**
    * getTreeTitle
    * Stub Function used to modify record titles
    *
    * @param string $title A record title
    *
    * @return string A translated title string
    * @access public
    */
    static function getTreeTitle($title)
    {
        if (strlen($title) > 52) {
            return substr($title, 0, 52) . "...";
        }
        return $title;
    }
}

?>