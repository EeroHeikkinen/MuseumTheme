<?php
/**
 * Hold Logic Title Class
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
 * @package  Support_Classes
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @author   Luke O'Sullivan <l.osullivan@swansea.ac.uk>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/system_classes#index_interface Wiki
 */

require_once 'CatalogConnection.php';
require_once 'Crypt/generateHMAC.php';

/**
 * Hold Logic Title Class
 *
 * @category VuFind
 * @package  Support_Classes
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @author   Luke O'Sullivan <l.osullivan@swansea.ac.uk>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/system_classes#index_interface Wiki
 */
class HoldLogicTitle
{
    protected $catalog;
    protected $hideHoldings;

    /**
     * Constructor
     *
     * @param object $catalog A catalog connection
     *
     * @access public
     */
    public function __construct($catalog = false)
    {
        global $configArray;

        $this->hideHoldings = isset($configArray['Record']['hide_holdings'])
            ? $configArray['Record']['hide_holdings'] : array();

        $this->catalog = ($catalog == true)
            ? $catalog : ConnectionManager::connectToCatalog();
    }

    /**
     * Public method for getting title level holds
     *
     * @param string $id     An Bib ID
     * @param array  $patron An array of patron data
     *
     * @return array A sorted results set
     * @access public
     */

    public function getHold($id, $patron = false)
    {
        // Get Holdings Data
        if ($this->catalog && $this->catalog->status) {

            $mode = CatalogConnection::getTitleHoldsMode();
            if ($mode == "disabled") {
                 return false;
            } else if ($mode == "driver") {
                return $this->driverHold($id, $patron);
            } else {
                return $this->generateHold($id, $mode);
            }
        }
        return false;
    }

    /**
     * Protected method for driver defined title holds
     *
     * @param string $id     A Bib ID
     * @param array  $patron An Array of patron data
     *
     * @return mixed A url on success, boolean false on failure
     * @access protected
     */
    protected function driverHold($id, $patron)
    {
        // Get Hold Details
        $checkHolds = $this->catalog->checkFunction("Holds");
        $data = array(
            'id' => $id,
            'level' => "title"
        );

        $valid = $this->catalog->checkRequestIsValid($id, $data, $patron);
        if ($valid) {
            return $this->_getHoldDetails($data, $checkHolds['HMACKeys']);
        }
        return false;
    }

    /**
     * Protected method for vufind (i.e. User) defined holds
     *
     * @param string $id   A Bib ID
     * @param string $type The holds mode to be applied from:
     * (all, holds, recalls, availability)
     *
     * @return mixed A url on success, boolean false on failure
     * @access protected
     */
    protected function generateHold($id, $type)
    {
        $any_available = false;
        $addlink = false;

        $data = array(
            'id' => $id,
            'level' => "title"
        );

        // Are holds allows?
        $checkHolds = $this->catalog->checkFunction("Holds");

        if ($checkHolds != false) {

            if ($type == "always") {
                 $addlink = true;
            } elseif ($type == "availability") {

                $holdings = $this->catalog->getHolding($id);
                foreach ($holdings as $holding) {
                    if ($holding['availability']
                        && !in_array($holding['location'], $this->hideHoldings)
                    ) {
                        $any_available = true;
                    }
                }
                $addlink = !$any_available;
            }

            if ($addlink) {
                if ($checkHolds['function'] == "getHoldLink") {
                    /* Return opac link */
                    return $this->catalog->getHoldLink($id, $data);
                } else {
                    /* Return non-opac link */
                    return $this->_getHoldDetails($data, $checkHolds['HMACKeys']);
                }
            }
        }
        return false;
    }

    /**
     * Get Hold Link
     *
     * Supplies the form details required to place a hold
     *
     * @param array $data     An array of item data
     * @param array $HMACKeys An array of keys to hash
     *
     * @return string A url link (with HMAC key)
     * @access private
     */
    private function _getHoldDetails($data, $HMACKeys)
    {
        global $configArray;

        $siteUrl = $configArray['Site']['url'];
        $id = $data['id'];

        // Generate HMAC
        $HMACkey = generateHMAC($HMACKeys, $data);

        // Add Params
        foreach ($data as $key => $param) {
            $needle = in_array($key, $HMACKeys);
            if ($needle) {
                $queryString[] = $key. "=" .urlencode($param);
            }
        }

        //Add HMAC
        $queryString[] = "hashKey=" . $HMACkey;

        // Build Params
        $urlParams = "?" . implode("&", $queryString);

        $holdLink = $siteUrl."/Record/".urlencode($id)."/Hold".$urlParams."#tabnav";
        return $holdLink;
    }
}
?>
