<?php
/**
 * Multiple Backend Driver.
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
 * @package  ILS_Drivers
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_an_ils_driver Wiki
 */
require_once 'Interface.php';

/**
 * Multiple Backend Driver.
 *
 * This driver allows to use multiple backends determined by a record id or 
 * user id prefix (e.g. source.12345). 
 *
 * @category VuFind
 * @package  ILS_Drivers
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_an_ils_driver Wiki
 
 */
class MultiBackend implements DriverInterface
{
    protected $config = null;
    
    /**
     * Constructor
     *
     * @access public
     */
    public function __construct()
    {
        // Load Configuration for this Module
        $this->config = getExtraConfigArray('MultiBackend');
    }
    
    /**
     * Get the drivers (data source IDs) enabled in MultiBackend for login
     * 
     * @return string[]
     */
    public function getLoginDrivers()
    {
        $drivers = array();
        foreach ($this->config as $id => $driver) {
            if (isset($driver['login']) && $driver['login']) {
                $drivers[] = $id;
            }
        }
        return $drivers;
    }

    /**
     * Get the default driver (data source ID) for login
     * 
     * @return string
     */
    public function getDefaultLoginDriver()
    {
        return isset($this->config['General']['defaultLoginDriver']) ? $this->config['General']['defaultLoginDriver'] : $this->config['General']['defaultDriver'];
    }

    /**
     * Get Status
     *
     * This is responsible for retrieving the status information of a certain
     * record.
     *
     * @param string $id The record id to retrieve the holdings for
     *
     * @return mixed     On success, an associative array with the following keys:
     * id, availability (boolean), status, location, reserve, callnumber; on
     * failure, a PEAR_Error.
     * @access public
     */
    public function getStatus($id)
    {
        return $this->getHolding($id);
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
     * a PEAR_Error object otherwise.
     * @access public
     */
    public function getStatuses($ids)
    {
        $items = array();
        foreach ($ids as $id) {
            $items[] = $this->getHolding($id);
        }
        return $items;
    }
    
    /**
     * Get Holding
     *
     * This is responsible for retrieving the holding information of a certain
     * record.
     *
     * @param string $id     The record id to retrieve the holdings for
     * @param array  $patron Patron data
     *
     * @return mixed     On success, an associative array with the following keys:
     * id, availability (boolean), status, location, reserve, callnumber, duedate,
     * number, barcode; on failure, a PEAR_Error.
     * @access public
     */
    public function getHolding($id, $patron = false)
    {   
        $source = $this->getSource($id);    
        $driver = $this->getDriver($source);
        if ($driver) {
            $holdings = $driver->getHolding($this->getLocalId($id), $patron ? $this->stripIdPrefixes($patron, $source) : false);
            if ($holdings) {
                return $this->addIdPrefixes($holdings, $source);
            }
        }
        return array();
    }
    
    /**
     * Get Purchase History
     *
     * This is responsible for retrieving the acquisitions history data for the
     * specific record (usually recently received issues of a serial).
     *
     * @param string $id The record id to retrieve the info for
     *
     * @return mixed     An array with the acquisitions data on success, PEAR_Error
     * on failure
     * @access public
     */
    public function getPurchaseHistory($id)
    {
        $source = $this->getSource($id);    
        $driver = $this->getDriver($source);
        if ($driver) {
            return $driver->getPurchaseHistory($this->getLocalId($id));
        }
        return array();
    }
    
    /**
     * Get New Items
     *
     * Retrieve the IDs of items recently added to the catalog.
     *
     * @param int $page    Page number of results to retrieve (counting starts at 1)
     * @param int $limit   The size of each page of results to retrieve
     * @param int $daysOld The maximum age of records to retrieve in days (max. 30)
     * @param int $fundId  optional fund ID to use for limiting results (use a value
     * returned by getFunds, or exclude for no limit); note that "fund" may be a
     * misnomer - if funds are not an appropriate way to limit your new item
     * results, you can return a different set of values from getFunds. The
     * important thing is that this parameter supports an ID returned by getFunds,
     * whatever that may mean.
     *
     * @return array       Associative array with 'count' and 'results' keys
     * @access public
     */
    public function getNewItems($page, $limit, $daysOld, $fundId = null)
    {
        $driver = $this->getDriver($this->config['General']['defaultDriver']);
        if ($driver) {
            return $driver->getNewItems($page, $limit, $daysOld, $fundId);
        }
        return array();
    }
    
    /**
     * Find Reserves
     *
     * Obtain information on course reserves.
     *
     * @param string $course ID from getCourses (empty string to match all)
     * @param string $inst   ID from getInstructors (empty string to match all)
     * @param string $dept   ID from getDepartments (empty string to match all)
     *
     * @return mixed An array of associative arrays representing reserve items
     * (or a PEAR_Error object if there is a problem)
     * @access public
     */
    public function findReserves($course, $inst, $dept)
    {
        $driver = $this->getDriver($this->config['General']['defaultDriver']);
        if ($driver) {
            return $driver->findReserves($course, $inst, $dept);
        }
        return array();
    }
    
    /**
     * Get Patron Profile
     *
     * This is responsible for retrieving the profile for a specific patron.
     *
     * @param array $user The patron array
     *
     * @return mixed      Array of the patron's profile data on success,
     * PEAR_Error otherwise.
     * @access public
     */
    public function getMyProfile($user)
    {
        $source = $this->getSource($user['cat_username']);
        $driver = $this->getDriver($source);
        if ($driver) {
            $profile = $driver->getMyProfile($this->stripIdPrefixes($user, $source));
            return $this->addIdPrefixes($profile, $source);
        }
        return array();
    }
    
    /**
     * Patron Login
     *
     * This is responsible for authenticating a patron against the catalog.
     *
     * @param string $username The patron user id or barcode
     * @param string $password The patron password
     *
     * @return mixed           Associative array of patron info on successful login,
     * null on unsuccessful login, PEAR_Error on error.
     * @access public
     */
    public function patronLogin($username, $password)
    {
        $hash = md5($username . $password);
        if (isset($_SESSION['logins'][$hash])) {
            return unserialize($_SESSION['logins'][$hash]);
        }
        $source = $this->getSource($username);
        if (!$source) {
            $source = $this->getDefaultLoginDriver();
        }
        $driver = $this->getDriver($source);
        if ($driver) {
            $patron = $driver->patronLogin($this->getLocalId($username), $password);
            $patron = $this->addIdPrefixes($patron, $source);
            $_SESSION['logins'][$username] = serialize($patron);
            return $patron;
        }
        return new PEAR_Error('No suitable backend driver found');
    }
    
    /**
     * Get Patron Transactions
     *
     * This is responsible for retrieving all transactions (i.e. checked out items)
     * by a specific patron.
     *
     * @param array $user The patron array from patronLogin
     *
     * @return mixed      Array of the patron's transactions on success,
     * PEAR_Error otherwise.
     * @access public
     */
    public function getMyTransactions($user)
    {
        $source = $this->getSource($user['cat_username']);
        $driver = $this->getDriver($source);
        if ($driver) {
            $transactions = $driver->getMyTransactions($this->stripIdPrefixes($user, $source));
            return $this->addIdPrefixes($transactions, $source);
        }
        return new PEAR_Error('No suitable backend driver found');
    }
    
    /**
     * Get Renew Details
     *
     * In order to renew an item, the ILS requires information on the item and
     * patron. This function returns the information as a string which is then used
     * as submitted form data in checkedOut.php. This value is then extracted by
     * the RenewMyItems function.
     *
     * @param array $checkoutDetails An array of item data
     *
     * @return string Data for use in a form field
     * @access public
     */
    public function getRenewDetails($checkoutDetails)
    {
        $source = $this->getSource($checkoutDetails['id']);
        $driver = $this->getDriver($source);
        if ($driver) {
            $details = $driver->getRenewDetails($this->stripIdPrefixes($checkoutDetails, $source));
            return $this->addIdPrefixes($details, $source);
        } 
        return new PEAR_Error('No suitable backend driver found');
    }
    
    /**
     * Renew My Items
     *
     * Function for attempting to renew a patron's items. The data in
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
        $source = $this->getSource($renewDetails['patron']['id']);
        $driver = $this->getDriver($source);
        if ($driver) {
            $details = $driver->renewMyItems($this->stripIdPrefixes($renewDetails, $source));
            return $this->addIdPrefixes($details, $source);
        }
        return new PEAR_Error('No suitable backend driver found');
    }
    
    /**
     * Get Patron Fines
     *
     * This is responsible for retrieving all fines by a specific patron.
     *
     * @param array $user The patron array from patronLogin
     *
     * @return mixed      Array of the patron's fines on success, PEAR_Error
     * otherwise.
     * @access public
     */
    public function getMyFines($user)
    {
        $source = $this->getSource($user['cat_username']);
        $driver = $this->getDriver($source);
        if ($driver) {
            $fines = $driver->getMyFines($this->stripIdPrefixes($user, $source));
            return $this->addIdPrefixes($fines, $source);
        }
        return new PEAR_Error('No suitable backend driver found');
    }
    
    /**
     * Get Patron Holds
     *
     * This is responsible for retrieving all holds by a specific patron.
     *
     * @param array $user The patron array from patronLogin
     *
     * @return mixed      Array of the patron's holds on success, PEAR_Error
     * otherwise.
     * @access public
     */
    public function getMyHolds($user)
    {
        $source = $this->getSource($user['cat_username']);
        $driver = $this->getDriver($source);
        if ($driver) {
            $holds = $driver->getMyHolds($this->stripIdPrefixes($user, $source));
            return $this->addIdPrefixes($holds, $source);
        }
        return new PEAR_Error('No suitable backend driver found');
    }

    /**
     * Get Patron Call Slips
     *
     * This is responsible for retrieving all call slips by a specific patron.
     *
     * @param array $user The patron array from patronLogin
     *
     * @return mixed      Array of the patron's holds on success, PEAR_Error
     * otherwise.
     * @access public
     */
    public function getMyCallSlips($user)
    {
        $source = $this->getSource($user['cat_username']);
        $driver = $this->getDriver($source);
        if ($driver) {
            if (method_exists($driver, 'getMyCallSlips')) {
                $holds = $driver->getMyCallSlips($this->stripIdPrefixes($user, $source));
                return $this->addIdPrefixes($holds, $source);
            }
            return array();
        }
        return new PEAR_Error('No suitable backend driver found');
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
        $source = $this->getSource($patron['cat_username']);
        $driver = $this->getDriver($source);
        if ($driver) {
            if ($this->getSource($id) != $source) {
                return false;
            }
            return $driver->checkRequestIsValid(
                $this->stripIdPrefixes($id, $source),
                $this->stripIdPrefixes($data, $source), $this->stripIdPrefixes($patron, $source)
            );
        }
        return false;
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
        $source = $this->getSource($patron['cat_username']);
        $driver = $this->getDriver($source);
        if ($driver) {
            if ($this->getSource($id) != $source) {
                return false;
            }
            return $driver->checkCallSlipRequestIsValid(
                $this->stripIdPrefixes($id, $source),
                $this->stripIdPrefixes($data, $source), $this->stripIdPrefixes($patron, $source)
            );
        }
        return false;
    }
    
    /**
     * Get Pick Up Locations
     *
     * This is responsible get a list of valid library locations for holds / recall
     * retrieval
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
        $source = $this->getSource($patron['cat_username']);
        $driver = $this->getDriver($source);
        if ($driver) {
            if ($holdDetails) {
                if ($this->getSource($holdDetails['id']) != $source) {
                    // TODO: any other error handling?
                    return array();                 
                }
            }
            $locations = $driver->getPickUpLocations(
                $this->stripIdPrefixes($patron, $source),
                $this->stripIdPrefixes($holdDetails, $source)
            );
            return $this->addIdPrefixes($locations, $source);
        }
        return new PEAR_Error('No suitable backend driver found');
    }
    
    /**
     * Get Default Pick Up Location
     *
     * Returns the default pick up location set in HorizonXMLAPI.ini
     *
     * @param array $patron      Patron information returned by the patronLogin
     * method.
     * @param array $holdDetails Optional array, only passed in when getting a list
     * in the context of placing a hold; contains most of the same values passed to
     * placeHold, minus the patron data.  May be used to limit the pickup options
     * or may be ignored.
     *
     * @return string A location ID
     * @access public
     */
    public function getDefaultPickUpLocation($patron = false, $holdDetails = null)
    {
        $source = $this->getSource($patron['cat_username']);
        $driver = $this->getDriver($source);
        if ($driver) {
            if ($holdDetails) {
                if ($this->getSource($holdDetails['id']) != $source) {
                    // TODO: any other error handling?
                    return '';                 
                }
            }
            $locations = $driver->getDefaultPickUpLocation(
                $this->stripIdPrefixes($patron, $source),
                $this->stripIdPrefixes($holdDetails, $source)
            );
            return $this->addIdPrefixes($locations, $source);
        }
        return new PEAR_Error('No suitable backend driver found');
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
        $source = $this->getSource($holdDetails['patron']['cat_username']);
        $driver = $this->getDriver($source);
        if ($driver) {
            if ($this->getSource($holdDetails['id']) != $source) {
                return array(
                    "success" => false,
                    "sysMessage" => 'hold_wrong_user_institution'
                );                   
            }
            $holdDetails = $this->stripIdPrefixes($holdDetails, $source);
            return $driver->placeHold($holdDetails);
        }
        return new PEAR_Error('No suitable backend driver found');
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
        $source = $this->getSource($cancelDetails['patron']['cat_username']);
        $driver = $this->getDriver($source);
        if ($driver) {
            return $driver->cancelHolds($this->stripIdPrefixes($cancelDetails, $source));
        }
        return new PEAR_Error('No suitable backend driver found');
    }
    
    /**
     * Get Cancel Hold Details
     *
     * In order to cancel a hold, the ILS requires some information on the hold.
     * This function returns the required information, which is then submitted 
     * as form data in Hold.php. This value is then extracted by the CancelHolds
     * function.
     *
     * @param array $holdDetails An array of item data
     *
     * @return string Data for use in a form field
     * @access public
     */
    public function getCancelHoldDetails($holdDetails)
    {
        $source = $this->getSource($holdDetails['id']);
        $driver = $this->getDriver($source);
        if ($driver) {
            $holdDetails = $this->stripIdPrefixes($holdDetails, $source);
            return $driver->getCancelHoldDetails($holdDetails);
        }
        return new PEAR_Error('No suitable backend driver found');
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
        $source = $this->getSource($details['patron']['cat_username']);
        $driver = $this->getDriver($source);
        if ($driver) {
            if ($this->getSource($details['id']) != $source) {
                return array(
                    "success" => false,
                    "sysMessage" => 'hold_wrong_user_institution'
                );                   
            }
            $details = $this->stripIdPrefixes($details, $source);
            return $driver->placeCallSlipRequest($details);
        }
        return new PEAR_Error('No suitable backend driver found');
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
        $source = $this->getSource($cancelDetails['patron']['cat_username']);
        $driver = $this->getDriver($source);
        if ($driver) {
            return $driver->cancelCallSlips($this->stripIdPrefixes($cancelDetails, $source));
        }
        return new PEAR_Error('No suitable backend driver found');
    }
    
    /**
     * Get Cancel Call Slip Details
     *
     * In order to cancel a call slip, the ILS requires some information on it.
     * This function returns the required information, which is then submitted 
     * as form data in CallSlip.php. This value is then extracted by the CancelCallSlips
     * function.
     *
     * @param array $details An array of item data
     *
     * @return string Data for use in a form field
     * @access public
     */
    public function getCancelCallSlipDetails($details)
    {
        $source = $this->getSource($details['id']);
        $driver = $this->getDriver($source);
        if ($driver) {
            $details = $this->stripIdPrefixes($details, $source);
            return $driver->getCancelCallSlipDetails($details);
        }
        return new PEAR_Error('No suitable backend driver found');
    }
    
    /**
     * Change Password
     *
     * Attempts to change patron password (PIN code)
     *
     * @param array $details An array of patron id and old and new password
     *
     * @return mixed An array of data on the request including
     * whether or not it was successful and a system message (if available) or a
     * PEAR error on failure of support classes 
     * @access public
     */
    public function changePassword($details)
    {
        $source = $this->getSource($details['patron']['cat_username']);
        $driver = $this->getDriver($source);
        if ($driver) {
            $details = $this->stripIdPrefixes($details, $source);
            return $driver->changePassword($details);
        }
        return new PEAR_Error('No suitable backend driver found');
    }
    
    /**
     * Function which specifies renew, hold and cancel settings.
     *
     * @param string $function The name of the feature to be checked
     * @param string $id       Optional record id
     *
     * @return array An array with key-value pairs.
     * @access public
     */
    public function getConfig($function, $id = null)
    {
        global $user;
        
        $source = null;
        if ($id) {
            $source = $this->getSource($id);
        }
        if (!$source && $user && isset($user->cat_username)) {
            $source = $this->getSource($user->cat_username);
        }
        
        $driver = $this->getDriver($source);
        
        // If we have resolved the needed driver, just getConfig and return.
        if ($driver && method_exists($driver, 'getConfig')) {
            return $driver->getConfig($function);
        }
        
        // If driver not available, return default values
        switch ($function) {
        case 'Holds':
            return Array(
                'function' => 'placeHold',
                'HMACKeys' => 'id',
                'extraHoldFields' => 'requiredByDate:pickUpLocation',
                'defaultRequiredDate' => '1:0:0'
            );
        case 'cancelHolds':
            return Array(
                'function' => 'cancelHolds',
                'HMACKeys' => 'id'
            );
        case 'Renewals':
        case 'CallSlips':
            return Array();
        default:
            error_log("MultiBackend: unhandled getConfig function: '$function'");
        }
        return Array();
    }
        
    /**
     * Extract local ID from the given prefixed ID
     * 
     * @param string $id Prefixed ID
     * 
     * @return string Local ID 
     */
    protected function getLocalId($id)
    {
        $pos = strpos($id, '.');
        if ($pos > 0) {
            return substr($id, $pos + 1);
        }
        error_log("MultiBackend: Can't find local id in '$id'");
        return $id;
    }

    /**
     * Extract source from the given ID
     * 
     * @param string $id Prefixed ID
     * 
     * @return string Source
     */
    protected function getSource($id)
    {
        $pos = strpos($id, '.');
        if ($pos > 0) {
            return substr($id, 0, $pos);
        }
        error_log("MultiBackend: Can't find source id in '$id'");
        return '';
    }

    /**
     * Find the correct driver for the given source
     * 
     * @param string $source Source
     * 
     * @return mixed On success a driver object, otherwise null
     */
    protected function getDriver($source)
    {
        $source = strtolower($source);
        if (isset($this->config[$source])) {
            $driver = $this->config[$source]['driver'];
            try {
                include_once "{$driver}.php";
                return new $driver("{$driver}_{$source}.ini");
            } catch (Exception $e) {
                error_log("MultiBackend: error initializing driver '$driver': " . $e->__toString());
                return null;
            }
        }
        return null;
    }

    /**
     * Change local ID's to global ID's in the given array
     * 
     * @param mixed  $data         The data to be modified, normally
     *                             array or array of arrays
     * @param string $source       Source code
     * @param array  $modifyFields Fields to be modified in the array
     * 
     * @return mixed  Modified array or empty/null if that input was 
     *                empty/null
     */
    protected function addIdPrefixes($data, $source, $modifyFields = array('id', 'cat_username'))
    {
        if (!isset($data) || empty($data) || PEAR::isError($data)) {
            return $data;
        }
        $array = is_array($data) ? $data : array($data);
    
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = $this->addIdPrefixes(
                    $value, $source, $modifyFields
                );
            } else {
                if (!is_numeric($key) && in_array($key, $modifyFields)) {
                    $array[$key] = $source . '.' . $value; 
                }
            }
        }
        return is_array($data) ? $array : $array[0];
    }

    /**
     * Change global ID's to local ID's in the given array
     * 
     * @param mixed  $data         The data to be modified, normally
     *                             array or array of arrays
     * @param string $source       Source code
     * @param array  $modifyFields Fields to be modified in the array
     * 
     * @return mixed Modified array or empty/null if that input was
     *               empty/null
     */
    protected function stripIdPrefixes($data, $source, $modifyFields = array('id', 'cat_username'))
    {
        if (!isset($data) || empty($data)) {
            return $data;
        }
        $array = is_array($data) ? $data : array($data);
    
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = $this->stripIdPrefixes(
                    $value, $source, $modifyFields
                );
            } else {
                if (in_array($key, $modifyFields) 
                    && strncmp($source . '.', $value, strlen($source) + 1) == 0
                ) {
                    $array[$key] = substr($value, strlen($source) + 1);
                }
            }
        }
        return is_array($data) ? $array : $array[0];
    }

}
    
