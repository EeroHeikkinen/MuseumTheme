<?php
/**
 * JSON handler for SFX RSI check 
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
require_once 'sys/MetaLib.php';

/**
 * JSON RSI check action
 *
 * @category VuFind
 * @package  Controller_Record
 * @author   Markku Heinäsenaho <markku.heinasenaho@helsinki.fi>
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */

class JSON_RSI extends JSON
{
    /**
     * Get data and output in JSON
     *
     * @return void
     * @access public
     */
    public function getRSIStatuses()
    {
        //<SFX server>:<port>/<sfx_instance>/cgi/core/rsi/rsi.cgi
        global $configArray;

        $sfxUrl = $configArray['OpenURL']['url'] . "/cgi/core/rsi/rsi.cgi";
        $metalib = new MetaLib();
        $indexEngine = SearchObjectFactory::initSearchObject()->getIndexEngine();
       
        $dom = new DOMDocument('1.0', 'UTF-8');
        
        # ID REQUEST
        $idReq = $dom->createElement('IDENTIFIER_REQUEST', '');
        $idReq->setAttribute("VERSION", "1.0");
        $idReq->setAttribute("xsi:noNamespaceSchemaLocation", "ISSNRequest.xsd");
        $idReq->setAttribute("xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance");
        $dom->appendChild($idReq);
        
        // Cache values and status in an array
        $rsiResults = array();
        $validRequest = false;
        foreach ($_REQUEST['id'] as $id) {
            if (strncmp($id, 'metalib.', 8) == 0) {
                if (!($record = $metalib->getRecord($id))) {
                    $this->output('Record does not exist', JSON::STATUS_ERROR);
                    return;
                }
                $values = array(
                    'isbn' => !empty($record['ISBN']) ? $record['ISBN'][0] : '',
                    'issn' => !empty($record['ISSN']) ? $record['ISSN'][0] : '',
                    'year' => !empty($record['PublicationDate']) ? $record['PublicationDate'][0] : '',
                    'volume' => !empty($record['Volume']) ? $record['Volume'] : '',
                    'issue' => !empty($record['Issue']) ? $record['Issue'] : '',
                    'institute' => isset($configArray['OpenURL']['institute']) 
                        ? $configArray['OpenURL']['institute'] : '' 
                );
            } else {
                if (!($record = $indexEngine->getRecord($id))) {
                    $this->output('Record does not exist', JSON::STATUS_ERROR);
                    return;
                }
                $recordDriver = RecordDriverFactory::initRecordDriver($record);
                $values = $recordDriver->getRSIValues($recordDriver);
            }
            
            $result = array('id' => $id, 'status' => 'noInformation');
            
            // Ignore the record if mandatory elements are not available
            if (empty($values['issn']) && empty($values['isbn'])) {
                // Mark this result invalid so it can be skipped when processing results
                $result['invalid'] = true;
                $rsiResults[] = $result;
                continue;
            }
            $rsiResults[] = $result;
            $validRequest = true;
            
            // ID REQUEST ITEM
            $idReqItem = $dom->createElement('IDENTIFIER_REQUEST_ITEM', '');
            $idReq->appendChild($idReqItem);
        
            // ID
            if (!empty($values['issn'])) {
                $identifier = $dom->createElement('IDENTIFIER', 'issn:' . $values['issn']);
                $idReqItem->appendChild($identifier);
                	
            }
            if (!empty($values['isbn'])) {
                $identifier = $dom->createElement('IDENTIFIER', 'isbn:' . $values['isbn']);
                $idReqItem->appendChild($identifier);
            }
        
            // Optional elements 
            if ($values['year']) {
                $year = $dom->createElement('YEAR', $values['year']);
                $idReqItem->appendChild($year);
            }
        
            if ($values['volume']) {
                $volume = $dom->createElement('VOLUME', $values['volume']);
                $idReqItem->appendChild($volume);
            }
        
            if ($values['issue']) {
                $issue = $dom->createElement('ISSUE', $values['issue']);
                $idReqItem->appendChild($issue);
            }
        
            if ($values['institute']) {
                $institute = $dom->createElement('INSTITUTE_NAME', $values['institute']);
                $idReqItem->appendChild($institute);
            }
        }
        
        if (!$validRequest) {
            return $this->output(array(), JSON::STATUS_OK);
        }
        
        $xml = $dom->saveXML();
    
        $req = new Proxy_Request($sfxUrl, array('saveBody' => true));
        $req->setMethod(HTTP_REQUEST_METHOD_POST);
        $req->addPostData('request_xml', $xml);
        $req->sendRequest();
        $code = $req->getResponseCode();
        
        if ($code != 200) {
            $this->output("SFX RSI request failed ($code)", JSON::STATUS_ERROR);
            return;
        }
        $dom->loadXML($req->getResponseBody());
        $items = $dom->getElementsByTagName('IDENTIFIER_RESPONSE_ITEM');
        $position = -1;
        foreach ($items as $item) {
            $requests = $dom->getElementsByTagName('IDENTIFIER_REQUEST_ITEM');
            $request = $requests->item(0);
            $position++;
            // Bypass invalid ID's and stop if at the end of list.
            while (isset($rsiResults[$position]['invalid'])) {
                ++$position;
            }
            if (!isset($rsiResults[$position])) {
                break;
            }
            
            $result = $item->getElementsByTagName('RESULT')->item(0)->nodeValue;
            if ($result == 'not found') {
                $rsiResults[$position]['status'] = 'noFullText';
            } elseif ($result == 'maybe') {
                $rsiResults[$position]['status'] = 'maybeFullText';
            } else {
                foreach ($item->getElementsByTagName('AVAILABLE_SERVICES') as $service) {
                    if ($service->nodeValue == 'getFullTxt') {
                        $peerReviewed = false;
                        foreach ($item->getElementsByTagName('PEER_REVIEWED') as $peer) {
                            if ($peer->nodeValue == 'YES') {
                                $peerReviewed = true;
                                break;
                            }
                        }
                        $rsiResults[$position]['status'] = $peerReviewed ? 'peerReviewedFullText' : 'fullText';
                        break;
                    }
                }
            }
        }
        
        $results = array();
        foreach ($rsiResults as $result) {
            $results[] = array('id' => $result['id'], 'status' => $result['status']);
        }
        return $this->output($results, JSON::STATUS_OK);
    }
    
}

