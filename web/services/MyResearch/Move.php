<?php
/**
 * Move action for MyResearch module
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
 * @package  Controller_MyResearch
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */

require_once 'services/MyResearch/Copy.php';

/**
 * Copy action for MyResearch module
 *
 * @category VuFind
 * @package  Controller_MyResearch
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */
class Move extends Copy
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

        if (isset($_REQUEST['followup'])) {
            $this->followupUrl =  $configArray['Site']['url'] . "/".
                $_REQUEST['followupModule'];
            $this->followupUrl .= "/" . $_REQUEST['followupAction'];
        } else if (isset($_REQUEST['listID']) && !empty($_REQUEST['listID'])) {
            $this->followupUrl = $configArray['Site']['url'] .
                "/MyResearch/MyList/" . urlencode($_REQUEST['listID']);
        } else {
            $this->followupUrl = $configArray['Site']['url'] .
                "/MyResearch/Favorites";
        }

        $this->processRecords($_REQUEST['ids'], $_REQUEST['move']);

        header('Location: ' . $this->followupUrl . ($this->errorMsg ? '?errorMsg=' . $this->errorMsg : ($this->infoMsg ? '?infoMsg=' . $this->infoMsg : '')));
    }
    
    /**
     * Perform a bulk operation.
     *
     * @param array $ids    IDs to process
     * @param int   $listID Target list
     * limit).
     *
     * @return void
     */
    protected function processRecords($ids, $listID)
    {    
        global $user;

        // First copy the records
        if (!parent::processRecords($ids, $listID)) {
            return false;
        }
        // Copy successful, now delete
        $list = User_list::staticGet($_REQUEST['listID']);
        if ($user->id != $list->user_id) {
            $this->errorMsg = 'list_access_denied';
            return false;
        }
        $list->removeResourcesById($ids);
        $this->infoMsg = 'records_moved';
        return true;
    }
}

