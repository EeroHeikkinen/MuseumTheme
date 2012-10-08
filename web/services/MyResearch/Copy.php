<?php
/**
 * Copy action for MyResearch module
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

require_once 'Action.php';
require_once 'services/MyResearch/MyResearch.php';
require_once 'services/MyResearch/lib/Comments.php';

/**
 * Copy action for MyResearch module
 *
 * @category VuFind
 * @package  Controller_MyResearch
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */
class Copy extends MyResearch
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

        $this->processRecords($_REQUEST['ids'], $_REQUEST['copy']);

        header('Location: ' . $this->followupUrl . ($this->errorMsg ? '?errorMsg=' . $this->errorMsg : ($this->infoMsg ? '?infoMsg=' . $this->infoMsg : '')));
    }

    /**
     * Perform a bulk operation.
     *
     * @param array $ids    IDs to process
     * @param int   $listID Target list
     * limit).
     *
     * @return boolean Success
     */
    protected function processRecords($ids, $listID)
    {    
        global $user;
        
        if (empty($ids)) {
            $this->errorMsg = 'bulk_noitems_advice';
            return false;
        }
        if (!$user) {
            $this->errorMsg = 'You must be logged in first';
            return false;
        }
        $list = User_list::staticGet($listID);
        if ($user->id != $list->user_id) {
            $this->errorMsg = 'list_access_denied';
            return false;
        }
        $resources = $user->getResources();
        foreach ($resources as $resource) {
            if (in_array($resource->record_id, $ids)) {
                $notes = '';
                $userResource = new User_resource();
                $userResource->user_id = $user->id;
                $userResource->list_id = $_REQUEST['listID'];
                $userResource->resource_id = $resource->id;
                if ($userResource->find(true)) {
                    $notes = $userResource->notes;
                }
                $tags = array();
                foreach ($user->getTags($resource->record_id) as $tag) {
                    $tags[] = $tag->tag;
                }
                $user->addResource(
                    $resource, $list, $tags, $notes
                );
            }
        }
        $this->infoMsg = 'records_copied';
        return true;
    }
}

