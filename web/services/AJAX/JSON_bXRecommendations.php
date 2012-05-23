<?php
/**
 * JSON handler for bX recommendations 
 *
 * PHP version 5
 *
 * Copyright (C) The National Library of Finland 2012.
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
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */
require_once 'JSON.php';
require_once 'RecordDrivers/Factory.php';

/**
 * JSON bX Recommendations action
 *
 * @category VuFind
 * @package  Controller_Record
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */

// TODO: This should probably be a recommendation subclass, but those are geared
// towards search results, so we'll keep this separate for now

class JSON_bXRecommendations extends JSON
{
    /**
     * Get data and output in JSON
     *
     * @return void
     * @access public
     */
    public function getbXRecommendations()
    {
        global $configArray;
        
        if (!isset($configArray['bX']['token'])) {
            $this->output('bX support not enabled', JSON::STATUS_ERROR);
            return;
        }
        
        $id = $_REQUEST['id'];
        $source = $_REQUEST['source'];
        if ($source == 'MetaLib') {
            require_once 'sys/MetaLib.php';
            $metalib = new MetaLib();
            if (!($record = $metalib->getRecord($id))) {
                $this->output('Record does not exist', JSON::STATUS_ERROR);
                return;
            }
            $openUrl = $record['documents'][0]['openUrl'];
        } else {
            $searchObject = SearchObjectFactory::initSearchObject();
            if (!($record = $searchObject->getIndexEngine()->getRecord($id))) {
                $this->output('Record does not exist', JSON::STATUS_ERROR);
                return;
            }
            $recordDriver = RecordDriverFactory::initRecordDriver($record);
            $openUrl = $recordDriver->getOpenURL();
        }
                
        $params = http_build_query(
            array(
                'token' => $configArray['bX']['token'],
                'format' => 'xml',
                'source' => isset($configArray['bX']['source']) ? $configArray['bX']['source'] : 'global',
                'maxRecords' => isset($configArray['bX']['maxRecords']) ? $configArray['bX']['maxRecords'] : '5',
                'threshold' => isset($configArray['bX']['threshold']) ? $configArray['bX']['threshold'] : '50',
            )
        );
        $openUrl .= '&res_dat=' . urlencode($params);
        
        $baseUrl = isset($configArray['bX']['baseUrl']) 
            ? $configArray['bX']['baseUrl'] 
            : 'http://recommender.service.exlibrisgroup.com/service/recommender/openurl';
        $client = new HTTP_Request();
        $client->setMethod(HTTP_REQUEST_METHOD_GET);
        $client->setURL($baseUrl . "?$openUrl");

        $result = $client->sendRequest();
        if (!PEAR::isError($result)) {
            // Even if we get a response, make sure it's a 'good' one.
            if ($client->getResponseCode() != 200) {
                $this->output('bX request failed, response code ' . $client->getResponseCode(), JSON::STATUS_ERROR);
            }
        } else {
            $this->_output('bX request failed: ' . $result, JSON::STATUS_ERROR);
        }
        $xml = simplexml_load_string($client->getResponseBody());
        $data = array();
        $jnl = 'info:ofi/fmt:xml:xsd:journal';
        $xml->registerXPathNamespace('jnl', $jnl);
        foreach ($xml->xpath('//jnl:journal') as $journal) {
            $item = $this->_convertToArray($journal, $jnl);
            if (!isset($item['authors']['author'][0])) {
                $item['authors']['author'] = array($item['authors']['author']);
            }
            $item['openurl'] = $this->_createOpenUrl($item);
            $data[] = $item;
        }
        $this->output($data, JSON::STATUS_OK);
    }
    
    /**
     * Convert XML to array
     * @param simpleXMLElement $xml  XML to convert
     * @param string           $ns   Optional namespace for nodes
     * 
     * @return array
     * @access protected
     */
    protected function _convertToArray($xml, $ns = '')
    {
        $result = array();
        foreach ($xml->children($ns) as $node) {
            $children = $node->children($ns);
            if (count($children) > 0) {
                $item = $this->_convertToArray($node, $ns);
            } else {
                $item = (string)$node;
            }
            $key = $node->getName();
            if (isset($result[$key])) {
                if (!is_array($result[$key])) {
                    $result[$key] = array($result[$key]);
                }
                $result[$key][] = $item;
            } else {
                $result[$key] = $item;
            }
        }
        return $result;
    }
    
    /**
     * Create OpenURL for the item
     * @param array $item  Item fields
     * 
     * @return string
     * @access protected
     */
    protected function _createOpenUrl($item)
    {
        global $configArray;
        
        if (!isset($configArray['OpenURL']['url'])) {
            return '';
        }
        
        $coinsID = isset($configArray['OpenURL']['rfr_id'])
            ? $configArray['OpenURL']['rfr_id']
            : $configArray['COinS']['identifier'];
        if (empty($coinsID)) {
            $coinsID = 'vufind.svn.sourceforge.net';
        }
        
        $params = array(
            'ctx_ver' => 'Z39.88-2004',
            'ctx_enc' => 'info:ofi/enc:UTF-8',
            'rfr_id' => "info:sid/{$coinsID}:generator",
            'rft_val_fmt' => 'info:ofi/fmt:kev:mtx:journal'
        );
        
        foreach ($item as $key => $value) {
            if ($key == 'authors') {
                foreach ($value['author'][0] as $auKey => $auValue) {
                    $params["rft.$auKey"] = $auValue;
                }
            } else {
                $params["rft.$key"] = $value;
            }
        }
        return http_build_query($params);        
    }
}

