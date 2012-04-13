<?php
/**
 * Collection map action for Collection module
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
 * @package  Controller_Record
 * @author   Lutz Biedinger <lutz.biedigner@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */

require_once 'Collection.php';

/**
 * Home action for Record module
 *
 * @category VuFind
 * @package  Controller_Record
 * @author   Lutz Biedinger <lutz.biedigner@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */
class CollectionMap extends Collection
{
    protected  $record;
    private $_structure;

    /**
     * Process incoming parameters and display the page.
     *
     * @return void
     * @access public
     */
    public function launch()
    {
        global $interface;
        global $configArray;

        // Set Up Collection
        $this->assignCollection();
        $this->assignCollectionFacets();

        $interface->setPageTitle(
            translate('Map') . ': ' . $this->recordDriver->getBreadcrumb()
        );
        //$mapSearchObject = SearchObjectFactory::initSearchObject();
        $interface->assign(
            'searchParams', $this->searchObject->renderSearchUrlParams()
        );

        // Set Messages
        $interface->assign('infoMsg', $this->infoMsg);
        $interface->assign('errorMsg', $this->errorMsg);

        //turn off quirks mode for ie
        if (isset($_SERVER['HTTP_USER_AGENT'])
            && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)
        ) {
            $interface->assign('quirks', 'off');
        }
        // Set Templates etc
        $interface->assign('subpage', 'Collection/collectionGoogleMap.tpl');
        // This is because if loaded from an link, the tab will
        // not automatically be selected
        $interface->assign('tab', 'CollectionMap');
        //$interface->setTemplate('collectionview.tpl');
        // Display Page
        $interface->display('layout.tpl');
    }
}

?>
