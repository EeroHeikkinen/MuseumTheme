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
 * @author   Kalle Pyykkönen <kalle.pyykkonen@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */
require_once 'Action.php';
require_once 'sys/Proxy_Request.php';

/**
 * Ajax page for popular searches from Piwik
 *
 * @category VuFind
 * @package  Controller_AJAX
 * @author   Kalle Pyykkönen <kalle.pyykkonen@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */
class AJAX_PopularSearches extends Action
{
    /**
     * Constructor.
     *
     * @access public
     */
    public function __construct()
    {
        parent::__construct();
        $_SESSION['no_store'] = true; 
    }
    
    /**
     * Get popular search terms and return html snippet
     * 
     * @return void
     */
    public function launch()
    {
        global $interface, $configArray;
        $options = array(
            'module'       => 'API',
            'format'       => 'json',
            'method'       => 'Actions.getSiteSearchKeywords',
            'idSite'       => $configArray['Piwik']['site_id'],
            'period'       => 'week',
            'date'         => date('Y-m-d'),
            'token_auth'   => $configArray['Piwik']['token_auth']
        );
        $url = $configArray['Piwik']['url'];
        // Retrieve data from Piwik
        $request = new Proxy_Request();
        $request->setMethod(HTTP_REQUEST_METHOD_GET);
        $request->setURL($url);
        // Load request parameters:
        foreach ($options as $key => $value) {
            $request->addQueryString($key, $value);
        }
        // Perform request and die on error:
        $result = $request->sendRequest();
        if (PEAR::isError($result)) {
            die($result->getMessage() . "\n");
        }
        $response = json_decode($request->getResponseBody(), true);
        $searchPhrases = array();
        if (isset($response['result']) && $response['result'] == 'error') {
            $logger = new Logger();
            $logger->log('Piwik error: ' . $response['message'], PEAR_LOG_ERR);
        } else {
            foreach ($response as $item) {
                $searchPhrases[ $item['label'] ] = !isset($item['nb_actions']) || is_null($item['nb_actions']) ? $item['nb_visits'] : $item['nb_actions'];
            }
            // Order by hits
            arsort($searchPhrases);
            
        }
        // Assign values only and 10 first items
        $interface->assign('searchPhrases', array_slice(array_keys($searchPhrases), 0, 10));
        $interface->display('AJAX/popularSearches.tpl');
    }
}
