<?php
/**
 * Search action for EBSCO module
 *
 * PHP version 5
 *
 * Copyright (C) Andrew Nagy 2009.
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
 * @package  Controller_Summon
 * @author   Andrew Nagy <vufind-tech@lists.sourceforge.net>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */
require_once 'Base.php';
require_once 'sys/Pager.php';

/**
 * Search action for EBSCO module
 *
 * @category VuFind
 * @package  Controller_EBSCO
 * @author   Andrew Nagy <vufind-tech@lists.sourceforge.net>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */
class Search extends Base
{
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

        // TODO: something else than a hacky IP address check
        $config = getExtraConfigArray("EBSCO");
        if (isset($config['Access']['ip_ranges'])) {
            $found = false;
            $remote = sprintf('%u', ip2long($_SERVER['REMOTE_ADDR']));
            $ranges = explode(',', $config['Access']['ip_ranges']);
            foreach ($ranges as $range) {
                $ips = explode('-', $range);
                if (!isset($ips[0])) {
                    continue;
                }
                $start = sprintf('%u', ip2long($ips[0]));
                if (!isset($ips[1])) {
                    $end = $start;
                } else {
                    $end = sprintf('%u', ip2long($ips[1]));
                }
                if ($remote >= $start && $remote <= $end) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                die ("Access denied from '" . $_SERVER['REMOTE_ADDR'] . "'");
            }
        }
        
        // Initialise SearchObject.
        $this->searchObject->init();

        $displayQuery = $this->searchObject->displayQuery();
        $interface->setPageTitle(
            translate('Search Results') .
            (empty($displayQuery) ? '' : ' - ' . htmlspecialchars($displayQuery))
        );

        $interface->assign('lookfor', $displayQuery);
        $interface->assign('searchIndex', $this->searchObject->getSearchIndex());
        $interface->assign('searchType', $this->searchObject->getSearchType());

        // Search EBSCO
        $result = $this->searchObject->processSearch(true, true);

        // We'll need recommendations no matter how many results we found:
        $interface->assign('qtime', round($this->searchObject->getQuerySpeed(), 2));
        $interface->assign(
            'spellingSuggestions', $this->searchObject->getSpellingSuggestions()
        );
        $interface->assign(
            'topRecommendations',
            $this->searchObject->getRecommendationsTemplates('top')
        );
        $interface->assign(
            'sideRecommendations',
            $this->searchObject->getRecommendationsTemplates('side')
        );

        if ($result['recordCount'] > 0) {
            // If the "jumpto" parameter is set, jump to the specified result index:
            $this->_processJumpto($result);

            $summary = $this->searchObject->getResultSummary();
            $page = $summary['page'];
            $interface->assign('recordCount', $summary['resultTotal']);
            $interface->assign('recordStart', $summary['startRecord']);
            $interface->assign('recordEnd',   $summary['endRecord']);
            $interface->assign('recordSet', $result['response']['docs']);
            $interface->assign('sortList',   $this->searchObject->getSortList());

            // If our result set is larger than the number of records that
            // EDS will let us page through, we should cut off the number
            // before passing it to our paging mechanism:
            $config = getExtraConfigArray('EBSCO');
            $pageLimit = isset($config['General']['result_limit']) ?
                $config['General']['result_limit'] : 2000;
            $totalPagerItems = $summary['resultTotal'] < $pageLimit ?
                $summary['resultTotal'] : $pageLimit;

            // Process Paging
            $link = $this->searchObject->renderLinkPageTemplate();
            $options = array('totalItems' => $totalPagerItems,
                             'fileName'   => $link,
                             'perPage'    => $summary['perPage']);
            $pager = new VuFindPager($options);
            $interface->assign('pageLinks', $pager->getLinks());

            // Display Listing of Results
            $interface->setTemplate('list.tpl');
            $interface->assign('subpage', 'EBSCO/list-list.tpl');
        } else {
            $interface->assign('recordCount', 0);
            // Was the empty result set due to an error?
            $error = $this->searchObject->getIndexError();
            if ($error !== false) {
                // If it's a parse error or the user specified an invalid field, we
                // should display an appropriate message:
                if (stristr($error, 'user.entered.query.is.malformed')
                    || stristr($error, 'unknown.field')
                ) {
                    $interface->assign('parseError', true);
                } else {
                    // Unexpected error -- let's treat this as a fatal condition.
                    PEAR::raiseError(
                        new PEAR_Error(
                            'Unable to process query<br />EBSCO Returned: ' . $error
                        )
                    );
                }
            }
            $interface->setTemplate('list-none.tpl');
        }

        // 'Finish' the search... complete timers and log search history.
        $this->searchObject->close();
        $interface->assign('time', round($this->searchObject->getTotalSpeed(), 2));
        // Show the save/unsave code on screen
        // The ID won't exist until after the search has been put in the search
        //    history so this needs to occur after the close() on the searchObject
        $interface->assign('showSaved',   true);
        $interface->assign('savedSearch', $this->searchObject->isSavedSearch());
        $interface->assign('searchId',    $this->searchObject->getSearchId());

        // Save the URL of this search to the session so we can return to it easily:
        $_SESSION['lastSearchURL'] = $this->searchObject->renderSearchUrl();

        $interface->display('layout.tpl');
    }

    /**
     * Process the "jumpto" parameter.
     *
     * @param array $result Summon result
     *
     * @return void
     * @access private
     */
    private function _processJumpto($result)
    {
        if (isset($_REQUEST['jumpto']) && is_numeric($_REQUEST['jumpto'])) {
            $i = intval($_REQUEST['jumpto'] - 1);
            if (isset($result['documents'][$i])) {
                $record = & $result['documents'][$i];
                $jumpUrl = 'Record?id=' . urlencode($record['ID'][0]);
                header('Location: ' . $jumpUrl);
                die();
            }
        }
    }
}

?>
