<?php
/**
 * LIDO Record Driver
 *
 * PHP version 5
 *
 * Copyright (C) Ere Maijala 2012.
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
 * @package  RecordDrivers
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/other_than_marc Wiki
 */
require_once 'RecordDrivers/IndexRecord.php';

/**
 * LIDO Record Driver
 *
 * This class is designed to handle LIDO records.
 *
 * @category VuFind
 * @package  RecordDrivers
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/other_than_marc Wiki
 */
class LidoRecord extends IndexRecord
{
    // LIDO record
    protected $xml;
    
    /**
     * Constructor.  We build the object using all the data retrieved
     * from the (Solr) index (which also happens to include the
     * 'fullrecord' field containing raw metadata).  Since we have to
     * make a search call to find out which record driver to construct,
     * we will already have this data available, so we might as well
     * just pass it into the constructor.
     *
     * @param array $indexFields All fields retrieved from the index.
     *
     * @access public
     */
    public function __construct($indexFields)
    {
        parent::__construct($indexFields);
        
        $this->xml = simplexml_load_string($this->fields['fullrecord']);
    }
    
    /**
    * Assign necessary Smarty variables and return a template name to
    * load in order to display core metadata (the details shown in the
    * top portion of the record view pages, above the tabs).
    *
    * @return string Name of Smarty template file to display.
    * @access public
    */
    public function getCoreMetadata()
    {
        global $interface;
        
        parent::getCoreMetadata();
        $interface->assign('coreImages', $this->getAllImages());
        if (in_array('Image', $this->getFormats()) && $this->getSubtitle() == '') {
            $interface->assign('coreSubtitle', $this->getDescription());
        }
        $summary = array();
        foreach ($this->xml->xpath('/lidoWrap/lido/descriptiveMetadata/objectRelationWrap/relatedWorksWrap/relatedWorkSet/relatedWork/displayObject') as $node) {
            $summary[] = (string)$node;
        }
        $interface->assign('coreSummary', implode(" -- ", $summary));
        if (isset($this->fields['measurements'])) {
            $interface->assign('coreMeasurements', $this->fields['measurements']);
        }
        $mainFormat = $this->getFormats();
        if (is_array($mainFormat)) {
            $mainFormat = $mainFormat[0] . '_';
        } else {
            $mainFormat = '';
        }

        $events = array();
        foreach ($this->xml->xpath('/lidoWrap/lido/descriptiveMetadata/eventWrap/eventSet/event') as $node) {
            $type = isset($node->eventType->term) ? translate('lido_event_type_' . $mainFormat . (string)$node->eventType->term) : '';
            $date = isset($node->eventDate->displayDate) ? (string)$node->eventDate->displayDate : '';
            $method = isset($node->eventMethod->term) ? (string)$node->eventMethod->term : '';
            $materials = isset($node->eventMaterialsTech->displayMaterialsTech) ? (string)$node->eventMaterialsTech->displayMaterialsTech : '';
            $place = isset($node->eventPlace->displayPlace) ? (string)$node->eventPlace->displayPlace : '';
            $actors = array();
            if (isset($node->eventActor->actorInRole)) {
                foreach ($node->eventActor->actorInRole as $actor) {
                    if (isset($actor->actor->nameActorSet->appellationValue)) {
                        $role = isset($actor->roleActor->term) ? $actor->roleActor->term : '';
                        $actors[] = array('name'  => $actor->actor->nameActorSet->appellationValue, 'role' => $role);
                    }        
                }
            }
            $place = isset($node->eventPlace->displayPlace) ? (string)$node->eventPlace->displayPlace : '';
            $event = array('type' => $type, 'date' => $date, 'method' => $method, 'materials' => $materials, 
            	'place' => $place, 'actors' => $actors);
            $events[] = $event;
        }
        $interface->assign('coreEvents', $events);
        
        return 'RecordDrivers/Lido/core.tpl';
    }
        
    /**
    * Assign necessary Smarty variables and return a template name for the current
    * view to load in order to display a summary of the item suitable for use in
    * search results.
    *
    * @param string $view The current view.
    *
    * @return string      Name of Smarty template file to display.
    * @access public
    */
    public function getSearchResult($view = 'list')
    {
        global $configArray;
        global $interface;
        
        parent::getSearchResult($view);
    
        $interface->assign('summImages', $this->getAllImages());
        if (in_array('Image', $this->getFormats()) && $this->getSubtitle() == '') {
            if ($this->getHighlightedTitle()) {
                $interface->assign('summHighlightedTitle', $this->getHighlightedTitle() . ' ' . $this->getDescription());
            }
            $summary = $this->getSummary();
            if ($summary) {
                $summary = ' ' . $summary[0];
            } else {
                $summary = '';
            }
            $interface->assign('summTitle', $this->getTitle() . $summary);
        }
        $interface->assign('summSubtitle', $this->getSubtitle());
        
        $mainFormat = $this->getFormats();
        if (is_array($mainFormat)) {
            $mainFormat = $mainFormat[0];
        } else {
            $mainFormat = '';
        }
        if (isset($this->fields['event_creation_displaydate_str'])) {
            if ($mainFormat == 'Image') {
                $interface->assign('summImageDate', $this->fields['event_creation_displaydate_str']);
            } else {
                $interface->assign('summCreationDate', $this->fields['event_creation_displaydate_str']);
            }
        }
        if (isset($this->fields['event_use_displaydate_str'])) {
            $interface->assign('summUseDate', $this->fields['event_use_displaydate_str']);
        }
        if (isset($this->fields['event_use_displayplace_str'])) {
            $interface->assign('summUsePlace', $this->fields['event_use_displayplace_str']);
        }
        
        return 'RecordDrivers/Lido/result.tpl';
    }
    
	/**
     * Return an associative array of image URLs associated with this record (key = URL,
     * value = description), if available; false otherwise. 
	 *
	 * @return mixed
	 * @access protected
	 */
    public function getAllImages()
    {
        $urls = array();
        $url = '';
        foreach ($this->xml->xpath('/lidoWrap/lido/administrativeMetadata/resourceWrap/resourceSet/resourceRepresentation/linkResource') as $node) {
            $url = (string)$node;
            $urls[$url] = '';
        }
        return $urls;
    }
    
	/**
	 * Return a URL to a thumbnail preview of the record, if available; false
	 * otherwise.
	 *
	 * @param array $size Size of thumbnail (small, medium or large -- small is
	 * default).
	 *
	 * @return mixed
	 * @access protected
	 */
	protected function getThumbnail($size = 'small')
	{
	    global $configArray;
		if (isset($this->fields['thumbnail']) && $this->fields['thumbnail']) {
		    return $configArray['Site']['url'] . '/thumbnail.php?id=' .
		        urlencode($this->getUniqueID()) . '&size=' . urlencode($size);
		}
		return false;
	}

	/**
	* Return the actual URL where a thumbnail can be retrieved, if available; false
	* otherwise.
	*
	* @param array $size Size of thumbnail (small, medium or large -- small is
	* default).
	*
	* @return mixed
	* @access public
	*/
	public function getThumbnailURL($size = 'small')
	{
	    global $configArray;
	    if (isset($this->fields['thumbnail']) && $this->fields['thumbnail']) {
	        return $this->fields['thumbnail'];
	    }
	    return false;
	}

	/**
	 * Get the description of the current record.
	 *
	 * @return string
	 * @access protected
	 */
	protected function getDescription()
	{
	    return isset($this->fields['description']) ?
	    $this->fields['description'] : '';
	}
	
}

?>
