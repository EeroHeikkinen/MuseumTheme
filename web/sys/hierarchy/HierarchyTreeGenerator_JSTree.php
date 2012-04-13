<?php
/**
 * Hierarchy Tree Generator for the JS_Tree plugin
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

require_once 'HierarchyTreeGenerator.php';

/**
 * Hierarchy Tree Generator
 *
 * This is a helper class for producing hierarchy Trees.
 *
 * @category VuFind
 * @package  HierarchyTreeGenerator
 * @author   Luke O'Sullivan <l.osullivan@swansea.ac.uk>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_an_authentication_handler Wiki
 */

class HierarchyTreeGenerator_JSTree extends HierarchyTreeGenerator
{
    /**
     * Has Hierarchy Tree
     *
     * @param string $source      The source of the Hierarchy Tree
     * @param string $hierarchyID The hierarchy ID to check for (optional)
     *
     * @return mixed An array of hierarchy IDS if an archive tree exists,
     * false if it does not
     * @access public
     */
    public function hasHierarchyTree($source, $hierarchyID = false)
    {
        if (empty($source)) {
            return false;
        }
        $hierarchies = array();
        $id = $this->recordDriver->getUniqueID();
        $inHierarchies = $this->recordDriver->getHierarchyTopID();
        $inHierarchiesTitle = $this->recordDriver->getHierarchyTopTitle();

        // Specific Hierarchy Supplied
        if ($hierarchyID && in_array($hierarchyID, $inHierarchies)) {
            $hierarchyExists = false;
            if ($source == "XMLFile") {
                if (file_exists("services/Collection/xml/" . $hierarchyID . ".xml")
                ) {
                    $hierarchyExists = true;
                }
            } elseif ($source == "Solr") {
                $hierarchyExists = true;
            }
            if ($hierarchyExists) {
                return array(
                    $hierarchyID => $this->getHierarchyName(
                        $hierarchyID, $inHierarchies, $inHierarchiesTitle
                    )
                );
            }

        }

        // Return All Hierarchies
        $i = 0;
        if ($source == "XMLFile") {
            foreach ($inHierarchies as $hierarchyTopID) {
                if (!empty($hierarchyTopID) && file_exists(
                    "services/Collection/xml/" . $hierarchyTopID . ".xml"
                )) {
                    $hierarchies[$hierarchyTopID] = $inHierarchiesTitle[$i];
                }
                $i++;
            }
        } elseif ($source == "Solr") {
            foreach ($inHierarchies as $hierarchyTopID) {
                if (!empty($hierarchyTopID)) {
                    $hierarchies[$hierarchyTopID] = $inHierarchiesTitle[$i];
                }
                $i++;
            }
        }
        if (!empty($hierarchies)) {
            return $hierarchies;
        }
        return false;
    }

    /**
     * Get Hierarchy Tree
     *
     * @param string $source      The source of the Hierarchy Tree
     * @param string $context     The context from which the call has been made
     * @param string $mode        The mode in which the tree should be generated
     * @param string $hierarchyID The hierarchy to get the tree for
     * @param string $recordID    The current record ID
     *
     * @return mixed The desired hierarchy tree output
     * @access public
     */
    public function getHierarchyTree(
        $source, $context, $mode, $hierarchyID, $recordID = false
    ) {
        if (!empty($context) && !empty($mode) && !empty($source)) {
            return $this->transformCollectionXML(
                $source, $context, $mode, $hierarchyID, $recordID
            );
        }
        return false;
    }

    /**
    * transformCollectionXML
    *
    * Transforms Collection XML to Desired Format
    *
    * @param string $source      The source of the Hierarchy Tree
    * @param string $context     The Context in which the tree is being displayed
    * @param string $mode        The Mode in which the tree is being displayed
    * @param string $hierarchyID The hierarchy to get the tree for
    * @param string $recordID    The currently selected Record
    *
    * @return string A HTML List
    * @access public
    */
    protected function transformCollectionXML(
        $source, $context, $mode, $hierarchyID, $recordID
    ) {
        global $configArray;
        $transformation = ucfirst($context) . ucfirst($mode);
        $inHierarchies = $this->recordDriver->getHierarchyTopID();
        $inHierarchiesTitle = $this->recordDriver->getHierarchyTopTitle();

        $hierarchyTitle = $this->getHierarchyName(
            $hierarchyID, $inHierarchies, $inHierarchiesTitle
        );

        $hierarchyType = $this->recordDriver->getHierarchyType();
        $hierarchyDriver = HierarchyFactory::initHierarchy($hierarchyType);

        $base = $configArray['Site']['url'];

        if ($source == "XMLFile") {
            $xmlFile = "services/Collection/xml/" . $hierarchyID . ".xml";
            $xmlFile = file_get_contents($xmlFile);
        } elseif ($source == "Solr") {
            $xmlFile = $this->getXMLFromSolr($hierarchyID);
        }
        $xslFile = "services/Collection/xsl/Storeto" . $transformation . ".xsl";
        if (!file_exists($xslFile) || $xmlFile == false) {
            return false;
        }
        $doc = new DOMDocument();
        $xsl = new XSLTProcessor();

        $doc->load($xslFile);
        $xsl->importStyleSheet($doc);
        $doc->loadXML($xmlFile);

        // Append Collection ID, Collection Title && Record ID
        $xsl->setParameter('', 'titleText', translate("collection_view_record"));
        $xsl->setParameter('', 'collectionID', $hierarchyID);
        $xsl->setParameter('', 'collectionTitle', $hierarchyTitle);
        $xsl->setParameter('', 'baseURL', $base);
        $xsl->setParameter('', 'context', $context);
        $xsl->setParameter('', 'recordID', $recordID);
        return $xsl->transformToXML($doc);
    }
}

?>