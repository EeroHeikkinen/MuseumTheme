<?php
/**
 * Logout action for MyResearch module
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
 * @package  Controller_MyResearch
 * @author   Andrew S. Nagy <vufind-tech@lists.sourceforge.net>
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */
require_once 'Action.php';

/**
 * Logout action for MyResearch module
 *
 * @category VuFind
 * @package  Controller_MyResearch
 * @author   Andrew S. Nagy <vufind-tech@lists.sourceforge.net>
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */
class Logout extends Action
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

        self::performLogout();

        // Forward to the appropriate place (depending on auth settings):
        if ($configArray['Authentication']['method'] == 'Shibboleth'
            && isset($configArray['Shibboleth']['logout'])
        ) {
            $logout = $configArray['Shibboleth']['logout'] . "?return="
                . urlencode($configArray['Site']['url']);
            header('Location: ' . $logout);
        } else {
            header('Location: ' . $configArray['Site']['url']);
        }
    }

    /**
     * Destroy the session
     *
     * @return void
     * @access public
     */
    public static function performLogout()
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        $_SESSION = array();

        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time()-42000, '/');
        }

        session_destroy();
    }
}

?>
