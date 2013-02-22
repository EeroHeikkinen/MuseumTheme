<?php
/**
 * On-the-fly metadata previewer. 
 *
 * PHP version 5
 *
 * Copyright (C) Villanova University 2007.
 * Copyright (C) Eero Heikkinen 2013
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
 * @author   Eero Heikkinen <eero.heikkinen@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */
require_once 'Action.php';
require_once 'sys/Language.php';
require_once 'RecordDrivers/Factory.php';
require_once 'sys/ResultScroller.php';
require_once 'sys/VuFindDate.php';

/**
 * Base class shared by most Record module actions.
 *
 * @category VuFind
 * @package  Controller_Record
 * @author   Eero Heikkinen <eero.heikkinen@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */


class Preview extends Action
{
    protected $recordDriver;
    protected $cacheId;
    protected $db;
    protected $catalog;
    protected $errorMsg;
    protected $infoMsg;
    protected $hasHoldings;

    /**
     * Constructor
     *
     * @access public
     */
    public function __construct()
    {
        global $interface;
        global $configArray;
         
        $url = isset($configArray['NormalizationPreview']['url']) ? $configArray['NormalizationPreview']['url'] : null;
        if (empty($url)) {
            PEAR::raiseError('No normalization preview service configured.');
            exit();
        }
         
        $record = $this->_getPreviewRecord($url, $_REQUEST['data'], $_REQUEST['format'], $_REQUEST['source']);
         
        $this->recordDriver = RecordDriverFactory::initRecordDriver($record);
        
        // Define Default Tab
        $defaultTab = isset($configArray['Site']['defaultRecordTab']) ?
        $configArray['Site']['defaultRecordTab'] : 'Holdings';
        
        if (isset($configArray['Site']['hideHoldingsTabWhenEmpty'])
            && $configArray['Site']['hideHoldingsTabWhenEmpty']
        ) {
            $showHoldingsTab = $this->recordDriver->hasHoldings();
            $interface->assign('hasHoldings', $showHoldingsTab);
            $defaultTab =  (!$showHoldingsTab && $defaultTab == "Holdings") ?
            "Description" : $defaultTab;
        } else {
            $interface->assign('hasHoldings', true);
        }
        
        $tab = (isset($_GET['action'])) ? $_GET['action'] : $defaultTab;
        $interface->assign('tab', $tab);
        //$interface->debugging = true;
        
        // Check if ajax tabs are active
        if (isset($configArray['Site']['ajaxRecordTabs']) && $configArray['Site']['ajaxRecordTabs']) {
            $interface->assign('dynamicTabs', true);
        }
        
        $interface->assign('coreMetadata', $this->recordDriver->getCoreMetadata());
        $interface->assign('coreThumbMedium', $record['thumbnail']);
        $interface->assign('coreThumbLarge', $record['thumbnail']);
        
        // Determine whether to display book previews
        if (isset($configArray['Content']['previews'])) {
            $interface->assignPreviews();
        }
        
        // Determine whether to include script tag for syndetics plus
        if (isset($configArray['Syndetics']['plus'])
            && $configArray['Syndetics']['plus']
            && isset($configArray['Syndetics']['plus_id'])
        ) {
            $interface->assign(
                'syndetics_plus_js',
                "http://plus.syndetics.com/widget.php?id=" .
                $configArray['Syndetics']['plus_id']
            );
        }
        
        // Set flags that control which tabs are displayed:
        if (isset($configArray['Content']['reviews'])) {
            $interface->assign('hasReviews', $this->recordDriver->hasReviews());
        }
        if (isset($configArray['Content']['excerpts'])) {
            $interface->assign('hasExcerpt', $this->recordDriver->hasExcerpt());
        }
        
        //Hierarchy Tree
        $interface->assign(
            'hasHierarchyTree', $this->recordDriver->hasHierarchyTree()
        );
        
        $interface->assign('hasTOC', $this->recordDriver->hasTOC());
        $interface->assign('hasMap', $this->recordDriver->hasMap());
        $this->recordDriver->getTOC();
        
        $interface->assign(
            'extendedMetadata', $this->recordDriver->getExtendedMetadata()
        );
        
        
        // Send down text for inclusion in breadcrumbs
        $interface->assign('breadcrumbText', $this->recordDriver->getBreadcrumb());
        
        // Send down OpenURL for COinS use:
        $interface->assign('openURL', $this->recordDriver->getOpenURL());
        
        // Whether RSI is enabled
        if (isset($configArray['OpenURL']['use_rsi']) && $configArray['OpenURL']['use_rsi']) {
            $interface->assign('rsi', true);
        }
        
        // Whether embedded openurl autocheck is enabled
        if (isset($configArray['OpenURL']['autocheck']) && $configArray['OpenURL']['autocheck']) {
            $interface->assign('openUrlAutoCheck', true);
        }
        
        // Send down legal export formats (if any):
        $interface->assign('exportFormats', $this->recordDriver->getExportFormats());
        
        // Set AddThis User
        $interface->assign(
            'addThis', isset($configArray['AddThis']['key'])
            ? $configArray['AddThis']['key'] : false
        );
        
        // Set Proxy URL
        if (isset($configArray['EZproxy']['host'])) {
            $interface->assign('proxy', $configArray['EZproxy']['host']);
        }
        
        // Get Messages
        $this->infoMsg = isset($_GET['infoMsg']) ? $_GET['infoMsg'] : false;
        $this->errorMsg = isset($_GET['errorMsg']) ? $_GET['errorMsg'] : false;
    }
    
    /**
     * Process incoming parameters and display the page.
     *
     * @return void
     * @access public
     */
    public function launch()
    {
        global $interface;
        
        if (!$interface->is_cached($this->cacheId)) {
            $interface->setPageTitle(
                translate('Description') . ': ' .
                $this->recordDriver->getBreadcrumb()
            );
            $interface->assign(
                'extendedMetadata', $this->recordDriver->getExtendedMetadata()
            );
            $interface->assign('subTemplate', 'view-description.tpl');
            $interface->setTemplate('view.tpl');
        }
        
        // Set Messages
        $interface->assign('infoMsg', $this->infoMsg);
        $interface->assign('errorMsg', $this->errorMsg);
        
        // Display Page
        $interface->display('layout.tpl', $this->cacheId);
    }
    
    protected function _getPreviewRecord($serviceUrl, $metadata, $format, $source = null) {
        $client = new Proxy_Request();
        $client->setMethod(HTTP_REQUEST_METHOD_POST);
        $client->setURL($serviceUrl);
        $client->addPostData('data', $metadata);
        $client->addPostData('format', $format);
        if(!empty($source))
            $client->addPostData('source', $source);
        
        $result = $client->sendRequest();
        if (!PEAR::isError($result)) {
            if ($client->getResponseCode() != 200) {
                PEAR::raiseError('Error generating normalization preview.');
            }
        } else {
            PEAR::raiseError($result);
        }
        return json_decode($client->getResponseBody(), true);
    }
}

?>
