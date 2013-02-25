<?php
/**
 * Voyager ILS Driver
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
 * @package  ILS_Drivers
 * @author   Andrew S. Nagy <vufind-tech@lists.sourceforge.net>
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @author   Luke O'Sullivan <l.osullivan@swansea.ac.uk>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_an_ils_driver Wiki
 */

require_once 'Voyager.php';

/**
 * Voyager Restful ILS Driver
 *
 * @category VuFind
 * @package  ILS_Drivers
 * @author   Andrew S. Nagy <vufind-tech@lists.sourceforge.net>
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @author   Luke O'Sullivan <l.osullivan@swansea.ac.uk>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_an_ils_driver Wiki
 */
class VoyagerRestful extends Voyager
{
    protected $ws_host;
    protected $ws_port;
    protected $ws_app;
    protected $ws_dbKey;
    protected $ws_patronHomeUbId;
    protected $ws_pickUpLocations;
    protected $defaultPickUpLocation;
    protected $holdCheckLimit;
    protected $callSlipCheckLimit;
    protected $checkRenewalsUpFront;

    /**
     * Constructor
     *
     * @param string $configFile Name of configuration file to load (relative to
     * web/conf folder; defaults to VoyagerRestful.ini).
     *
     * @access public
     */
    public function __construct($configFile = 'VoyagerRestful.ini')
    {
        // Call the parent's constructor...
        parent::__construct($configFile);

        // Define Voyager Restful Settings
        $this->ws_host = $this->config['WebServices']['host'];
        $this->ws_port = $this->config['WebServices']['port'];
        $this->ws_app = $this->config['WebServices']['app'];
        $this->ws_dbKey = $this->config['WebServices']['dbKey'];
        $this->ws_patronHomeUbId = $this->config['WebServices']['patronHomeUbId'];
        $this->ws_pickUpLocations
            = (isset($this->config['pickUpLocations']))
            ? $this->config['pickUpLocations'] : false;
        $this->defaultPickUpLocation
            = $this->config['Holds']['defaultPickUpLocation'];
        $this->holdCheckLimit
            = isset($this->config['Holds']['holdCheckLimit'])
            ? $this->config['Holds']['holdCheckLimit'] : "15";
        $this->callSlipCheckLimit
            = isset($this->config['CallSlips']['checkLimit'])
            ? $this->config['CallSlips']['checkLimit'] : "15";
        $this->checkRenewalsUpFront
            = isset($this->config['Renewals']['checkUpFront'])
            ? $this->config['Renewals']['checkUpFront'] : true;
    }

    /**
     * Public Function which retrieves renew, hold and cancel settings from the
     * driver ini file.
     *
     * @param string $function The name of the feature to be checked
     *
     * @return array An array with key-value pairs.
     * @access public
     */
    public function getConfig($function)
    {
        if (isset($this->config[$function]) ) {
            $functionConfig = $this->config[$function];
        } else {
            $functionConfig = false;
        }
        return $functionConfig;
    }

    /**
     * Support method for VuFind Hold Logic. Take an array of status strings
     * and determines whether or not an item is holdable based on the
     * valid_hold_statuses settings in configuration file
     *
     * @param array $statusArray The status codes to analyze.
     *
     * @return bool Whether an item is holdable
     * @access protected
     */
    protected function isHoldable($statusArray)
    {
        // User defined hold behaviour
        $is_holdable = true;

        if (isset($this->config['Holds']['valid_hold_statuses'])) {
            $valid_hold_statuses_array
                = explode(":", $this->config['Holds']['valid_hold_statuses']);

            if (count($valid_hold_statuses_array > 0)) {
                foreach ($statusArray as $status) {
                    if (!in_array($status, $valid_hold_statuses_array)) {
                        $is_holdable = false;
                    }
                }
            }
        }
        return $is_holdable;
    }

    /**
     * Support method for VuFind Hold Logic. Takes an item type id
     * and determines whether or not an item is borrowable based on the
     * non_borrowable settings in configuration file
     *
     * @param string $itemTypeID The item type id to analyze.
     *
     * @return bool Whether an item is borrowable
     * @access protected
     */
    protected function isBorrowable($itemTypeID)
    {
        $is_borrowable = true;
        if (isset($this->config['Holds']['non_borrowable'])) {
            $non_borrow = explode(":", $this->config['Holds']['non_borrowable']);
            if (in_array($itemTypeID, $non_borrow)) {
                $is_borrowable = false;
            }
        }

        return $is_borrowable;
    }

    /**
     * Support method for VuFind Call Slip Logic. Take a holdings row array 
     * and determine whether or not a call slip is allowed based on the
     * valid_call_slip_locations settings in configuration file
     *
     * @param array $holdingsRow The holdings row to analyze.
     *
     * @return bool Whether an item is holdable
     * @access protected
     */
    protected function isCallSlipAllowed($holdingsRow)
    {
        $holdingsRow = $holdingsRow['_fullRow'];
        if (isset($this->config['CallSlips']['valid_item_types'])) {
            $validTypes = explode(":", $this->config['CallSlips']['valid_item_types']);

            $type = $holdingsRow['TEMP_ITEM_TYPE_ID'] ? $holdingsRow['TEMP_ITEM_TYPE_ID'] : $holdingsRow['ITEM_TYPE_ID'];
            return in_array($type, $validTypes);
        }
        return true;
    }
    
    /**
     * Protected support method for getHolding.
     *
     * @param array $id A Bibliographic id
     *
     * @return array Keyed data for use in an sql query
     * @access protected
     */
    protected function getHoldingItemsSQL($id)
    {
        $sqlArray = parent::getHoldingItemsSQL($id);
        $sqlArray['expressions'][] = "ITEM.ITEM_TYPE_ID";
        $sqlArray['expressions'][] = "ITEM.TEMP_ITEM_TYPE_ID";
        
        return $sqlArray;
    }
    
    /**
     * Protected support method for getHolding.
     *
     * @param array $id A Bibliographic id
     *
     * @return array Keyed data for use in an sql query
     * @access protected
     */
    protected function getHoldingNoItemsSQL($id)
    {
        $sqlArray = parent::getHoldingItemsSQL($id);
        $sqlArray['expressions'][] = "null as ITEM_TYPE_ID";
        $sqlArray['expressions'][] = "null as TEMP_ITEM_TYPE_ID";
        
        return $sqlArray;        
    }    

    /**
     * Protected support method for getHolding.
     *
     * @param array $sqlRow SQL Row Data
     *
     * @return array Keyed data
     * @access protected
     */
    protected function processHoldingRow($sqlRow)
    {
        $row = parent::processHoldingRow($sqlRow);
        $row += array('item_id' => $sqlRow['ITEM_ID'], '_fullRow' => $sqlRow);
        return $row;
    }

    /**
     * Protected support method for getHolding.
     *
     * @param array $data   Item Data
     * @param mixed $patron Patron Data or boolean false
     *
     * @return array Keyed data
     * @access protected
     */

    protected function processHoldingData($data, $patron = false)
    {
        $holding = parent::processHoldingData($data, $patron);
        $mode = CatalogConnection::getHoldsMode();

        foreach ($holding as $i => $row) {
            $is_borrowable = $this->isBorrowable($row['_fullRow']['ITEM_TYPE_ID']);
            $is_holdable = $this->isHoldable($row['_fullRow']['STATUS_ARRAY']);
            $isCallSlipAllowed = $this->isCallSlipAllowed($row);
            // If the item cannot be borrowed or if the item is not holdable,
            // set is_holdable to false
            if (!$is_borrowable || !$is_holdable) {
                $is_holdable = false;
            }
            
            // Only used for driver generated hold links
            $addLink = false;
            $addCallSlipLink = false;
            $holdType = '';
            $callslip = '';

            // Hold Type - If we have patron data, we can use it to dermine if a
            // hold link should be shown
            if ($is_holdable) {
                if ($patron && $mode == "driver") {
                    // This limit is set as the api is slow to return results
                    if ($i < $this->holdCheckLimit && $this->holdCheckLimit != "0") {
                        $holdType = $this->determineHoldType(
                            $patron['id'], $row['id'], $row['item_id']
                        );
                        $addLink = $holdType ? $holdType : false;
                    } else {
                        $holdType = "auto";
                        $addLink = "check";
                    }
                } else {
                    $holdType = "auto";
                }
            }
            if ($isCallSlipAllowed) {
                if ($patron && $mode == "driver") {
                    if ($i < $this->callSlipCheckLimit && $this->callSlipCheckLimit != "0") {
                        $callslip = false;
                        if ($this->isCallSlipAllowed($row)) {
                            $callslip = $this->checkItemRequests($patron['id'], 'callslip', $row['id'], $row['item_id']);
                        }
                    } else {
                        $callslip = "auto";
                        $addCallSlipLink = "check";
                    }
                } else {
                    $callslip = "auto";
                }
            }
            $holding[$i] += array(
                'is_holdable' => $is_holdable,
                'holdtype' => $holdType,
                'addLink' => $addLink,
                'level' => "copy",
                'callslip' => $callslip,
                'addCallSlipLink' => $addCallSlipLink
            );
            unset($holding[$i]['_fullRow']);
        }
        return $holding;
    }

    /**
     * checkRequestIsValid
     *
     * This is responsible for determining if an item is requestable
     *
     * @param string $id     The Bib ID
     * @param array  $data   An Array of item data
     * @param patron $patron An array of patron data
     *
     * @return string True if request is valid, false if not
     * @access public
     */
    public function checkRequestIsValid($id, $data, $patron)
    {
        $holdType = isset($data['holdtype']) ? $data['holdtype'] : "auto";
        $level = isset($data['level']) ? $data['level'] : "copy";
        $mode = ("title" == $level) ? CatalogConnection::getTitleHoldsMode()
            : CatalogConnection::getHoldsMode();
        if ("driver" == $mode && "auto" == $holdType) {
            $itemID = isset($data['item_id']) ? $data['item_id'] : false;
            $result = $this->determineHoldType($patron['id'], $id, $itemID);
            if (!$result || $result == 'block') {
                return $result;
            }
        }
        return true;
    }

    /**
     * checkCallSlipRequestIsValid
     *
     * This is responsible for determining if an item is requestable
     *
     * @param string $id     The Bib ID
     * @param array  $data   An Array of item data
     * @param patron $patron An array of patron data
     *
     * @return string True if request is valid, false if not
     * @access public
     */
    public function checkCallSlipRequestIsValid($id, $data, $patron)
    {
        if ($this->checkAccountBlocks($patron['id'])) {
            return 'block';
        }
        
        $level = isset($data['level']) ? $data['level'] : "copy";
        $itemID = ($level != 'title' && isset($data['item_id'])) ? $data['item_id'] : false;
        $result = $this->checkItemRequests($patron['id'], 'callslip', $id, $itemID);
        if (!$result || $result == 'block') {
            return $result;
        }
        return true;
    }
    
    /**
     * Determine Renewability
     *
     * This is responsible for determining if an item is renewable
     *
     * @param string $patronId The user's patron ID
     * @param string $itemId   The Item Id of item
     *
     * @return mixed Array of the renewability status and associated
     * message
     * @access protected
     */

    protected function isRenewable($patronId, $itemId)
    {
        // Build Hierarchy
        $hierarchy = array(
            "patron" => $patronId,
            "circulationActions" => "loans"
        );

        // Add Required Params
        $params = array(
            "patron_homedb" => $this->ws_patronHomeUbId,
            "view" => "full"
        );

        // Create Rest API Renewal Key
        $restItemID = $this->ws_dbKey. "|" . $itemId;

        // Add to Hierarchy
        $hierarchy[$restItemID] = false;

        $renewability = $this->makeRequest($hierarchy, $params, "GET");
        $renewability = $renewability->children();
        $node = "reply-text";
        $reply = (string)$renewability->$node;
        if ($reply == "ok") {
            $loanAttributes = $renewability->resource->loan->attributes();
            $canRenew = (string)$loanAttributes['canRenew'];
            if ($canRenew == "Y") {
                $renewData['message'] = false;
                $renewData['renewable'] = true;
            } else {
                $renewData['message'] = "renew_item_no";
                $renewData['renewable'] = false;
            }
        } else {
            $renewData['message'] = "renew_determine_fail";
            $renewData['renewable'] = false;
        }
        return $renewData;
    }

    /**
     * Protected support method for getMyTransactions.
     *
     * @param array $sqlRow An array of keyed data
     * @param array $patron An array of keyed patron data
     *
     * @return array Keyed data for display by template files
     * @access protected
     */
    protected function processMyTransactionsData($sqlRow, $patron)
    {
        $transactions = parent::processMyTransactionsData($sqlRow, $patron);

        // Do we need to check renewals up front?  If so, do the check; otherwise,
        // set up fake "success" data to move us forward.
        $renewData = $this->checkRenewalsUpFront
            ? $this->isRenewable($patron['id'], $transactions['item_id'])
            : array('message' => false, 'renewable' => true);

        $transactions['renewable'] = $renewData['renewable'];
        $transactions['message'] = $renewData['message'];

        return $transactions;
    }

     /**
     * Get Pick Up Locations
     *
     * This is responsible for gettting a list of valid library locations for
     * holds / recall retrieval
     *
     * @param array $patron      Patron information returned by the patronLogin
     * method.
     * @param array $holdDetails Optional array, only passed in when getting a list
     * in the context of placing a hold; contains most of the same values passed to
     * placeHold, minus the patron data.  May be used to limit the pickup options
     * or may be ignored.  The driver must not add new options to the return array
     * based on this data or other areas of VuFind may behave incorrectly.
     *
     * @return array        An array of associative arrays with locationID and
     * locationDisplay keys
     * @access public
     */
    public function getPickUpLocations($patron = false, $holdDetails = null)
    {
        if ($this->ws_pickUpLocations) {
            foreach ($this->ws_pickUpLocations as $code => $library) {
                $pickResponse[] = array(
                    'locationID' => $code,
                    'locationDisplay' => $library
                );
            }
        } else {
            $sql = "SELECT CIRC_POLICY_LOCS.LOCATION_ID as location_id, " .
                "NVL(LOCATION.LOCATION_DISPLAY_NAME, LOCATION.LOCATION_NAME) " .
                "as location_name from " .
                $this->dbName . ".CIRC_POLICY_LOCS, $this->dbName.LOCATION " .
                "where CIRC_POLICY_LOCS.PICKUP_LOCATION = 'Y' ".
                "and CIRC_POLICY_LOCS.LOCATION_ID = LOCATION.LOCATION_ID";

            try {
                $sqlStmt = $this->db->prepare($sql);
                $sqlStmt->execute();
            } catch (PDOException $e) {
                return new PEAR_Error($e->getMessage());
            }

            // Read results
            while ($row = $sqlStmt->fetch(PDO::FETCH_ASSOC)) {
                $pickResponse[] = array(
                    "locationID" => $row['LOCATION_ID'],
                    "locationDisplay" => utf8_encode($row['LOCATION_NAME'])
                );
            }
        }
        return $pickResponse;
    }

    /**
     * Get Default Pick Up Location
     *
     * Returns the default pick up location set in VoyagerRestful.ini
     *
     * @param array $patron      Patron information returned by the patronLogin
     * method.
     * @param array $holdDetails Optional array, only passed in when getting a list
     * in the context of placing a hold; contains most of the same values passed to
     * placeHold, minus the patron data.  May be used to limit the pickup options
     * or may be ignored.
     *
     * @return string       The default pickup location for the patron.
     */
    public function getDefaultPickUpLocation($patron = false, $holdDetails = null)
    {
        return $this->defaultPickUpLocation;
    }

     /**
     * Make Request
     *
     * Makes a request to the Voyager Restful API
     *
     * @param array  $hierarchy Array of key-value pairs to embed in the URL path of
     * the request (set value to false to inject a non-paired value).
     * @param array  $params    A keyed array of query data
     * @param string $mode      The http request method to use (Default of GET)
     * @param string $xml       An optional XML string to send to the API
     *
     * @return obj  A Simple XML Object loaded with the xml data returned by the API
     * @access protected
     */
    protected function makeRequest($hierarchy, $params = false, $mode = "GET",
        $xml = false
    ) {
        // Build Url Base
        $urlParams = "http://{$this->ws_host}:{$this->ws_port}/{$this->ws_app}";

        // Add Hierarchy
        foreach ($hierarchy as $key => $value) {
            $hierarchyString[] = ($value !== false)
                ? urlencode($key) . "/" . urlencode($value) : urlencode($key);
        }

        // Add Params
        foreach ($params as $key => $param) {
            $queryString[] = $key. "=" . urlencode($param);
        }

        // Build Hierarchy
        $urlParams .= "/" . implode("/", $hierarchyString);

        // Build Params
        if (isset($queryString)) {
            $urlParams .= "?" . implode("&", $queryString);
        }

        // Create Proxy Request
        $client = new Proxy_Request($urlParams);

        // Select Method
        if ($mode == "POST") {
            $client->setMethod(HTTP_REQUEST_METHOD_POST);
            if ($xml) {
                $client->addRawPostData($xml);
            }
        } else if ($mode == "PUT") {
            $client->setMethod(HTTP_REQUEST_METHOD_PUT);
            $client->addRawPostData($xml);
        } else if ($mode == "DELETE") {
            $client->setMethod(HTTP_REQUEST_METHOD_DELETE);
        } else {
            $client->setMethod(HTTP_REQUEST_METHOD_GET);
        }

        // Send Request and Retrieve Response
        $client->sendRequest();
        $xmlResponse = $client->getResponseBody();
        error_log("VR: $mode request $urlParams, body:\n$xml\nResults:\n$xmlResponse");
        $oldLibXML = libxml_use_internal_errors();
        libxml_use_internal_errors(true);
        $simpleXML = simplexml_load_string($xmlResponse);
        libxml_use_internal_errors($oldLibXML);
        
        if ($simpleXML === false) {
            $logger = new Logger();
            $error = libxml_get_last_error();
            $logger->log('VoyagerRestful: Failed to parse response XML: ' . $error->message . ", response:\n" . $xmlResponse, PEAR_LOG_ERR);
            return false;
        }
        return $simpleXML;
    }

    /**
     * Build Basic XML
     *
     * Builds a simple xml string to send to the API
     *
     * @param array $xml A keyed array of xml node names and data
     *
     * @return string    An XML string
     * @access protected
     */

    protected function buildBasicXML($xml)
    {
        $xmlString = "";

        foreach ($xml as $root => $nodes) {
            $xmlString .= "<" . $root . ">";

            foreach ($nodes as $nodeName => $nodeValue) {
                $xmlString .= "<" . $nodeName . ">";
                $xmlString .= htmlspecialchars($nodeValue, ENT_COMPAT, "UTF-8");
                // Split out any attributes
                $nodeName = strtok($nodeName, ' ');
                $xmlString .= "</" . $nodeName . ">";
            }

            // Split out any attributes
            $root = strtok($root, ' '); 
            $xmlString .= "</" . $root . ">";
        }

        $xmlComplete = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" . $xmlString;

        return $xmlComplete;
    }

    /**
     * Check Account Blocks
     *
     * Checks if a user has any blocks against their account which may prevent them
     * performing certain operations
     *
     * @param string $patronId A Patron ID
     *
     * @return mixed           A boolean false if no blocks are in place and an array
     * of block reasons if blocks are in place
     * @access protected
     */

    protected function checkAccountBlocks($patronId)
    {
        $blockReason = false;

        // Build Hierarchy
        $hierarchy = array(
            "patron" =>  $patronId,
            "patronStatus" => "blocks"
        );

        // Add Required Params
        $params = array(
            "patron_homedb" => $this->ws_patronHomeUbId,
            "view" => "full"
        );

        $blocks = $this->makeRequest($hierarchy, $params);

        if ($blocks) {
            $node = "reply-text";
            $reply = (string)$blocks->$node;

            // Valid Response
            if ($reply == "ok" && isset($blocks->blocks)) {
                $blockReason = array();
                foreach ($blocks->blocks->institution->borrowingBlock
                    as $borrowBlock
                ) {
                    $blockReason[] = (string)$borrowBlock->blockReason;
                }
            }
        }

        return $blockReason;
    }

    /**
     * Renew My Items
     *
     * Function for attempting to renew a patron's items.  The data in
     * $renewDetails['details'] is determined by getRenewDetails().
     *
     * @param array $renewDetails An array of data required for renewing items
     * including the Patron ID and an array of renewal IDS
     *
     * @return array              An array of renewal information keyed by item ID
     * @access public
     */
    public function renewMyItems($renewDetails)
    {
        $renewProcessed = array();
        $renewResult = array();
        $failIDs = array();
        $patronId = $renewDetails['patron']['id'];

        // Get Account Blocks
        $finalResult['blocks'] = $this->checkAccountBlocks($patronId);

        if ($finalResult['blocks'] === false) {
            // Add Items and Attempt Renewal
            foreach ($renewDetails['details'] as $renewID) {
                // Build an array of item ids which may be of use in the template
                // file
                $failIDs[$renewID] = "";

                // Did we need to check renewals up front?  If not, do the check now;
                // otherwise, set up fake "success" data to avoid redundant work.
                $renewable = !$this->checkRenewalsUpFront
                    ? $this->isRenewable($patronId, $renewID)
                    : array('renewable' => true);

                // Don't even try to renew a non-renewable item; we don't want to
                // break any rules, and Voyager's API doesn't always enforce well.
                if (isset($renewable['renewable']) && $renewable['renewable']) {
                    // Build Hierarchy
                    $hierarchy = array(
                        "patron" => $patronId,
                        "circulationActions" => "loans"
                    );

                    // Add Required Params
                    $params = array(
                        "patron_homedb" => $this->ws_patronHomeUbId,
                        "view" => "full"
                    );

                    // Create Rest API Renewal Key
                    $restRenewID = $this->ws_dbKey. "|" . $renewID;

                    // Add to Hierarchy
                    $hierarchy[$restRenewID] = false;

                    // Attempt Renewal
                    $renewalObj = $this->makeRequest($hierarchy, $params, "POST");

                    $process = $this->processRenewals($renewalObj);
                    if (PEAR::isError($process)) {
                        return $process;
                    }
                    // Process Renewal
                    $renewProcessed[] = $process;
                }
            }

            // Place Successfully processed renewals in the details array
            foreach ($renewProcessed as $renewal) {
                if ($renewal !== false) {
                    $finalResult['details'][$renewal['item_id']] = $renewal;
                    unset($failIDs[$renewal['item_id']]);
                }
            }
            // Deal with unsuccessful results
            foreach ($failIDs as $id => $junk) {
                $finalResult['details'][$id] = array(
                    "success" => false,
                    "new_date" => false,
                    "item_id" => $id,
                    "sysMessage" => ""
                );
            }
        }
        return $finalResult;
    }

    /**
     * Process Renewals
     *
     * A support method of renewMyItems which determines if the renewal attempt
     * was successful
     *
     * @param object $renewalObj A simpleXML object loaded with renewal data
     *
     * @return array             An array with the item id, success, new date (if
     * available) and system message (if available)
     * @access protected
     */
    protected function processRenewals($renewalObj)
    {
        // Not Sure Why, but necessary!
        $renewal = $renewalObj->children();
        $node = "reply-text";
        $reply = (string)$renewal->$node;

        // Valid Response
        if ($reply == "ok") {
            $loan = $renewal->renewal->institution->loan;
            $itemId = (string)$loan->itemId;
            $renewalStatus = (string)$loan->renewalStatus;

            $response['item_id'] = $itemId;
            $response['sysMessage'] = $renewalStatus;

            if ($renewalStatus == "Success") {
                $dueDate = (string)$loan->dueDate;
                if (!empty($dueDate)) {
                    // Convert Voyager Format to display format
                    $newDate = $this->dateFormat->convertToDisplayDate(
                        "Y-m-d H:i", $dueDate
                    );
                    $newTime = $this->dateFormat->convertToDisplayTime(
                        "Y-m-d H:i", $dueDate
                    );
                    if (!PEAR::isError($newDate)) {
                        $response['new_date'] = $newDate;
                    }
                    if (!PEAR::isError($newTime)) {
                        $response['new_time'] = $newTime;
                    }
                }
                $response['success'] = true;
            } else {
                $response['success'] = false;
                $response['new_date'] = false;
                $response['new_time'] = false;
            }

            return $response;
        } else {
            // System Error
            return false;
        }
    }

    /**
     * Check Item Requests
     *
     * Determines if a user can place a hold or recall on a specific item
     *
     * @param string $patronId The user's Patron ID
     * @param string $request  The request type (hold or recall)
     * @param string $bibId    An item's Bib ID
     * @param string $itemId   An item's Item ID (optional)
     *
     * @return boolean         true if the request can be made, false if it cannot
     * @access protected
     */
    protected function checkItemRequests($patronId, $request, $bibId,
        $itemId = false
    ) {
        if (!empty($bibId) && !empty($patronId) && !empty($request) ) {

            $hierarchy = array();

            // Build Hierarchy
            $hierarchy['record'] = $bibId;

            if ($itemId) {
                $hierarchy['items'] = $itemId;
            }

            $hierarchy[$request] = false;

            // Add Required Params
            $params = array(
                "patron" => $patronId,
                "patron_homedb" => $this->ws_patronHomeUbId,
                "view" => "full"
            );

            $check = $this->makeRequest($hierarchy, $params, "GET", false);

            if ($check) {
                // Process
                $check = $check->children();
                $node = "reply-text";
                $reply = (string)$check->$node;

                // Valid Response
                if ($reply == "ok") {
                    if ($check->$request ) {
                        $requestAttributes = $check->$request->attributes();
                        if ($requestAttributes['allowed'] == "Y") {
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    /**
     * Make Item Requests
     *
     * Places a Hold or Recall for a particular item
     *
     * @param string $patronId    The user's Patron ID
     * @param string $request     The request type (hold or recall)
     * @param string $level       The request level (title or copy)
     * @param array  $requestData An array of data to submit with the request,
     * may include comment, lastInterestDate and pickUpLocation
     * @param string $bibId       An item's Bib ID
     * @param string $itemId      An item's Item ID (optional)
     *
     * @return array             An array of data from the attempted request
     * including success, status and a System Message (if available)
     * @access protected
     */
    protected function makeItemRequests($patronId, $request, $level,
        $requestData, $bibId, $itemId = false
    ) {
        $response = array('success' => false, 'status' =>"hold_error_fail");

        if (!empty($bibId) && !empty($patronId) && !empty($requestData)
            && !empty($request)
        ) {
            $hierarchy = array();

            // Build Hierarchy
            $hierarchy['record'] = $bibId;

            if ($itemId) {
                $hierarchy['items'] = $itemId;
            }

            $hierarchy[$request] = false;

            // Add Required Params
            $params = array(
                "patron" => $patronId,
                "patron_homedb" => $this->ws_patronHomeUbId,
                "view" => "full"
            );

            if ("title" == $level) {
                $xmlParameter = ("recall" == $request)
                    ? "recall-title-parameters" : "hold-title-parameters";
                $request = $request . "-title";
            } else {
                $xmlParameter = ("recall" == $request)
                    ? "recall-parameters" : "hold-request-parameters";
            }


            $xml[$xmlParameter] = array(
                "pickup-location" => $requestData['pickupLocation'],
                "last-interest-date" => $requestData['lastInterestDate'],
                "comment" => $requestData['comment'],
                "dbkey" => $this->ws_dbKey
            );

            // Generate XML
            $requestXML = $this->buildBasicXML($xml);

            // Get Data
            $result = $this->makeRequest($hierarchy, $params, "PUT", $requestXML);

            if ($result) {
                // Process
                $result = $result->children();
                $node = "reply-text";
                $reply = (string)$result->$node;

                $responseNode = "create-".$request;
                $note = (isset($result->$responseNode))
                    ? trim((string)$result->$responseNode->note) : false;

                // Valid Response
                if ($reply == "ok" && $note == "Your request was successful.") {
                    $response['success'] = true;
                    $response['status'] = "hold_success";
                } else {
                    // Failed
                    $response['sysMessage'] = $note;
                }
            }
        }
        return $response;
    }

    /**
     * Determine Hold Type
     *
     * Determines if a user can place a hold or recall on a particular item
     *
     * @param string $patronId The user's Patron ID
     * @param string $bibId    An item's Bib ID
     * @param string $itemId   An item's Item ID (optional)
     *
     * @return string The name of the request method to use or false on
     * failure
     * @access protected
     */
    protected function determineHoldType($patronId, $bibId, $itemId = false)
    {
        if ($itemId && isset($this->config['Holds']['enableItemHolds']) && !$this->config['Holds']['enableItemHolds']) {
            return false;
        }

        // Check for account Blocks
        if ($this->checkAccountBlocks($patronId)) {
            return "block";
        }

        // Check Recalls First
        $recall = false;
        if (!isset($this->config['Holds']['enableRecalls']) || $this->config['Holds']['enableRecalls']) {
            $recall = $this->checkItemRequests($patronId, "recall", $bibId, $itemId);
        }
        if ($recall) {
            return "recall";
        } else {
            // Check Holds
            $hold = $this->checkItemRequests($patronId, "hold", $bibId, $itemId);
            if ($hold) {
                return "hold";
            }
        }
        return false;
    }

    /**
     * Hold Error
     *
     * Returns a Hold Error Message
     *
     * @param string $msg An error message string
     *
     * @return array An array with a success (boolean) and sysMessage key
     * @access protected
     */
    protected function holdError($msg)
    {
        return array(
                    "success" => false,
                    "sysMessage" => $msg
        );
    }

    /**
     * Place Hold
     *
     * Attempts to place a hold or recall on a particular item and returns
     * an array with result details or a PEAR error on failure of support classes
     *
     * @param array $holdDetails An array of item and patron data
     *
     * @return mixed An array of data on the request including
     * whether or not it was successful and a system message (if available) or a
     * PEAR error on failure of support classes
     * @access public
     */
    public function placeHold($holdDetails)
    {
        $patron = $holdDetails['patron'];
        $type = isset($holdDetails['holdtype']) && !empty($holdDetails['holdtype'])
            ? $holdDetails['holdtype'] : "auto";
        $level = isset($holdDetails['level']) && !empty($holdDetails['level'])
            ? $holdDetails['level'] : "copy";
        $pickUpLocation = !empty($holdDetails['pickUpLocation'])
            ? $holdDetails['pickUpLocation'] : $this->defaultPickUpLocation;
        $itemId = isset($holdDetails['item_id']) ? $holdDetails['item_id'] : false;
        $comment = $holdDetails['comment'];
        $bibId = $holdDetails['id'];
        // Request was initiated before patron was logged in -
        // Let's determine Hold Type now
        if ($type == "auto") {
            $type = $this->determineHoldType($patron['id'], $bibId, $itemId);
            if (!$type || $type == "block") {
                return $this->holdError("hold_error_blocked");
            }
        }

        // Convert last interest date from Display Format to Voyager required format
        $lastInterestDate = $this->dateFormat->convertFromDisplayDate(
            "Y-m-d", $holdDetails['requiredBy']
        );
        if (PEAR::isError($lastInterestDate)) {
            // Hold Date is invalid
            return $this->holdError("hold_date_invalid");
        }

        $checkTime =  $this->dateFormat->convertFromDisplayDate(
            "U", $holdDetails['requiredBy']
        );
        if (PEAR::isError($checkTime) || !is_numeric($checkTime)) {
            return $checkTime;
        }

        if (time() > $checkTime) {
            // Hold Date is in the past
            return $this->holdError("hold_date_past");
        }

        // Make Sure Pick Up Library is Valid
        $pickUpValid = false;
        $pickUpLibs = $this->getPickUpLocations($patron, $holdDetails);
        foreach ($pickUpLibs as $location) {
            if ($location['locationID'] == $pickUpLocation) {
                $pickUpValid = true;
            }
        }
        if (!$pickUpValid) {
            // Invalid Pick Up Point
            return $this->holdError("hold_invalid_pickup");
        }

        // Build Request Data
        $requestData = array(
            'pickupLocation' => $pickUpLocation,
            'lastInterestDate' => $lastInterestDate,
            'comment' => $comment
        );

        if ($this->checkItemRequests($patron['id'], $type, $bibId, $itemId)) {
            // Attempt Request
            $result = $this->makeItemRequests(
                $patron['id'], $type, $level, $requestData, $bibId, $itemId
            );
            if ($result) {
                return $result;
            }
        }
        return $this->holdError("hold_error_blocked");
    }

    /**
     * Cancel Holds
     *
     * Attempts to Cancel a hold or recall on a particular item. The
     * data in $cancelDetails['details'] is determined by getCancelHoldDetails().
     *
     * @param array $cancelDetails An array of item and patron data
     *
     * @return array               An array of data on each request including
     * whether or not it was successful and a system message (if available)
     * @access public
     */
    public function cancelHolds($cancelDetails)
    {
        $details = $cancelDetails['details'];
        $patron = $cancelDetails['patron'];
        $count = 0;
        $response = array();

        foreach ($details as $cancelDetails) {
            list($itemId, $cancelCode) = explode("|", $cancelDetails);

             // Create Rest API Cancel Key
            $cancelID = $this->ws_dbKey. "|" . $cancelCode;

            // Build Hierarchy
            $hierarchy = array(
                "patron" => $patron['id'],
                 "circulationActions" => "requests",
                 "holds" => $cancelID
            );

            // Add Required Params
            $params = array(
                "patron_homedb" => $this->ws_patronHomeUbId,
                "view" => "full"
            );

            // Get Data
            $cancel = $this->makeRequest($hierarchy, $params, "DELETE");

            if ($cancel) {

                // Process Cancel
                $cancel = $cancel->children();
                $node = "reply-text";
                $reply = (string)$cancel->$node;
                $count = ($reply == "ok") ? $count+1 : $count;

                $response[$itemId] = array(
                    'success' => ($reply == "ok") ? true : false,
                    'status' => ($reply == "ok")
                        ? "hold_cancel_success" : "hold_cancel_fail",
                    'sysMessage' => ($reply == "ok") ? false : $reply,
                );

            } else {
                $response[$itemId] = array(
                    'success' => false, 'status' => "hold_cancel_fail"
                );
            }
        }
        $result = array('count' => $count, 'items' => $response);
        return $result;
    }

    /**
     * Get Cancel Hold Details
     *
     * In order to cancel a hold, Voyager requires the patron details an item ID
     * and a recall ID. This function returns the item id and recall id as a string
     * separated by a pipe, which is then submitted as form data in Hold.php. This
     * value is then extracted by the CancelHolds function.
     *
     * @param array $holdDetails An array of item data
     *
     * @return string Data for use in a form field
     * @access public
     */
    public function getCancelHoldDetails($holdDetails)
    {
        $cancelDetails = $holdDetails['item_id']."|".$holdDetails['reqnum'];
        return $cancelDetails;
    }

    /**
     * Get Renew Details
     *
     * In order to renew an item, Voyager requires the patron details and an item
     * id. This function returns the item id as a string which is then used
     * as submitted form data in checkedOut.php. This value is then extracted by
     * the RenewMyItems function.
     *
     * @param array $checkOutDetails An array of item data
     *
     * @return string Data for use in a form field
     * @access public
     */
    public function getRenewDetails($checkOutDetails)
    {
        $renewDetails = $checkOutDetails['item_id'];
        return $renewDetails;
    }

    /**
     * Get Patron Profile
     *
     * This is responsible for retrieving the profile for a specific patron.
     *
     * @param array $patron The patron array
     *
     * @return mixed        Array of the patron's profile data on success,
     * PEAR_Error otherwise.
     * @access public
     */
    public function getMyProfile($patron)
    {
        $result = parent::getMyProfile($patron);
        if ($result) {
            $result['blocks'] = $this->checkAccountBlocks($patron['id']);
        }
        return $result;
    }
    

    /**
     * Place Call Slip Request
     *
     * Attempts to place a call slip request on a particular item and returns
     * an array with result details or a PEAR error on failure of support classes
     *
     * @param array $details An array of item and patron data
     *
     * @return mixed An array of data on the request including
     * whether or not it was successful and a system message (if available) or a
     * PEAR error on failure of support classes
     * @access public
     */
    public function placeCallSlipRequest($details)
    {
        $patron = $details['patron'];
        $level = isset($details['level']) && !empty($details['level'])
            ? $details['level'] : 'copy';
        $itemId = isset($details['item_id']) ? $details['item_id'] : false;
        $mfhdId = isset($details['mfhd_id']) ? $details['mfhd_id'] : false;
        $comment = $details['comment'];
        $bibId = $details['id'];

        // Attempt Request
        $hierarchy = array();

        // Build Hierarchy
        $hierarchy['record'] = $bibId;

        if ($itemId && $level != 'title') {
            $hierarchy['items'] = $itemId;
        }

        $hierarchy['callslip'] = false;

        // Add Required Params
        $params = array(
            'patron' => $patron['id'],
            'patron_homedb' => $this->ws_patronHomeUbId,
            'view' => 'full'
        );

        if ('title' == $level) {
            $xml['call-slip-title-parameters'] = array(
                'comment' => $comment,
                'reqinput field="1"' => $details['volume'],
                'reqinput field="2"' => $details['issue'],
                'reqinput field="3"' => $details['year'],
                'dbkey' => $this->ws_dbKey,
                'mfhdId' => $mfhdId 
            );
        } else {
            $xml['call-slip-parameters'] = array(
                'comment' => $comment,
                'dbkey' => $this->ws_dbKey,
            );
        }
        
        // Generate XML
        $requestXML = $this->buildBasicXML($xml);

        // Get Data
        $result = $this->makeRequest($hierarchy, $params, "PUT", $requestXML);

        if ($result) {
            // Process
            $result = $result->children();
            $reply = (string)$result->{'reply-text'};

            $responseNode = 'title' == $level ? 'create-call-slip-title' : 'create-call-slip';
            $note = (isset($result->$responseNode))
                ? trim((string)$result->$responseNode->note) : false;

            // Valid Response
            if ($reply == "ok" && $note == "Your request was successful.") {
                $response['success'] = true;
                $response['status'] = "call_slip_success";
            } else {
                // Failed
                $response['sysMessage'] = $note;
            }
            return $response;
        }
        
        return $this->holdError('call_slip_error_blocked');
    }

    /**
     * Cancel Call Slips
     *
     * Attempts to Cancel a call slip on a particular item. The
     * data in $cancelDetails['details'] is determined by getCancelCallSlipDetails().
     *
     * @param array $cancelDetails An array of item and patron data
     *
     * @return array               An array of data on each request including
     * whether or not it was successful and a system message (if available)
     * @access public
     */
    public function cancelCallSlips($cancelDetails)
    {
        $details = $cancelDetails['details'];
        $patron = $cancelDetails['patron'];
        $count = 0;
        $response = array();

        foreach ($details as $cancelDetails) {
            list($itemId, $cancelCode) = explode("|", $cancelDetails);

             // Create Rest API Cancel Key
            $cancelID = $this->ws_dbKey. "|" . $cancelCode;

            // Build Hierarchy
            $hierarchy = array(
                "patron" => $patron['id'],
                 "circulationActions" => 'requests',
                 "callslips" => $cancelID
            );

            // Add Required Params
            $params = array(
                "patron_homedb" => $this->ws_patronHomeUbId,
                "view" => "full"
            );

            // Get Data
            $cancel = $this->makeRequest($hierarchy, $params, "DELETE");

            if ($cancel) {

                // Process Cancel
                $cancel = $cancel->children();
                $node = "reply-text";
                $reply = (string)$cancel->$node;
                $count = ($reply == "ok") ? $count+1 : $count;

                $response[$itemId] = array(
                    'success' => ($reply == "ok") ? true : false,
                    'status' => ($reply == "ok")
                        ? "call_slip_cancel_success" : "call_slip_cancel_fail",
                    'sysMessage' => ($reply == "ok") ? false : $reply,
                );

            } else {
                $response[$itemId] = array(
                    'success' => false, 'status' => "call_slip_cancel_fail"
                );
            }
        }
        $result = array('count' => $count, 'items' => $response);
        return $result;
    }
    
    /**
     * Get Cancel Call Slip Details
     *
     * In order to cancel a call slip, Voyager requires the patron details an item ID
     * and a recall ID. This function returns the item id and call slip id as a string
     * separated by a pipe, which is then submitted as form data in CallSlip.php. This
     * value is then extracted by the CancelCallSlips function.
     *
     * @param array $details An array of item data
     *
     * @return string Data for use in a form field
     * @access public
     */
    public function getCancelCallSlipDetails($details)
    {
        $details = $details['item_id']."|".$details['reqnum'];
        return $details;
    }

    /**
     * Change Password
     *
     * Attempts to change patron password (PIN code)
     *
     * @param array $details An array of patron id and old and new password:
     * 
     * 'patron'      The patron array from patronLogin
     * 'oldPassword' Old password
     * 'newPassword' New password
     *
     * @return mixed An array of data on the request including
     * whether or not it was successful and a system message (if available) or a
     * PEAR error on failure of support classes 
     * @access public
     */
    public function changePassword($details)
    {
        $patron = $details['patron'];
        $id = htmlspecialchars($patron['id'], ENT_COMPAT, 'UTF-8');
        $lastname = htmlspecialchars($patron['lastname'], ENT_COMPAT, 'UTF-8');
        $ubId = htmlspecialchars($this->ws_patronHomeUbId, ENT_COMPAT, 'UTF-8');
        $oldPIN = trim(htmlspecialchars($details['oldPassword'], ENT_COMPAT, 'UTF-8'));
        if ($oldPIN === '') {
            // Voyager requires the PIN code to be set even 
            $oldPIN = '     ';
        }
        $newPIN = trim(htmlspecialchars($details['newPassword'], ENT_COMPAT, 'UTF-8'));
        $barcode = htmlspecialchars($patron['cat_username'], ENT_COMPAT, 'UTF-8');
        
        $xml =  <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<ser:serviceParameters xmlns:ser="http://www.endinfosys.com/Voyager/serviceParameters">
   <ser:parameters>
      <ser:parameter key="oldPatronPIN">
         <ser:value>$oldPIN</ser:value>
      </ser:parameter>
      <ser:parameter key="newPatronPIN">
         <ser:value>$newPIN</ser:value>
      </ser:parameter>
   </ser:parameters>
   <ser:patronIdentifier lastName="$lastname" patronHomeUbId="$ubId" patronId="$id">
      <ser:authFactor type="B">$barcode</ser:authFactor>
   </ser:patronIdentifier>
</ser:serviceParameters>               
EOT;
        
        $result = $this->makeRequest(array('ChangePINService' => false), array(), 'POST', $xml);
        
        $result->registerXPathNamespace('ser', 'http://www.endinfosys.com/Voyager/serviceParameters');
        $error = $result->xpath("//ser:message[@type='error']");
        if (!empty($error)) {
            $error = reset($error);
            if ($error->attributes()->errorCode == 'com.endinfosys.voyager.patronpin.PatronPIN.ValidateException') {
                return array('success' => false, 'status' => 'change_password_error_old_wrong');
            }
            if ($error->attributes()->errorCode == 'com.endinfosys.voyager.patronpin.PatronPIN.ValidateUniqueException') {
                return array('success' => false, 'status' => 'change_password_error_code_not_unique');
            }
            if ($error->attributes()->errorCode == 'com.endinfosys.voyager.patronpin.PatronPIN.ValidateLengthException') {
                return array('success' => false, 'status' => 'change_password_error_invalid_length');
            }
            return new PEAR_Error((string)$error);
        }
        return array('success' => true, 'status' => 'change_password_ok');
    }
    
}

?>
