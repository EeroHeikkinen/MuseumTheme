<?php
/**
 * EAD Record Driver
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
 * EAD Record Driver
 *
 * This class is designed to handle EAD records.
 *
 * @category VuFind
 * @package  RecordDrivers
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/other_than_marc Wiki
 */
class EadRecord extends IndexRecord
{
    
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
        
        $template = parent::getCoreMetadata();
        
        $interface->assign('coreSubtitle', $this->_getYearRange());
        
        return $template;
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
        global $interface;
        
        $template = parent::getSearchResult($view);
        
        $title = $this->getTitle();
        $years = $this->_getYearRange();
        if ($years) {
            $title .= " $years";
        }
        $interface->assign('summTitle', $title);
        
        return $template;
    }
        
    
    /**
    * Return an associative array of URLs associated with this record (key = URL,
    * value = description).
    *
    * @return array
    * @access protected
    */
    protected function getURLs()
    {
        $record = simplexml_load_string($this->fields['fullrecord']);
        $urls = array();
        $url = '';
        foreach ($record->xpath('//daoloc') as $node) {
            $url = (string)$node->attributes()->href;
            if ($node->daodesc) {
                if ($node->daodesc->p) {
                    $urls[$url] = (string)$node->daodesc->p;
                } else {
                    $urls[$url] = (string)$node->daodesc;
                }
            } else {
                $urls[$url] = $url;
            }
        }
        return $urls;
    }
    
    protected function _getYearRange()
    {
        if (isset($this->fields['unit_daterange'])) {
            $dates = explode(',', $this->fields['unit_daterange']);
            $startYear = substr($dates[0], 0, 4);
            $endYear = substr($dates[1], 0, 4);
            $yearRange = '';
            if ($startYear !== '0000') {
                $yearRange .= $startYear;
            }
            $yearRange .= '-';
            if ($endYear !== '9999') {
                $yearRange .= $endYear;
            }
            return $yearRange;
        }
        return '';
    }
}

?>
