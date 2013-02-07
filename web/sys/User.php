<?php
/**
 * Wrapper class for handling logged-in user in session.
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
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/system_classes Wiki
 */
require_once 'XML/Unserializer.php';
require_once 'XML/Serializer.php';

require_once 'sys/authn/AuthenticationFactory.php';

// This is necessary for unserialize
require_once 'services/MyResearch/lib/User.php';
require_once 'services/MyResearch/lib/User_account.php';

/**
 * Wrapper class for handling logged-in user in session.
 *
 * @category VuFind
 * @package  Support_Classes
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/system_classes Wiki
 */
class UserAccount
{
    /**
     * Checks whether the user is logged in.
     *
     * @return bool Is the user logged in?
     * @access public
     */
    public static function isLoggedIn()
    {
        if (isset($_SESSION['userinfo'])) {
            return unserialize($_SESSION['userinfo']);
        }
        return false;
    }

    /**
     * Checks whether the user is authorized to access 
     * restricted resources.
     *
     * @return bool Is the user authorized
     * @access public
     */
    public static function isAuthorized()
    {
        global $configArray;
        
        if (isset($_SESSION['authMethod']) && isset($configArray['Authorization']['authentication_methods'])) {
            if (in_array($_SESSION['authMethod'], $configArray['Authorization']['authentication_methods'])) {
                return true;
            }
        }
        
        if (isset($configArray['Authorization']['ip']) && $configArray['Authorization']['ip']) {
            if (UserAccount::isInIpRange()) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if user's IP address is in the known IP addresses
     * 
     * @return boolean Whether the IP address is known
     */
    public static function isInIpRange()
    {
        global $configArray;
        
        if (!isset($configArray['IP_Addresses'])) {
            return false;
        }

        foreach ($configArray['IP_Addresses'] as $rangeDef) {
            $remote = UserAccount::normalizeIp($_SERVER['REMOTE_ADDR']);
            $ranges = explode(',', $rangeDef);
            foreach ($ranges as $range) {
                $ips = explode('-', $range);
                if (!isset($ips[0])) {
                    continue;
                }
                $ips[0] = UserAccount::normalizeIp($ips[0]);
                if (!isset($ips[1])) {
                    $ips[1] = $ips[0];
                } else {
                    $ips[1] = UserAccount::normalizeIp($ips[1], true);
                }
                if ($ips[0] === false || $ips[1] === false) {
                    error_log("Could not parse IP address/range: $range");
                    continue;
                }
                if ($remote >= $ips[0] && $remote <= $ips[1]) {
                    return true;
                }
            }
        }
        return false;
    }
    
    /**
     * Updates the user information in the session.
     *
     * @param object $user User object to store in the session
     *
     * @return void
     * @access public
     */
    public static function updateSession($user)
    {
        $_SESSION['userinfo'] = serialize($user);
        if (isset($user->authMethod)) {
            $_SESSION['authMethod'] = $user->authMethod;
        }
    }

    /**
     * Try to log in the user using current query parameters; return User object
     * on success, PEAR error on failure.
     *
     * @param string $method Optional method to override configuration
     *
     * @return object
     * @access public
     */
    public static function login($method = '')
    {
        global $configArray;

        // Perform authentication:
        try {
            $authN = AuthenticationFactory::initAuthentication(
                $method ? $method : $configArray['Authentication']['method']
            );
            $user = $authN->authenticate();
        } catch (Exception $e) {
            if ($configArray['System']['debug']) {
                echo "Exception: " . $e->getMessage();
            }
            error_log("Authentication exception: " . $e->getMessage());
            $user = new PEAR_Error('authentication_error_technical');
        }

        // If we authenticated, store the user in the session:
        if ($user && !PEAR::isError($user)) {
            self::verifyAccountInList($user);
            self::updateSession($user);
        }

        // Send back the user object (which may be a PEAR error):
        return $user;
    }

    /**
     * Log the current user into the catalog using stored credentials; if this
     * fails, clear the user's stored credentials so they can enter new, corrected
     * ones.
     *
     * @return mixed                     $user object (on success) or false (on
     * failure)
     * @access protected
     */
    public static function catalogLogin()
    {
        global $user;

        $catalog = ConnectionManager::connectToCatalog();
        if ($catalog && $catalog->status && $user && $user->cat_username) {
            $patron = $catalog->patronLogin(
                $user->cat_username, $user->cat_password
            );
            if (empty($patron) || PEAR::isError($patron)) {
                // Problem logging in -- clear user credentials so they can be
                // prompted again; perhaps their password has changed in the
                // system!
                unset($user->cat_username);
                unset($user->cat_password);
            } else {
                self::verifyAccountInList($user);
                return $patron;
            }
        }

        return false;
    }

    /**
     * Attempt to log in the user to the ILS, and save credentials if it works.
     *
     * @param string $username Catalog username
     * @param string $password Catalog password
     *
     * @return bool            True on successful login, false on error.
     */
    public static function processCatalogLogin($username, $password)
    {
        global $user;

        $catalog = ConnectionManager::connectToCatalog();
        $result = $catalog->patronLogin($username, $password);
        if ($result && !PEAR::isError($result)) {
            $user->cat_username = $username;
            $user->cat_password = $password;
            $user->update();
            self::verifyAccountInList($user);
            self::updateSession($user);
            return true;
        }
        return false;
    }

    /**
     * Activate a catalog account (no checks performed)
     * 
     * @param string $username    User ID
     * @param string $password    Password
     * @param string $homeLibrary Home Library
     * 
     * @return void
     */
    public static function activateCatalogAccount($username, $password, $homeLibrary)
    {
        global $user;

        $user->cat_username = $username;
        $user->cat_password = $password;
        $user->home_library = $homeLibrary;
        $user->update();
        self::updateSession($user);
    }

    /**
     * Activate a catalog account (no checks performed)
     * 
     * @param string $id Account ID
     * 
     * @return void
     */
    public static function activateCatalogAccountID($id)
    {
        global $user;
        
        $account = new User_account();
        $account->id = $id;
        $account->user_id = $user->id;
        if ($account->find(true)) {
            $user->cat_username = $account->cat_username;
            $user->cat_password = $account->cat_password;
            $user->home_library = $account->home_library;
            $user->update();
            self::updateSession($user);
        }
    }
    
    /**
     * Normalize IP address to numeric IPv6 address
     * 
     * @param string  $ip  IP Address
     * @param boolean $end Whether to make this and "end of range" address
     * 
     * @return number
     */
    protected static function normalizeIp($ip, $end = false)
    {
        if (strpos($ip, ':') === false) {
            $addr = explode('.', $ip);
            while (count($addr) < 4) {
                $addr[] = $end ? 255 : 0;
            }
             
            $ip = '::' . implode('.', array_map('intval', $addr));
        } else {
            $ip = str_replace('::', ':' . str_repeat('0:', 8 - substr_count($ip, ':')), $ip);
            if ($ip[0] == ':') {
                $ip = "0$ip";
            }
            while (substr_count($ip, ':') < 7) {
                $ip .= $end ? ':ffff' : ':0';
            }
        }
        return inet_pton($ip);
    }

    /**
     * Verify that the current catalog account is in the account list
     * 
     * @param object $user User
     * 
     * @return void
     */
    protected static function verifyAccountInList($user)
    {
        if (!isset($user->cat_username) || !$user->cat_username) {
            return;
        }
        $account = new User_account();
        $account->user_id = $user->id;
        $account->cat_username = $user->cat_username;
        if (!$account->find(true)) {
            list($login_target, $cat_username) = explode('.', $account->cat_username, 2);
            if ($login_target && $cat_username) {
                $account->account_name = translate(array('text' => $login_target, 'prefix' => 'source_'));
            } else {
                $account->account_name = translate('Default');
            }
            $account->cat_password = $user->cat_password;
            $account->home_library = $user->home_library;
            $account->created = date('Y-m-d h:i:s');
            $account->insert();
        } 
    }
    
}

?>
