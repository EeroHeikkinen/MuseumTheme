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
	protected $_defaultDriver = '';
	protected $_drivers = array();
	
    /**
     * Constructor
     *
     * @access public
     */
    public function __construct()
    {
    	// Load Configuration for this Module
        $configArray = parse_ini_file(
            dirname(__FILE__) . '/../conf/MultiBackend.ini', true
        );

        $this->_defaultDriver = $configArray['General']['defaultDriver'];
        $this->_drivers = $configArray['Drivers'];
    }

    public function getStatus($id)
    {
    	return $this->getHolding($id);
    }
    
    public function getStatuses($ids)
    {
    	$items = array();
    	foreach ($ids as $id) {
    		$items[] = $this->getHolding($id);
    	}
    	return $items;
    }
    
    public function getHolding($id, $patron = false)
    {   
    	$source = $this->_getSource($id);	
    	$driver = $this->_getDriver($source);
    	if ($driver) {
    		$holdings = $driver->getHolding($this->_getLocalId($id), $patron);
    		if ($holdings) {
    		    return $this->_addIdPrefixes($holdings, $source);
    		}
    	}
    	else {
    		error_log("No driver for '$id' found");
    	}
		return Array();
    }
    
    public function getPurchaseHistory($id)
    {
    	$source = $this->_getSource($id);	
    	$driver = $this->_getDriver($source);
    	if ($driver) {
    		return $driver->getPurchaseHistory($this->_getLocalId($id));
    	}
    	error_log("No driver for '$id' found");
    }
    
    public function getNewItems($page, $limit, $daysOld, $fundId = null)
    {
    	$driver = $this->_getDriver($this->_defaultDriver);
    	if ($driver) {
    		return $driver->getNewItems($page, $limit, $daysOld, $fundId);
    	}
    	error_log("No driver for '$id' found");
    }
    
    public function findReserves($course, $inst, $dept)
    {
    	$driver = $this->_getDriver($this->_defaultDriver);
    	if ($driver) {
    		return $driver->findReserves($course, $inst, $dept);
    	}
    	error_log("No driver for '$id' found");
    }
    
    public function getMyProfile($user)
    {
    	$source = $this->_getSource($user['cat_username']);
    	$driver = $this->_getDriver($source);
    	if ($driver) {
    		$profile = $driver->getMyProfile($this->_stripIdPrefixes($user, $source));
    		return $this->_addIdPrefixes($profile, $source);
    	}
    	error_log("No driver for '$user' found");
    }
    
    public function patronLogin($username, $password)
    {
    	$source = $this->_getSource($username);
        if (!$source) {
    	    $source = $this->_defaultDriver;
    	}
    	$driver = $this->_getDriver($source);
    	if ($driver) {
    	    $patron = $driver->patronLogin($this->_getLocalId($username), $password);
    		return $this->_addIdPrefixes($patron, $source);
    	}
    	error_log("No driver for '$username' found");
    }
    
    public function getMyTransactions($user)
    {
    	$source = $this->_getSource($user['cat_username']);
    	$driver = $this->_getDriver($source);
    	if ($driver) {
    		$transactions = $driver->getMyTransactions($this->_stripIdPrefixes($user, $source));
    		return $this->_addIdPrefixes($transactions, $source);
        }
    	error_log("No driver for '$user' found");
    }
    
    public function getRenewDetails($checkoutDetails)
    {
	    $source = $this->_getSource($checkoutDetails['id']);
	    $driver = $this->_getDriver($source);
    	if ($driver) {
    		$details = $driver->getRenewDetails($this->_stripIdPrefixes($checkoutDetails, $source));
    		return $this->_addIdPrefixes($details, $source);
    	} 
	    error_log("No driver for '$id' found");
    }
    
    public function renewMyItems($renewDetails)
    {
	    $source = $this->_getSource($renewDetails['patron']['id']);
    	$driver = $this->_getDriver($source);
    	if ($driver) {
    		$details = $driver->renewMyItems($this->_stripIdPrefixes($renewDetails, $source));
    		return $this->_addIdPrefixes($details, $source);
    	}
    	error_log("No driver for '$id' found");
    }
    
    public function getMyFines($user)
    {
    	$source = $this->_getSource($user['cat_username']);
    	$driver = $this->_getDriver($source);
    	if ($driver) {
    		$fines = $driver->getMyFines($this->_stripIdPrefixes($user, $source));
    		return $this->_addIdPrefixes($fines, $source);
    	}
    	error_log("No driver for '$user' found");
    }
    
    public function getMyHolds($user)
    {
    	$source = $this->_getSource($user['cat_username']);
    	$driver = $this->_getDriver($source);
    	if ($driver) {
    		$holds = $driver->getMyHolds($this->_stripIdPrefixes($user, $source));
    		return $this->_addIdPrefixes($holds, $source);
    	}
    	error_log("No driver for '$user' found");
    }
    
    public function getPickUpLocations($user)
    {
    	$source = $this->_getSource($user['cat_username']);
    	$driver = $this->_getDriver($source);
    	if ($driver) {
    		$locations = $driver->getPickUpLocations($this->_stripIdPrefixes($user, $source));
    		return $this->_addIdPrefixes($locations, $source);
    	}
    	error_log("No driver for '$user' found");
    }
    
    public function placeHold($holdDetails)
    {
    	$source = $this->_getSource($holdDetails['patron']['cat_username']);
    	$driver = $this->_getDriver($source);
    	if ($driver) {
    	    $holdDetails = $this->_stripIdPrefixes($holdDetails, $source);
    	    return $driver->placeHold($holdDetails);
    	}
    	error_log("No driver for '$id' found");
    }
    
    public function cancelHolds($cancelDetails)
    {
    	$source = $this->_getSource($cancelDetails['patron']['cat_username']);
    	$driver = $this->_getDriver($source);
    	if ($driver) {
    	    return $driver->cancelHolds(array($this->_stripIdPrefixes($cancelDetails, $source), 'patron' => $patron));
    	}
    	error_log("No driver for '$id' found");
    }
    
    public function getCancelHoldDetails($holdDetails)
    {
    	$source = $this->_getSource($holdDetails['patron']['cat_username']);
    	$driver = $this->_getDriver($source);
    	if ($driver) {
    	    $holdDetails = $this->_stripIdPrefixes($holdDetails, $source);
    	    return $driver->getCancelHoldDetails($holdDetails);
    	}
    	error_log("No driver for '$id' found");
    }
    
    public function getConfig($function, $id = null)
    {
    	$source = null;
    	global $user;
    	if ($id) {
    		$source = $this->_getSource($id);
    	}
    	
    	if (!$source) {
    		global $user;
    		$source = $this->_getSource($user->cat_username);
    	}
    	
    	$driver = $this->_getDriver($source);
    	
    	# If we have resolved the needed driver, just getConfig and return.
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
                return Array();
            default:
                error_log("MultiHandler: unhandled getConfig function: '$function'");
        }
        return Array();
    }
    
    /**
     * Extract local ID from the given prefixed ID
     * 
     * @param string   $id
     * @return string  Local ID 
     */
    protected function _getLocalId($id)
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
     * @param string   $id
     * @return string  Source
     */
    protected function _getSource($id)
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
     * @param string   $source
     * @return mixed   On success a driver object, otherwise null.
     */
    protected function _getDriver($source)
    {
    	$source = strtolower($source);
		if (isset($this->_drivers[$source])) {
			$driver = $this->_drivers[$source];
    		try {
	    		require_once "{$driver}.php";
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
     * @param mixed		 $data		   The data to be modified, normally array or array of arrays
     * @param string     $source       Source code
     * @param array      $modifyFields Fields to be modified in the array
     * @return mixed     Modified array or empty/null if that input was empty/null
     */
    function _addIdPrefixes($data, $source, $modifyFields = array('id', 'cat_username'))
    {
        if (!isset($data) || empty($data) || PEAR::isError($data)) {
            return $data;
        }
        $array = is_array($data) ? $data : array($data);
    
        foreach ($array as $key => $value) {
        	if (is_array($value)) {
	            $array[$key] = $this->_addIdPrefixes($value, $source, $modifyFields);
            } else {
		        if (in_array($key, $modifyFields)) {
		            $array[$key] = $source . '.' . $value; 
		        }
		    }
		}
        return is_array($data) ? $array : $array[0];
	}

    /**
     * Change global ID's to local ID's in the given array
     * 
     * @param mixed		 $data		   The data to be modified, normally array or array of arrays
     * @param string     $source       Source code
     * @param array      $modifyFields Fields to be modified in the array
     * @return mixed     Modified array or empty/null if that input was empty/null
     */
	function _stripIdPrefixes($data, $source, $modifyFields = array('id', 'cat_username'))
	{
	    if (!isset($data) || empty($data)) {
	        return $data;
	    }
	    $array = is_array($data) ? $data : array($data);
	
	    foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = $this->_stripIdPrefixes($value, $source, $modifyFields);
            } else {
	            if (in_array($key, $modifyFields) && strncmp($source . '.', $value, strlen($source) + 1) == 0) {
	                $array[$key] = substr($value, strlen($source) + 1);
	            }
	        }
	    }
	    return is_array($data) ? $array : $array[0];
	}
}
    
