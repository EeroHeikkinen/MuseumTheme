<?php
/**
 * CallSlip action for Record module
 *
 * PHP version 5
 *
 * Copyright (C) Villanova University 2007.
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
 * @author   Andrew S. Nagy <vufind-tech@lists.sourceforge.net>
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */

require_once 'Record.php';
require_once 'Crypt/generateHMAC.php';

/**
 * CallSlip action for Record module
 *
 * @category VuFind
 * @package  Controller_Record
 * @author   Andrew S. Nagy <vufind-tech@lists.sourceforge.net>
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */
class CallSlip extends Record
{
    protected $gatheredDetails;
    protected $logonURL;

    /**
     * Process incoming parameters and display the page.
     *
     * @return void
     * @access public
     */
    public function launch()
    {
        global $configArray;
        global $interface;
        global $user;

        // Are Call Slips Allowed?
        $this->checkCallSlips = $this->catalog->checkFunction("CallSlips", $this->recordDriver->getUniqueID());
        if ($this->checkCallSlips != false) {

            // Do we have valid information?
            // Sets $this->logonURL and $this->gatheredDetails
            $validate = $this->_validateCallSlipData($this->checkCallSlips['HMACKeys']);
            if (!$validate) {
                header(
                    'Location: ../../Record/' .
                    urlencode($this->recordDriver->getUniqueID())
                );
                return false;
            }

            // Assign FollowUp Details required for login and catalog login
            $interface->assign('followup', true);
            $interface->assign('recordId', $this->recordDriver->getUniqueID());
            $interface->assign('followupModule', 'Record');
            $interface->assign('followupAction', 'CallSlip'.$this->logonURL);

            // User Must be logged In to Place Holds
            if (UserAccount::isLoggedIn()) {
                if ($patron = UserAccount::catalogLogin()) {
                    // Block invalid requests:
                    if (!$this->catalog->checkCallSlipRequestIsValid(
                        $this->recordDriver->getUniqueID(),
                        $this->gatheredDetails, $patron
                    )) {
                        header(
                            'Location: ../../Record/' .
                            urlencode($this->recordDriver->getUniqueID()) .
                            "?errorMsg=call_slip_error_blocked#top"
                        );
                        return false;
                    }

                    
                    $interface->assign('formURL', $this->logonURL);

                    $interface->assign('gatheredDetails', $this->gatheredDetails);

                    $extraFields = isset($this->checkCallSlips['extraFields'])
                        ? explode(":", $this->checkCallSlips['extraFields'])
                            : array();
                    $interface->assign('extraFields', $extraFields);

                    if (isset($_POST['placeRequest'])) {
                        if ($this->_placeRequest($patron)) {
                            // If we made it this far, we're ready to place the request;
                            // if successful, we will redirect and can stop here.
                            return;
                        }
                    }
                }
                $interface->setPageTitle(
                    translate('call_slip_place_text') . ': ' .
                    $this->recordDriver->getBreadcrumb()
                );
                // Display Form
                $interface->assign('subTemplate', 'call-slip-submit.tpl');
                
                // Main Details
                $interface->setTemplate('view.tpl');
                // Display Page
                $interface->display('layout.tpl');
            } else {
                // User is not logged in
                // Display Login Form
                $interface->setTemplate('../MyResearch/login.tpl');
                // Display Page
                $interface->display('layout.tpl');
            }

        } else {
            // Shouldn't Be Here
            header(
                'Location: ../../Record/' .
                urlencode($this->recordDriver->getUniqueID())
            );
            return false;
        }
    }

    /**
     * Send an error response to the view.
     *
     * @param array $results Place request response containing an error.
     *
     * @return void
     * @access protected
     */
    protected function assignError($results)
    {
        global $interface;

        $interface->assign('results', $results);

        // Fail: Display Form for Try Again
        // Get as much data back as possible
        $interface->assign('subTemplate', 'call-slip-submit.tpl');
    }

    /**
     * Private method for validating request data
     *
     * @param array $linkData An array of keys to check
     *
     * @return boolean True on success
     * @access private
     */
    private function _validateCallSlipData($linkData)
    {
        foreach ($linkData as $details) {
            $keyValueArray[$details] = $_GET[$details];
        }
        $hashKey = generateHMAC($linkData, $keyValueArray);

        if ($_REQUEST['hashKey'] != $hashKey) {
            return false;
        } else {
            // Initialize gatheredDetails with any POST values we find; this will
            // allow us to repopulate the hold form with user-entered values if there
            // is an error.  However, it is important that we load the POST data
            // FIRST and then override it with GET values in order to ensure that
            // the user doesn't bypass the hashkey verification by manipulating POST
            // values.
            $this->gatheredDetails = isset($_POST['gatheredDetails'])
                ? $_POST['gatheredDetails'] : array();

            // Make sure the bib ID is included, even if it's not loaded as part of
            // the validation loop below.
            $this->gatheredDetails['id'] = $_GET['id'];

            // Get Values Passed from holdings.php
            $i=0;
            foreach ($linkData as $details) {
                $this->gatheredDetails[$details] = $_GET[$details];
                // Build Logon URL
                if ($i == 0) {
                    $this->logonURL = "?".$details."=".urlencode($_GET[$details]);
                } else {
                    $this->logonURL .= "&".$details."=".urlencode($_GET[$details]);
                }
                $i++;
            }
            $this->logonURL .= ($i == 0 ? '?' : '&') .
                "hashKey=".urlencode($hashKey);
        }
        return true;
    }

    /**
     * Private method for making the request
     *
     * @param array $patron An array of patron information
     *
     * @return boolean true on success, false on failure
     * @access private
     */
    private function _placeRequest($patron)
    {
        // Add Patron Data to Submitted Data
        $details = $this->gatheredDetails + array('patron' => $patron);

        // Attempt to place the hold:
        $function = (string)$this->checkCallSlips['function'];
        
        $results = $this->catalog->$function($details);
        if (PEAR::isError($results)) {
            PEAR::raiseError($results);
        }
        // Success: Go to Display Holds
        if ($results['success'] == true) {
            header('Location: ../../MyResearch/Holds?callslip_success=true');
            return true;
        } else {
            $this->assignError($results);
        }
        return false;
    }
}

?>