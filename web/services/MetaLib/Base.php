<?php
/**
 * Base class for most MetaLib module actions.
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
require_once 'Action.php';
require_once 'sys/SearchObject/MetaLib.php';

/**
 * Base class for most MetaLib module actions.
 *
 * @category VuFind
 * @package  Controller_MetaLib
 * @author   Andrew Nagy <vufind-tech@lists.sourceforge.net>
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */
class Base extends Action
{
    protected $searchObject;

    /**
     * Constructor
     *
     * @access public
     */
    public function __construct()
    {
        global $interface;
        $interface->assign('currentTab', 'MetaLib');

        // Send MetaLib search types to the template so the basic search box can
        // function on all pages of the MetaLib.
        $this->searchObject = SearchObjectFactory::initSearchObject('MetaLib');

        $sets = $this->searchObject->getSearchSets();
        if (isset($_REQUEST['set']) && strncmp($_REQUEST['set'], '_ird:', 5) == 0) {
            $ird = substr($_REQUEST['set'], 5);
            if (preg_match('/\W/', $ird)) {
                PEAR::raiseError(new PEAR_Error('Invalid parameter'));
            }
            $irdInfo = $this->searchObject->getIRDInfo($ird);
            if ($irdInfo === false) {
                PEAR::raiseError(new PEAR_Error('Invalid parameter'));
            }
            // TODO: IP/login authorization
            if (strcasecmp($irdInfo['access'], 'guest') != 0) {
                PEAR::raiseError(new PEAR_Error('Search in this database is not allowed with current credentials. Log in or use a proxy to search this database.'));
            }
            
            // Add selected ird as a virtual search set in the beginning and select it
            $sets = array_reverse($sets, true);
            $sets["_ird:$ird"] = $irdInfo['name'];
            $sets = array_reverse($sets, true);
            $interface->assign('searchSet', "_ird:$ird");
        }

        $interface->assign(
            'metalibSearchTypes', $this->searchObject->getBasicTypes()
        );
        $interface->assign(
            'metalibSearchSets', $sets
        );
        
        // Increase max execution time to allow slow MetaLib searches to complete
        set_time_limit(60); 
    }
}
