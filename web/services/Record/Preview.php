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
    protected $service;
    protected $factoryClassName;
    protected $fields;

    /**
     * Constructor.
     * 
     * @param string $service Service to use for normalization (defaults to RecordManager based service)
     */
    public function __construct($service = null)
    {
        global $configArray;
         
        if ($service !== null) {
            $this->service = $service;
        } else { // Default to RecordManager_Normalization_Service if none specified
            if (!isset($configArray['NormalizationService']['url'])) {
                PEAR::raiseError('No normalization service configured.');
            }
            
            $this->service = new RecordManager_Normalization_Service(
                $configArray['NormalizationService']['url'], 
                $_REQUEST['format'], 
                $_REQUEST['source']
            );
        }
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
        
        $indexFields = $this->service->normalize($_REQUEST['data']);
        $driver = $this->getDriver($indexFields);
        
        $coreTemplate = $driver->getCoreMetadata();
        $interface->assign('coreMetadata', $coreTemplate);
        $interface->setPageTitle('Preview');
        
        // Override any thumbnail.php URLs by direct references
        if (isset($indexFields['thumbnail'])) {
            $interface->assign('coreThumbMedium', $indexFields['thumbnail']);
            $interface->assign('coreThumbLarge', $indexFields['thumbnail']);
            $interface->assign('coreThumbSmall', $indexFields['thumbnail']);
        }
        
        $interface->assign('dynamicTabs', true);
        $interface->setTemplate('view.tpl');
        $interface->display('layout.tpl');
    }
    
    /**
     * Returns a record driver for handling the pseudo-record. In a separate method so the static call can be mocked.
     * 
     * @param array $record The index fields to read from
     * 
     * @return Record driver instance
     */
    protected function getDriver($record) 
    {
        return RecordDriverFactory::initRecordDriver($record);
    }
}

/**
 * RecordManager Normalization Service implementation 
 * 
 * @category VuFind
 * @package  Controller_Record
 * @author   Eero Heikkinen <eero.heikkinen@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */
class RecordManager_Normalization_Service implements Normalization_Service
{
    protected $client;
    
    /**
     * Constructor.
     * 
     * @param string $url    The URL address of the remote service
     * @param string $format The record driver format to use
     * @param string $source The data source id to parse against
     */
    public function __construct($url, $format = null, $source = null)
    {
        $this->client = new Proxy_Request();
        $this->client->setMethod(HTTP_REQUEST_METHOD_POST);
        $this->client->setURL($url);
        
        if ($format !== null) {
            $this->client->addPostData('format', $format);
        }
        if ($source !== null) {
            $this->client->addPostData('source', $source);
        }
    }
    
    /**
     * Retrieve a record from a remote normalization preview service.
     * See {@link https://github.com/KDK-Alli/RecordManager/pull/7 this git pull request}
     * for a description of the contract of such a service.
     *
     * @param string $xml The XML metadata to parse
     *
     * @return array The parsed index fields as an array
     */
    public function normalize($xml)
    {
        $this->client->addPostData('data', $xml);
        
        $result = $this->client->sendRequest();
        if (!PEAR::isError($result)) {
            if ($this->client->getResponseCode() != 200) {
                PEAR::raiseError('Error generating normalization preview.');
            }
        } else {
            PEAR::raiseError($result);
        }
        return json_decode($this->client->getResponseBody(), true);
    }
}

/**
 * Generic Normalization Service interface.
 * 
 * @category VuFind
 * @package  Controller_Record
 * @author   Eero Heikkinen <eero.heikkinen@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */
interface Normalization_Service
{
    /**
     * Normalize a given XML string into a set of index fields.
     * 
     * @param string $xml The XML metadata to parse
     * 
     * @return array The parsed index fields as an array
     */
    public function normalize($xml);
}

?>
