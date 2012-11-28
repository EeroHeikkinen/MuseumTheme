<?php
/**
 * JSON handler for Mozilla Persona Login
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
* @package  Controller_AJAX
* @author   Ere Maijala <ere.maijala@helsinki.fi>
* @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
* @link     http://vufind.org/wiki/building_a_module Wiki
*/
require_once 'JSON.php';
require_once 'services/MyResearch/Logout.php';

/**
 * Mozilla Persona Login/Logout action
 * 
 * @category VuFind
 * @package  Controller_Record
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */
class JSON_PersonaLogin extends JSON
{
    /**
     * Verify Persona assertion and log the user in
     *
     * @return true
     * @access public
     */
    public function login()
    {
        try {
            $authN = AuthenticationFactory::initAuthentication('MozillaPersona');
            $user = $authN->authenticate();
        } catch (Exception $e) {
            if ($configArray['System']['debug']) {
                error_log("Exception: " . $e->getMessage());
            }
            return $this->output(false, JSON::STATUS_ERROR);
        }

        // If we authenticated, store the user in the session:
        if (PEAR::isError($user)) {
            error_log('Persona login error: ' . $user->getMessage());
            return $this->output(false, JSON::STATUS_ERROR);
        }
        unset($_SESSION['no_store']);
        UserAccount::updateSession($user);
        return $this->output(true, JSON::STATUS_OK);
    }

    /**
     * Logout
     *
     * @return true
     * @access public
     */
    public function logout()
    {
        Logout::performLogout();
        return $this->output(true, JSON::STATUS_OK);
    }
}

