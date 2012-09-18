<?php
/**
 * JSON handler for MetaLib functions 
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
 * @package  Controller_Record
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */
require_once 'JSON.php';
require_once 'RecordDrivers/Factory.php';
require_once 'sys/MetaLib.php';

/**
 * JSON MetaLib action
 *
 * @category VuFind
 * @package  Controller_Record
 * @author   Markku Heinäsenaho <markku.heinasenaho@helsinki.fi>
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */

class JSON_MetaLib extends JSON
{
    /**
     * Get data and output in JSON
     *
     * @return void
     * @access public
     */
    public function getSearchLinkStatuses()
    {
        $metalib = new MetaLib();

        // Cache values and status in an array
        $results = array();
        $authorized = UserAccount::isAuthorized();
        foreach ($_REQUEST['id'] as $id) {
            if ($authorized) {
                $results[] = array('id' => $id, 'status' => 'allowed');
                continue;
            }
            $ird = explode('.', $id, 2);
            if (!isset($ird[1])) {
                continue;
            }
            $ird = $ird[1];
            $irdInfo = $metalib->getIRDInfo($ird);
            if ($irdInfo && strcasecmp($irdInfo['access'], 'guest') == 0) {
                $results[] = array('id' => $id, 'status' => 'allowed');
            } else {
                $results[] = array('id' => $id, 'status' => 'denied');
            }
        }
        return $this->output($results, JSON::STATUS_OK);
    }
    
}

