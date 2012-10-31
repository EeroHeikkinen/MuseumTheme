<?php
/**
 * Mozilla Persona authentication module.
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
 * @package  Authentication
 * @author   Franck Borel <franck.borel@gbv.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_an_authentication_handler Wiki
 */
require_once 'PEAR.php';
require_once 'Authentication.php';
require_once 'ShibbolethConfigurationParameter.php';
require_once 'services/MyResearch/lib/User.php';

/**
 * Mozilla Persona authentication module.
 *
 * @category VuFind
 * @package  Authentication
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_an_authentication_handler Wiki
 */
class MozillaPersonaAuthentication implements Authentication
{
    /**
     * Constructor
     *
     * @param string $configurationFilePath Optional configuration file path.
     *
     * @access public
     */
    public function __construct($configurationFilePath = '')
    {
    }

    /**
     * Attempt to authenticate the current user.
     *
     * @return object User object if successful, PEAR_Error otherwise.
     * @access public
     */
    public function authenticate()
    {
        global $configArray;

        if (!isset($_POST['assertion'])) {
             return new PEAR_Error('Missing assertion');
        }
            
        $assertion = $_POST['assertion'];
        $audience = (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'];
        $postdata = 'assertion=' . urlencode($assertion) . '&audience=' . urlencode($audience);
        $client = new Proxy_Request('https://verifier.login.persona.org/verify');
        $client->setMethod(HTTP_REQUEST_METHOD_POST);
        $client->addPostData('assertion', $assertion);
        $client->addPostData('audience', $audience);
        $client->sendRequest();
        $response = $client->getResponseBody();
        $result = json_decode($response);
        if ($result->status !== 'okay') {
            return new PEAR_ERROR($result->reason);
        }
        $user = new User();
        $user->authMethod = 'MozillaPersona';

        $user->username = $result->email;
        $userIsInVufindDatabase = $user->find(true);
        if (!$userIsInVufindDatabase || !$user->email) {
            $user->email = $result->email;
        }
        if ($userIsInVufindDatabase) {
            $user->update();
        } else {
            $user->created = date('Y-m-d');
            $user->insert();
        }

        return $user;
    }
}
?>
