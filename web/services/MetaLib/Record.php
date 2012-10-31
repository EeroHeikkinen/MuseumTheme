<?php
/**
 * Record action for MetaLib module
 *
 * PHP version 5
 *
 * Copyright (C) Andrew Nagy 2008.
 * Copyright (C) Ere Maijala, The National Library of Finland 2012.
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
 * @package  Controller_MetaLib
 * @author   Andrew Nagy <vufind-tech@lists.sourceforge.net>
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */
require_once 'Base.php';

require_once 'sys/MetaLib.php';

require_once 'services/MyResearch/lib/User.php';
require_once 'services/MyResearch/lib/Resource.php';
require_once 'services/MyResearch/lib/Resource_tags.php';
require_once 'services/MyResearch/lib/Tags.php';

/**
 * Record action for MetaLib module
 *
 * @category VuFind
 * @package  Controller_MetaLib
 * @author   Andrew Nagy <vufind-tech@lists.sourceforge.net>
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */
class Record extends Base
{
    protected $record;

    /**
     * Constructor
     *
     * @access public
     */
    public function __construct()
    {
        global $interface;
        global $configArray;

        // Call parent constructor
        parent::__construct();

        // Fetch Record
        $config = getExtraConfigArray('MetaLib');
        $metalib = new MetaLib();
        $this->record = $metalib->getRecord($_REQUEST['id']);
        if (PEAR::isError($this->record)) {
            PEAR::raiseError($this->record);
        } 

        // Set Proxy URL
        $interface->assign(
            'proxy', isset($configArray['EZproxy']['host'])
            ? $configArray['EZproxy']['host'] : false
        );

        // Whether RSI is enabled
        if (isset($configArray['OpenURL']['use_rsi']) && $configArray['OpenURL']['use_rsi']) {
            $interface->assign('rsi', true);
        }
        
        // Whether embedded openurl autocheck is enabled
        if (isset($configArray['OpenURL']['autocheck']) && $configArray['OpenURL']['autocheck']) {
            $interface->assign('openUrlAutoCheck', true);
        }
        
        // Send record ID to template
        $interface->assign('id', $_REQUEST['id']);
    }

    /**
     * Process parameters and display the page.
     *
     * @return void
     * @access public
     */
    public function launch()
    {
        global $interface;
        global $configArray;

        // Send basic information to the template.
        $interface->assign('record', $this->record);
        $interface->setPageTitle($this->record['Title'][0]);

        // Assign the ID of the last search so the user can return to it.
        $interface->assign(
            'lastsearch',
            isset($_SESSION['lastSearchURL']) ? $_SESSION['lastSearchURL'] : false
        );

        // Set bX flag
        $interface->assign(
            'bXEnabled', isset($configArray['bX']['token'])
            ? true : false
        );
        
        // Display Page
        $interface->setTemplate('record.tpl');
        $interface->display('layout.tpl');
    }
}

?>
