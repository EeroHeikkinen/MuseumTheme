<?php
/**
 * FavoriteHandler Class
 *
 * PHP version 5
 *
 * Copyright (C) Villanova University 2010.
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
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */
require_once 'services/MyResearch/lib/Resource.php';
require_once 'sys/Pager.php';

/**
 * FavoriteHandler Class
 *
 * This class contains shared logic for displaying lists of favorites (based on
 * earlier logic duplicated between the MyResearch/Home and MyResearch/MyList
 * actions).
 *
 * @category VuFind
 * @package  Controller_MyResearch
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */
class FavoriteHandler
{
    private $_favorites;
    private $_user;
    private $_listId;
    private $_allowEdit;
    private $_records = array();
    protected $infoMsg = false;
    protected $sortOptions = array(
        'saved' => 'Order Added',
        'title' => 'Title',
        'author' => 'Author',
        'date' => 'Date',
        'format' => 'Format'
    );

    /**
     * Constructor.
     *
     * @param array  $favorites Array of Resource objects.
     * @param object $user      User object owning tag/note metadata.
     * @param int    $listId    ID of list containing desired tags/notes (or null
     * to show tags/notes from all user's lists).
     * @param bool   $allowEdit Should we display edit controls?
     * @param string $sort      Sort method
     *
     * @access public
     */
    public function __construct($favorites, $user, $listId = null, $allowEdit = true, $sort = 'saved')
    {
        $this->_favorites = is_array($favorites) ? $favorites : array($favorites);
        $this->_user = $user;
        $this->_listId = $listId;
        $this->_allowEdit = $allowEdit;
    }

    /**
     * Assign all necessary values to the interface.
     *
     * @return void
     * @access public
     */
    public function assign()
    {
        global $interface;

        $currentSort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'saved'; 
        if (!isset($this->sortOptions[$currentSort])) {
            $currentSort = 'saved';
        }
        
        $interface->assign('listEditAllowed', $this->_allowEdit);

        // Get html for the records
        $resourceList = array();
        $searchObjects = array();
        $searchObject = SearchObjectFactory::initSearchObject();
        foreach ($this->_favorites as $favorite) {
            $source = $favorite->source;
            $data = $favorite->data;
            if (!empty($data)) {
                $data = unserialize($data);
            }
            $sortKey = $favorite->saved;
            if ($source == 'VuFind') {
                if (empty($data)) {
                    // Fetch data from index for backwards compatibility and store it in the resource
                    $data = $searchObject->getIndexEngine()->getRecord($favorite->record_id);
                    $resource = new Resource();
                    $resource->id = $favorite->id;
                    $resource->source = $favorite->source;
                    if ($resource->find(true)) {
                        $resource->data = serialize($data);
                        $resource->update();
                    }
                }
                $record = RecordDriverFactory::initRecordDriver($data);
                $html = $interface->fetch($record->getListEntry($this->_user, $this->_listId, $this->_allowEdit));
                switch ($currentSort) {
                case 'title': 
                    $sortKey = isset($data['title_sort']) ? $data['title_sort'] : ''; 
                    break;
                case 'author': 
                    $sortKey = isset($data['author']) ? $data['author'] : ''; 
                    break;
                case 'date': 
                    $sortKey = isset($data['main_date_str']) ? $data['main_date_str'] : isset($data['publishDate'][0]) ? $data['publishDate'][0] : ''; 
                    break;
                case 'format': 
                    $sortKey = isset($data['format'][0]) ? translate($data['format'][0]) : ''; 
                    break;
                }
            } else {
                if (!isset($searchObjects[$source])) {
                    $searchObjects[$source] = SearchObjectFactory::initSearchObject($source);
                    if ($searchObjects[$source] === false) {
                        error_log("Could not create search object for source '$source'");
                        continue;
                    }
                }
                $html = $searchObjects[$source]->getResultHTML(
                    $data,
                    $this->_user,
                    $this->_listId,
                    $this->_allowEdit
                );
                switch ($currentSort) {
                case 'title': 
                    $sortKey = isset($data['Title'][0]) ? $data['Title'][0] : ' '; 
                    break;
                case 'author': 
                    $sortKey = isset($data['Author'][0]) ? $data['Author'][0] : ' '; 
                    break;
                case 'date': 
                    $sortKey = isset($data['main_date_str']) ? $data['main_date_str'] : isset($data['publicationDate'][0]) ? $data['publicationDate'][0] : ' '; 
                    break;
                case 'format': 
                    $sortKey = isset($data['format'][0]) ? translate($data['format'][0]) : ' '; 
                    break;
                }
            }
            $sortKey .= '_' . $favorite->record_id;
            $resourceList[$sortKey] = $html;
        }

        // Setup paging variables 
        if (isset($_REQUEST['page'])) {
            $page = $_REQUEST['page'];
            $page = intval($page);
            if ($page < 1) {
                $page = 1;
            }
        } else {
            $page = 1;
        } 
        $perPage = 20; // TODO: configurable?
        $recordCount = count($this->_favorites);
        $startRecord = ($page - 1) * $perPage;
        
        // Sort and slice the array
        ksort($resourceList);
        $resourceList = array_slice($resourceList, $startRecord, $perPage, true);
        
        $html = array();
        $interface->assign('resourceList', $resourceList);
        
        // Set up paging of list contents:
        $endRecord = $startRecord + $perPage;
        if ($endRecord > $recordCount) {
            $endRecord = $recordCount;
        }
        $interface->assign('recordCount', $recordCount);
        $interface->assign('recordStart', $startRecord + 1);
        $interface->assign('recordEnd', $endRecord);

        $searchObject->init();
        $options = array(
            'totalItems' => $recordCount,
            'perPage' => $perPage,
            'fileName' => $searchObject->renderLinkPageTemplate()
        );
        $pager = new VuFindPager($options);
        $interface->assign('pageLinks', $pager->getLinks());
        
        // Sorting options
        $sortList = array();
        foreach ($this->sortOptions as $sort => $desc) {
            $sortList[$sort] = array(
                'sortUrl'  => $searchObject->renderLinkWithSort($sort),
                'desc' => $desc,
                'selected' => ($sort == $currentSort)
            );
        }
        $interface->assign('sortList', $sortList);
    }

    /**
     * Get info message, if any (boolean false if no message).
     *
     * @return string|bool
     * @access public
     */
    public function getInfoMsg()
    {
        return $this->infoMsg;
    }
}

?>