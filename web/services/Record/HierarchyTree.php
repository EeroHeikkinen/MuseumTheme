<?php
/**
 * Hierarchy Tree action for Record module
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
 * @author   Lutz Biedinger <lutz.biedigner@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */
require_once 'Record.php';
require_once 'Drivers/Hierarchy/HierarchyFactory.php';

/**
 * Home action for Record module
 *
 * @category VuFind
 * @package  Controller_Record
 * @author   Lutz Biedinger <lutz.biedigner@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */
class HierarchyTree extends Record
{
    protected $record;
    private $_structure;
    /**
     * Process incoming parameters and display the page.
     *
     * @return void
     * @access public
     */
    public function launch()
    {

        global $interface;
        global $configArray;

        $hierarchyType = $this->recordDriver->getHierarchyType();
        $hierarchyDriver = HierarchyFactory::initHierarchy($hierarchyType);

        $source = $hierarchyDriver->getTreeSource();
        $generator = $hierarchyDriver->getTreeGenerator();
        $generator = !empty($generator) ? $generator : 'JSTree';
        $template = 'view-hierarchyTree_' . $generator . '.tpl';
        $showTreeSelector = true;

        $hasHierarchyTree = $this->recordDriver->hasHierarchyTree($hierarchyDriver);
        if (!$hasHierarchyTree) {
            $url = $configArray['Site']['url'] . "/Record/" . $_REQUEST['id'] . "/Description";
            header('Location: '. $url);
        }


        if (count($hasHierarchyTree) == 1) {
            $keys = array_keys($hasHierarchyTree);
            $hierarchyID = $keys[0];
            $showTreeSelector = false;
        } else {
            $hierarchyID = isset($_REQUEST['hierarchy'])
                ? $_REQUEST['hierarchy'] : false;
        }

        if ($hierarchyID) {
            $hierarchyTree = $this->recordDriver->getHierarchyTree(
                $hierarchyDriver, 'Record', 'List', $hierarchyID, $_GET['id']
            );
            $interface->assign('hierarchyTree', $hierarchyTree);
            $interface->assign('treeSettings', $hierarchyDriver->getTreeSettings());
        }
        $interface->assign('context', "Record");
        $interface->assign('hierarchyID', $hierarchyID);
        $interface->assign('hasHierarchyTree', $hasHierarchyTree);
    	if (isset($configArray['Hierarchy']['search'])?
        		$configArray['Hierarchy']['search']:true){
        	$interface->assign('showTreeSearch', true);
        	$interface->assign('treeSearchLimit', $configArray["Hierarchy"]["treeSearchLimit"]);
        	$interface->assign('treeSearchFullURL', $configArray["Site"]["url"] . "/Search/Results");
        }
        $interface->assign(
            'disablePartialHierarchy',
            $_REQUEST['id'] == $hierarchyID ? true : false
        );

        if (!isset($_GET['lightbox'])) {
            $interface->setPageTitle(
                translate('Hierarchy Tree') .
                ': ' . $this->recordDriver->getBreadcrumb()
            );
            $interface->assign('subTemplate', $template);
            // Set Messages
            $interface->assign('infoMsg', $this->infoMsg);
            $interface->assign('errorMsg', $this->errorMsg);
        }
        // Display Page
        if (isset($_GET['lightbox'])) {
            $interface->assign('title', $_GET['message']);
            $interface->assign('id', $_GET['id']);
            $interface->assign('lightbox', true);
            return $interface->fetch('Record/'.$template);
        } else {
            $interface->assign('showTreeSelector', $showTreeSelector);
            $interface->assign('id', $_GET['id']);
            // This is because if loaded from an link,
            // the tab will not automatically be selected
            $interface->assign('tab', 'Hierarchytree');
            $interface->setTemplate('view.tpl');
            $interface->display('layout.tpl');
        }
    }
}

?>
