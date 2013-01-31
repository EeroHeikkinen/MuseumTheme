<?php
/**
 * Accounts action for MyResearch module
 *
 * PHP version 5
 *
 * Copyright (C) The National Library of Finland 2013.
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
 * @package  Controller_MyResearch
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */
require_once 'services/MyResearch/MyResearch.php';
require_once 'services/MyResearch/Login.php';
require_once 'services/MyResearch/lib/User_account.php';

/**
 * Accounts action for MyResearch module
 *
 * @category VuFind
 * @package  Controller_MyResearch
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */
class Accounts extends MyResearch
{
    /**
     * Process parameters and display the page.
     *
     * @return void
     * @access public
     */
    public function launch()
    {
        global $configArray;
        global $interface;
        global $user;

        error_log("UN11: " . $user->cat_username);
        
        if (isset($_REQUEST['delete'])) {
            $this->deleteAccount($_REQUEST['delete']);
            header('Location: Accounts');
            die();
        }
        if (isset($_REQUEST['edit'])) {
            $this->editAccount($_REQUEST['edit']);
            return;
        }
        if (isset($_REQUEST['add'])) {
            if (!$this->editAccount(null)) {
                header('Location: Accounts');
                die();
            }
            return;
        }
        if (isset($_REQUEST['submit'])) {
            if ($this->saveAccount()) {
                header('Location: Accounts');
                die();
            } 
            return;
        }
        
        // Get account list
        $interface->assign('accounts', $user->getCatalogAccounts());
 
        $interface->setTemplate('accounts.tpl');
        $interface->setPageTitle('Library Cards');
        $interface->display('layout.tpl');
    }

    /**
     * Add or edit an account
     * 
     * @param int $id Account ID (null to add a new account)
     * 
     * @return void
     */
    protected function editAccount($id)
    {
        global $user;
        global $interface;
        
        if (isset($id)) { 
            $account = new User_account();
            $account->id = $id;
            $account->user_id = $user->id;
            if (!$account->find(true)) {
                header('Location: Accounts');
                die();
            }
            $date = new VuFindDate();
            $interface->assign('id', $account->id);
            $interface->assign('account_name', isset($_REQUEST['account_name']) ? $_REQUEST['account_name'] : $account->account_name);
            $interface->assign('description', isset($_REQUEST['description']) ? $_REQUEST['description'] : $account->description);
            list($login_target, $cat_username) = explode('.', $account->cat_username, 2);
            $interface->assign('login_target', isset($_REQUEST['login_target']) ? $_REQUEST['login_target'] : $login_target);
            $interface->assign('cat_username', isset($_REQUEST['username']) ? $_REQUEST['username'] : $cat_username);
            $interface->assign('cat_password', $account->cat_password);
            $interface->assign('home_library', $account->home_library);
            $interface->assign('created', $date->convertToDisplayDate('Y-m-d H:i:s', $account->created));
            $interface->assign('changed', $date->convertToDisplayDate('Y-m-d H:i:s', $account->saved));
        } else {
            $interface->assign('account_name', isset($_REQUEST['account_name']) ? $_REQUEST['account_name'] : '');
            $interface->assign('description', isset($_REQUEST['description']) ? $_REQUEST['description'] : '');
            $interface->assign('login_target', isset($_REQUEST['login_target']) ? $_REQUEST['login_target'] : '');
            $interface->assign('cat_username', isset($_REQUEST['username']) ? $_REQUEST['username'] : '');
        }

        Login::setupLoginFormVars();
        $interface->setTemplate('editAccount.tpl');
        $interface->setPageTitle('Library Cards');
        $interface->display('layout.tpl');        
    }
    
    /**
     * Validate and save account information after editing
     * 
     * @return boolean Success
     */
    protected function saveAccount()
    {
        global $interface;
        global $user;
        
        $username = $_POST['username'];
        $password = $_POST['password'];
        $loginTarget = isset($_POST['login_target']) ? $_POST['login_target'] : false;
        if ($loginTarget) {
            $username = "$loginTarget.$username";
        }

        $catalog = ConnectionManager::connectToCatalog();
        $result = $catalog->patronLogin($username, $password);
        if (!$result || PEAR::isError($result)) {
            $interface->assign('errorMsg', 'Invalid Patron Login');
            $this->editAccount(isset($_REQUEST['id']) ? $_REQUEST['id'] : null);
            return false;
        }

        $exists = false;
        $account = new User_account();
        $account->user_id = $user->id;
        if (isset($_REQUEST['id']) && $_REQUEST['id']) {
            $account->id = $_REQUEST['id'];
            $exists = $account->find(true);
        }

        // Check that the user name is not in use in another account
        if (!$exists || $account->cat_username != $username) {
            $otherAccount = new User_account();
            $otherAccount->user_id = $user->id;
            $otherAccount->cat_username = $username;
            if ($otherAccount->find()) {
                $interface->assign('errorMsg', 'Username already in use in another library card');
                $this->editAccount(isset($_REQUEST['id']) ? $_REQUEST['id'] : null);
                return false;
            }
        }
        
        
        if (!$exists) {
            $account->created = date('Y-m-d h:i:s');
        } else {
            error_log($user->cat_username . ' == ' . $account->cat_username);
            if ($user->cat_username == $account->cat_username) {
                // Active account modified, update
                UserAccount::activateCatalogAccount($username, $password, $account->home_library);
            }
        }
        
        $account->account_name = $_REQUEST['account_name'];
        $account->description = $_REQUEST['description'];
        $account->cat_username = $username;
        $account->cat_password = $password;
        if ($exists) {
            $account->update();
        } else {
            $account->insert();
            // If this is the first one, activate it
            if (count($user->getCatalogAccounts()) == 1) { 
                UserAccount::activateCatalogAccount($username, $password, $account->home_library);
            }
        }
        return true;
    }
    
    /**
     * Delete an account
     * 
     * @param int $id Account ID
     * 
     * @return boolean Whether the account was deleted
     */
    protected function deleteAccount($id)
    {
        global $user;
        
        $account = new User_account();
        $account->id = $id;
        $account->user_id = $user->id;
        if ($account->find(true)) {
            $account->delete();
            if ($user->cat_username == $account->cat_username) {
                // Active account deleted, select another or deactivate
                $account = new User_account();
                $account->user_id = $user->id;
                if ($account->find(true)) {
                    UserAccount::activateCatalogAccount($account->cat_username, $account->cat_password, $account->home_library);
                } else {
                    UserAccount::activateCatalogAccount('', '', '');
                }
            }
            return true;
        }
        return false;
    }
}
