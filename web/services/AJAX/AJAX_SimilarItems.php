<?php
/**
 * Ajax page for similar items
 *
 * PHP version 5
 *
 * Copyright (C) The National Library of Finland 2012
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
require_once 'Action.php';
require_once 'sys/SearchObject/Solr.php';

/**
 * Ajax page for similar items
 *
 * @category VuFind
 * @package  Controller_AJAX
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */
class AJAX_SimilarItems extends Action
{
    /**
     * Get similar items and return html snippet
     * 
     * @return void
     */
    public function launch()
    {
        global $interface;
        
        $this->db = ConnectionManager::connectToIndex();
        
        // Retrieve the record from the index
        if (!($record = $this->db->getRecord($_REQUEST['id']))) {
            PEAR::raiseError(new PEAR_Error('Record Does Not Exist'));
        }
        $this->recordDriver = RecordDriverFactory::initRecordDriver($record);
        
        // Get similar records
        $similar = $this->db->getMoreLikeThis(
            $_REQUEST['id'],
            array('fq' => SearchObject_Solr::getDefaultHiddenFilters())
        );

        // Send the similar items to the template; if there is only one, we need
        // to force it to be an array or things will not display correctly.
        if (count($similar['response']['docs']) > 0) {
            $interface->assign('similarRecords', $similar['response']['docs']);
        }

        // Find Other Editions
        $editions = $this->recordDriver->getEditions();
        if (!PEAR::isError($editions)) {
            $interface->assign('editions', $editions);
        }
        
        $interface->display('Record/similar-items.tpl');
    }
}

?>
