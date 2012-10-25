<?php
/**
 * MARC Record Driver
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
 * @package  RecordDrivers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/other_than_marc Wiki
 */
require_once 'RecordDrivers/IndexRecord.php';

/**
 * MARC Record Driver
 *
 * This class is designed to handle MARC records.  Much of its functionality
 * is inherited from the default index-based driver.
 *
 * @category VuFind
 * @package  RecordDrivers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/other_than_marc Wiki
 */
class MarcRecord extends IndexRecord
{
    protected $marcRecord;

    /**
     * Constructor.  We build the object using all the data retrieved
     * from the (Solr) index (which also happens to include the
     * 'fullrecord' field containing raw metadata).  Since we have to
     * make a search call to find out which record driver to construct,
     * we will already have this data available, so we might as well
     * just pass it into the constructor.
     *
     * @param array $record All fields retrieved from the index.
     *
     * @access public
     */
    public function __construct($record)
    {
        // Call the parent's constructor...
        parent::__construct($record);

        // Also process the MARC record:
        $marc = trim($record['fullrecord']);

        // check if we are dealing with MARCXML
        if (substr($marc, 0, 1) == '<') {
            $marc = new File_MARCXML($marc, File_MARCXML::SOURCE_STRING);
        } else {
            $marc = preg_replace('/#31;/', "\x1F", $marc);
            $marc = preg_replace('/#30;/', "\x1E", $marc);
            $marc = new File_MARC($marc, File_MARC::SOURCE_STRING);
        }

        $this->marcRecord = $marc->next();
        if (!$this->marcRecord) {
            PEAR::raiseError(new PEAR_Error('Cannot Process MARC Record'));
        }
    }

    /**
     * Assign necessary Smarty variables and return a template name to
     * load in order to export the record in the requested format.  For
     * legal values, see getExportFormats().  Returns null if format is
     * not supported.
     *
     * @param string $format Export format to display.
     *
     * @return string        Name of Smarty template file to display.
     * @access public
     */
    public function getExport($format)
    {
        global $interface;

        switch(strtolower($format)) {
        case 'endnote':
            // This makes use of core metadata fields in addition to the
            // assignment below:
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private", false);
            header('Content-type: application/x-endnote-refer');
            header("Content-Disposition: attachment; filename=\"vufind.enw\";");
            $interface->assign('marc', $this->marcRecord);
            return 'RecordDrivers/Marc/export-endnote.tpl';
        case 'marc':
            header('Content-type: application/MARC');
            header(
                "Content-Disposition: attachment; filename=\"VuFindExport.mrc\";"
            );
            $interface->assign('rawMarc', $this->marcRecord->toRaw());
            return 'RecordDrivers/Marc/export-marc.tpl';
        case 'marcxml':
            // Send this as marcXML & give it our bib as a name
            header('Content-disposition: attachment; filename="VuFindExport.xml";');
            header("Content-type: text/xml");
            $interface->assign('rawMarc', $this->marcRecord->toXML());
            return 'RecordDrivers/Marc/export-marc.tpl';
        case 'rdf':
            header("Content-type: application/rdf+xml");
            $interface->assign('rdf', $this->getRDFXML());
            return 'RecordDrivers/Marc/export-rdf.tpl';
        case 'refworks':
            // To export to RefWorks, we actually have to redirect to
            // another page.  We'll do that here when the user requests a
            // RefWorks export, then we'll call back to this module from
            // inside RefWorks using the "refworks_data" special export format
            // to get the actual data.
            $this->redirectToRefWorks();
            break;
        case 'refworks_data':
            // This makes use of core metadata fields in addition to the
            // assignment below:
            header('Content-type: text/plain; charset=utf-8');
            $interface->assign('marc', $this->marcRecord);
            return 'RecordDrivers/Marc/export-refworks.tpl';
            break;
        case 'bibtex':
            // This makes use of core metadata fields in addition to the
            // assignment below:
            header('Content-type: application/x-bibtex; charset=utf-8');
            $interface->assign('marc', $this->marcRecord);
            return 'RecordDrivers/Marc/export-bibtex.tpl';
            break;
        default:
            return null;
        }
    }

    /**
     * Get an array of strings representing formats in which this record's
     * data may be exported (empty if none).  Legal values: "RefWorks",
     * "EndNote", "MARC", "RDF".
     *
     * @return array Strings representing export formats.
     * @access public
     */
    public function getExportFormats()
    {
        // Get an array of legal export formats (from config array, or use defaults
        // if nothing in config array).
        global $configArray;
        $active = isset($configArray['Export']) ?
            $configArray['Export'] : array('RefWorks' => true, 'EndNote' => true);

        // These are the formats we can possibly support if they are turned on in
        // config.ini:
        $possible = array('RefWorks', 'EndNote', 'MARC', 'MARCXML', 'RDF', 'BibTeX');

        // Check which formats are currently active:
        $formats = array();
        foreach ($possible as $current) {
            if ($active[$current]) {
                $formats[] = $current;
            }
        }

        // Send back the results:
        return $formats;
    }

    /**
     * Assign necessary Smarty variables and return a template name to
     * load in order to display holdings extracted from the base record
     * (i.e. URLs in MARC 856 fields) and, if necessary, the ILS driver.
     * Returns null if no data is available.
     *
     * @param array $patron An array of patron data
     *
     * @return string Name of Smarty template file to display.
     * @access public
     */
    public function getHoldings($patron = false)
    {
        global $interface;

        if ("driver" == CatalogConnection::getHoldsMode()) {
            $interface->assign('driverMode', true);
            if (!UserAccount::isLoggedIn()) {
                $interface->assign('showLoginMsg', true);
            }
        }

        if ("driver" == CatalogConnection::getTitleHoldsMode()) {
            $interface->assign('titleDriverMode', true);
            if (!UserAccount::isLoggedIn()) {
                $interface->assign('showTitleLoginMsg', true);
            }
        }
        $interface->assign("holdingTitleHold", $this->getRealTimeTitleHold($patron));

        return parent::getHoldings($patron);
    }

    /**
     * Get an XML RDF representation of the data in this record.
     *
     * @return mixed XML RDF data (false if unsupported or error).
     * @access public
     */
    public function getRDFXML()
    {
        // Get Record as MARCXML
        $xml = trim($this->marcRecord->toXML());

        // Load Stylesheet
        $style = new DOMDocument;
        //$style->load('services/Record/xsl/MARC21slim2RDFDC.xsl');
        $style->load('services/Record/xsl/record-rdf-mods.xsl');

        // Setup XSLT
        $xsl = new XSLTProcessor();
        $xsl->importStyleSheet($style);

        // Transform MARCXML
        $doc = new DOMDocument;
        if ($doc->loadXML($xml)) {
            return $xsl->transformToXML($doc);
        }

        // If we got this far, something went wrong.
        return false;
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
        
        // get other links from MARC field 787
        $interface->assign('coreOtherLinks', $this->getOtherLinks());
        
        // MARC results work just like index results, except that we want to
        // enable the AJAX status display since we assume that MARC records
        // come from the ILS:
        $template = parent::getSearchResult($view);
        $interface->assign('summAjaxStatus', true);
        return $template;
    }

    /**
     * Assign necessary Smarty variables and return a template name to
     * load in order to display the full record information on the Staff
     * View tab of the record view page.
     *
     * @return string Name of Smarty template file to display.
     * @access public
     */
    public function getStaffView()
    {
        global $interface;

        // Get Record as MARCXML
        $xml = trim($this->marcRecord->toXML());

        // Prevent unprintable characters from interfering with the XSL transform:
        $xml = str_replace(
            array(chr(27), chr(28), chr(29), chr(30), chr(31)), ' ', $xml
        );

        // Transform MARCXML
        $style = new DOMDocument;
        $style->load('services/Record/xsl/record-marc.xsl');
        $xsl = new XSLTProcessor();
        $xsl->importStyleSheet($style);
        $doc = new DOMDocument;
        if ($doc->loadXML($xml)) {
            $html = $xsl->transformToXML($doc);
            $interface->assign('marc', $html);
        }
        $interface->assign('details', $this->fields);
        
        return 'RecordDrivers/Marc/staff.tpl';
    }

    /**
     * Assign necessary Smarty variables and return a template name to
     * load in order to display the Table of Contents extracted from the
     * record.  Returns null if no Table of Contents is available.
     *
     * @return string Name of Smarty template file to display.
     * @access public
     */
    public function getTOC()
    {
        global $interface;

        // Return null if we have no table of contents:
        $fields = $this->marcRecord->getFields('505');
        if (!$fields) {
            return null;
        }

        // If we got this far, we have a table -- collect it as a string:
        $toc = '';
        foreach ($fields as $field) {
            $subfields = $field->getSubfields();
            foreach ($subfields as $subfield) {
                $toc .= $subfield->getData();
            }
        }

        // Assign the appropriate variable and return the template name:
        $interface->assign('toc', $toc);
        return 'RecordDrivers/Marc/toc.tpl';
    }

    /**
     * Assign necessary Smarty variables and return a template name to 
     * load in order to display component parts extracted from the 
     * record.  Returns null if no component parts are available.
     *
     * @return string Name of Smarty template file to display.
     * @access public
     */
    public function getContainedComponentParts()
    {
        global $interface;
        global $configArray;
        
        $baseURI     = $configArray['Site']['url'];
        
        // Collect the values of all the 979 fields and their subfields into 
        // an array of arrays to be handed to a template for display.
        $fields = $this->marcRecord->getFields('979');
        if (!$fields) {
            return null;
        } else {
            $partOrderCounter = 0;                
            foreach ($fields as $field) {
                $partAuthor = '';
                $partAdditionalAuthors = '';
                $partAuthors = '';
                $subfields = $field->getSubfields();
                foreach ($subfields as $subfield) {
                    $subfieldCode = $subfield->getCode();
                    switch ($subfieldCode) {
                    case 'a':
                        $partOrderCounter++;
                        $partCode = $subfield->getData();
                        break;
                    case 'b':
                        $partTitle = $subfield->getData();
                        break;
                    case 'c':
                        if ($partAuthor) {
                            $partAuthor .= '; ';
                        }    
                        $partAuthor .= $subfield->getData();
                        break;
                    case 'd':
                        if ($partAdditionalAuthors) {
                            $partAdditionalAuthors .= '; ';
                        } 
                        $partAdditionalAuthors .= $subfield->getData();
                        break;          
                    }              
                }
                if ($partAuthor && $partAdditionalAuthors) {
                    $partAuthors = $partAuthor . '; ';
                }
                $partAuthors .= $partAdditionalAuthors;  
                $componentparts[] = array(
                                        'number' => $partOrderCounter,
                                        'title' => $partTitle,
                                        'link' => $baseURI . '/Record/' . $partCode,
                                        'author' => $partAuthors
                );
            }
            
        }   
        // Assign the appropriate variable and return the template name:
        $interface->assign('componentparts', $componentparts);
        return 'RecordDrivers/Marc/componentparts.tpl';
    }    
    
    /**
     * Return an XML representation of the record using the specified format.
     * Return false if the format is unsupported.
     *
     * @param string $format Name of format to use (corresponds with OAI-PMH
     * metadataPrefix parameter).
     *
     * @return mixed         XML, or false if format unsupported.
     * @access public
     */
    public function getXML($format)
    {
        // Special case for MARC:
        if ($format == 'marc21') {
            $xml = $this->marcRecord->toXML();
            $xml = str_replace(
                array(chr(27), chr(28), chr(29), chr(30), chr(31)), ' ', $xml
            );
            $xml = simplexml_load_string($xml);
            if (!$xml || !isset($xml->record)) {
                return false;
            }

            // Set up proper namespacing and extract just the <record> tag:
            $xml->record->addAttribute('xmlns', "http://www.loc.gov/MARC21/slim");
            $xml->record->addAttribute(
                'xsi:schemaLocation',
                'http://www.loc.gov/MARC21/slim ' .
                'http://www.loc.gov/standards/marcxml/schema/MARC21slim.xsd',
                'http://www.w3.org/2001/XMLSchema-instance'
            );
            $xml->record->addAttribute('type', 'Bibliographic');
            return $xml->record->asXML();
        }

        // Try the parent method:
        return parent::getXML($format);
    }

    /**
     * Does this record have a Table of Contents available?
     *
     * @return bool
     * @access public
     */
    public function hasTOC()
    {
        // Is there a table of contents in the MARC record?
        if ($this->marcRecord->getFields('505')) {
            return true;
        }
        return false;
    }

    /**
     * Does this record have Component Parts available?
     *
     * @return bool
     * @access public
     */
    public function hasContainedComponentParts()
    {
        // Are there contained component parts in the MARC record (979)?
        if ($this->marcRecord->getFields('979')) {
            return true;
        }
        return false;
    }    
    
    /**
     * Does this record support an RDF representation?
     *
     * @return bool
     * @access public
     */
    public function hasRDF()
    {
        return true;
    }

    /**
     * Get access restriction notes for the record.
     *
     * @return array
     * @access protected
     */
    protected function getAccessRestrictions()
    {
        return $this->getFieldArray('506');
    }

    /**
     * Get all subject headings associated with this record.  Each heading is
     * returned as an array of chunks, increasing from least specific to most
     * specific.
     *
     * @return array
     * @access protected
     */
    protected function getAllSubjectHeadings()
    {
        // These are the fields that may contain subject headings:
        $fields = array(
            '600', '610', '611', '630', '648', '650', '651', '656'
        );

        // This is all the collected data:
        $retval = array();

        // Try each MARC field one at a time:
        foreach ($fields as $field) {
            // Do we have any results for the current field?  If not, try the next.
            $results = $this->marcRecord->getFields($field);
            if (!$results) {
                continue;
            }

            // If we got here, we found results -- let's loop through them.
            foreach ($results as $result) {
                // Start an array for holding the chunks of the current heading:
                $current = array();

                // Get all the chunks and collect them together:
                $subfields = $result->getSubfields();
                if ($subfields) {
                    foreach ($subfields as $subfield) {
                        // Numeric subfields are for control purposes and should not
                        // be displayed:
                        if (!is_numeric($subfield->getCode())) {
                            $current[] = $subfield->getData();
                        }
                    }
                    // If we found at least one chunk, add a heading to our result:
                    if (!empty($current)) {
                        $current[count($current) - 1] = $this->stripTrailingPunctuation($current[count($current) - 1]);
                        $retval[] = $current;
                    }
                }
            }
        }

        // Send back everything we collected:
        return $retval;
    }

    /**
     * Get award notes for the record.
     *
     * @return array
     * @access protected
     */
    protected function getAwards()
    {
        return $this->getFieldArray('586');
    }

    /**
     * Get the bibliographic level of the current record.
     *
     * @return string
     * @access protected
     */
    protected function getBibliographicLevel()
    {
        $leader = $this->marcRecord->getLeader();
        $biblioLevel = strtoupper($leader[7]);

        switch ($biblioLevel) {
        case 'M':   // Monograph
            return "Monograph";
        case 'S':   // Serial
            return "Serial";
        case 'A':   // Monograph Part
            return "MonographPart";
        case 'B':   // Serial Part
            return "SerialPart";
        case 'C':   // Collection
            return "Collection";
        case 'D':   // Collection Part
            return "CollectionPart";
        default:
            return "Unknown";
        }
    }

    /**
     * Get notes on bibliography content.
     *
     * @return array
     * @access protected
     */
    protected function getBibliographyNotes()
    {
        return $this->getFieldArray('504');
    }

    /**
     * Get the main corporate author (if any) for the record.
     *
     * @return string
     * @access protected
     */
    protected function getCorporateAuthor()
    {
        return $this->getFirstFieldValue('110', array('a', 'b'));
    }

    /**
     * Return an array of all values extracted from the specified field/subfield
     * combination.  If multiple subfields are specified and $concat is true, they
     * will be concatenated together in the order listed -- each entry in the array
     * will correspond with a single MARC field.  If $concat is false, the return
     * array will contain separate entries for separate subfields.
     *
     * @param string $field     The MARC field number to read
     * @param array  $subfields The MARC subfield codes to read
     * @param bool   $concat    Should we concatenate subfields?
     *
     * @return array
     * @access protected
     */
    protected function getFieldArray($field, $subfields = null, $concat = true)
    {
        // Default to subfield a if nothing is specified.
        if (!is_array($subfields)) {
            $subfields = array('a');
        }

        // Initialize return array
        $matches = array();

        // Try to look up the specified field, return empty array if it doesn't
        // exist.
        $fields = $this->marcRecord->getFields($field);
        if (!is_array($fields)) {
            return $matches;
        }

        // Extract all the requested subfields, if applicable.
        foreach ($fields as $currentField) {
            $next = $this->getSubfieldArray($currentField, $subfields, $concat);
            $matches = array_merge($matches, $next);
        }

        return $matches;
    }

    /**
     * Get notes on finding aids related to the record.
     *
     * @return array
     * @access protected
     */
    protected function getFindingAids()
    {
        return $this->getFieldArray('555');
    }

    /**
     * Get the first value matching the specified MARC field and subfields.
     * If multiple subfields are specified, they will be concatenated together.
     *
     * @param string $field     The MARC field to read
     * @param array  $subfields The MARC subfield codes to read
     *
     * @return string
     * @access protected
     */
    protected function getFirstFieldValue($field, $subfields = null)
    {
        $matches = $this->getFieldArray($field, $subfields);
        return (is_array($matches) && count($matches) > 0) ?
            $matches[0] : null;
    }

    /**
     * Get general notes on the record.
     *
     * @return array
     * @access protected
     */
    protected function getGeneralNotes()
    {
        return $this->getFieldArray('500');
    }

    /**
     * Get the item's places of publication.
     *
     * @return array
     * @access protected
     */
    protected function getPlacesOfPublication()
    {
        return $this->getFieldArray('260');
    }

    /**
     * Get an array of playing times for the record (if applicable).
     *
     * @return array
     * @access protected
     */
    protected function getPlayingTimes()
    {
        $times = $this->getFieldArray('306', array('a'), false);

        // Format the times to include colons ("HH:MM:SS" format).
        for ($x = 0; $x < count($times); $x++) {
            $times[$x] = substr($times[$x], 0, 2) . ':' .
                substr($times[$x], 2, 2) . ':' .
                substr($times[$x], 4, 2);
        }

        return $times;
    }

    /**
     * Get credits of people involved in production of the item.
     *
     * @return array
     * @access protected
     */
    protected function getProductionCredits()
    {
        return $this->getFieldArray('508');
    }

    /**
     * Get an array of publication frequency information.
     *
     * @return array
     * @access protected
     */
    protected function getPublicationFrequency()
    {
        return $this->getFieldArray('310', array('a', 'b'));
    }

    /**
     * Check if an item has holdings in order to show or hide the holdings tab
     *
     * @return bool
     * @access public
     */
    public function hasHoldings()
    {
        // Get Acquisitions Data
        $id = $this->getUniqueID();
        $catalog = ConnectionManager::connectToCatalog();
        if ($catalog && $catalog->status) {
            $result = $catalog->hasHoldings($id);
            if (PEAR::isError($result)) {
                PEAR::raiseError($result);
            }
            return $result;
        }
        // Show holdings tab by default
        return true;
    }

    /**
     * Get Status/Holdings Information from the Marc Record (support method used by
     * the NoILS driver).
     *
     * @param array $field The Marc Field to retrieve
     * @param array $data  A keyed array of data to retrieve from subfields
     *
     * @return array
     * @access public
     */
    public function getFormattedMarcDetails($field, $data)
    {
        // Initialize return array
        $matches = array();
        $i = 0;

        // Try to look up the specified field, return empty array if it doesn't
        // exist.
        $fields = $this->marcRecord->getFields($field);
        if (!is_array($fields)) {
            return $matches;
        }

        // Extract all the requested subfields, if applicable.
        foreach ($fields as $currentField) {
            foreach ($data as $key => $info) {
                $split = explode("|", $info);
                if ($split[0] == "msg") {
                    if ($split[1] == "true") {
                        $result = true;
                    } elseif ($split[1] == "false") {
                        $result = false;
                    } else {
                        $result =$split[1];
                    }
                    $matches[$i][$key] = $result;
                } else {
                    // Default to subfield a if nothing is specified.
                    if (count($split) < 2) {
                        $subfields = array('a');
                    } else {
                        $subfields = str_split($split[1]);
                    }
                    $result = $this->getSubfieldArray(
                        $currentField, $subfields, true
                    );
                    $matches[$i][$key] = count($result) > 0
                        ? (string)$result[0] : '';
                }
            }
            $matches[$i]['id'] = $this->getUniqueID();
            $i++;
        }
        return $matches;
    }

    /**
     * Get an array of information about record history, obtained in real-time
     * from the ILS.
     *
     * @return array
     * @access protected
     */
    protected function getRealTimeHistory()
    {
        // Get Acquisitions Data
        $id = $this->getUniqueID();
        $catalog = ConnectionManager::connectToCatalog();
        if ($catalog && $catalog->status) {
            $result = $catalog->getPurchaseHistory($id);
            if (PEAR::isError($result)) {
                PEAR::raiseError($result);
            }
            return $result;
        }
        return array();
    }

    /**
     * Get an array of information about record holdings, obtained in real-time
     * from the ILS.
     *
     * @param array $patron An array of patron data
     *
     * @return array
     * @access protected
     */
    protected function getRealTimeHoldings($patron = false)
    {
        // Get ID and connect to catalog
        $id = $this->getUniqueID();
        $catalog = ConnectionManager::connectToCatalog();

        include_once 'sys/HoldLogic.php';
        $holdLogic = new HoldLogic($catalog);

        return $holdLogic->getHoldings($id, $patron);

    }

    /**
     * Get a link for placing a title level hold.
     *
     * @param array $patron An array of patron data
     *
     * @return mixed A url if a hold is possible, boolean false if not
     * @access protected
     */
    protected function getRealTimeTitleHold($patron = false)
    {
        global $configArray;

        $biblioLevel = $this->getBibliographicLevel();

        if ("monograph" == strtolower($biblioLevel)
            || stristr("part", $biblioLevel)
        ) {
            $titleHoldEnabled = CatalogConnection::getTitleHoldsMode();

            if ($titleHoldEnabled != "disabled") {
                include_once 'sys/HoldLogicTitle.php';

                // Get ID and connect to catalog
                $id = $this->getUniqueID();
                $catalog = ConnectionManager::connectToCatalog();
                $holdLogic = new HoldLogicTitle($catalog);
                return $holdLogic->getHold($id, $patron);
            }
        }
        return false;
    }

    /**
     * Get an array of strings describing relationships to other items.
     *
     * @return array
     * @access protected
     */
    protected function getRelationshipNotes()
    {
        return $this->getFieldArray('580');
    }

    /**
     * Get an array of all series names containing the record.  Array entries may
     * be either the name string, or an associative array with 'name' and 'number'
     * keys.
     *
     * @return array
     * @access protected
     */
    protected function getSeries()
    {
        $matches = array();

        // First check the 440, 800 and 830 fields for series information:
        $primaryFields = array(
            '440' => array('a', 'p'),
            '800' => array('a', 'b', 'c', 'd', 'f', 'p', 'q', 't'),
            '830' => array('a', 'p'));
        $matches = $this->getSeriesFromMARC($primaryFields);
        if (!empty($matches)) {
            return $matches;
        }

        // Now check 490 and display it only if 440/800/830 were empty:
        $secondaryFields = array('490' => array('a'));
        $matches = $this->getSeriesFromMARC($secondaryFields);
        if (!empty($matches)) {
            return $matches;
        }

        // Still no results found?  Resort to the Solr-based method just in case!
        return parent::getSeries();
    }

    /**
     * Support method for getSeries() -- given a field specification, look for
     * series information in the MARC record.
     *
     * @param array $fieldInfo Associative array of field => subfield information
     * (used to find series name)
     *
     * @return array
     * @access protected
     */
    protected function getSeriesFromMARC($fieldInfo)
    {
        $matches = array();

        // Loop through the field specification....
        foreach ($fieldInfo as $field => $subfields) {
            // Did we find any matching fields?
            $series = $this->marcRecord->getFields($field);
            if (is_array($series)) {
                foreach ($series as $currentField) {
                    // Can we find a name using the specified subfield list?
                    $name = $this->getSubfieldArray($currentField, $subfields);
                    if (isset($name[0])) {
                        $currentArray = array('name' => $name[0]);

                        // Can we find a number in subfield v?  (Note that number is
                        // always in subfield v regardless of whether we are dealing
                        // with 440, 490, 800 or 830 -- hence the hard-coded array
                        // rather than another parameter in $fieldInfo).
                        $number
                            = $this->getSubfieldArray($currentField, array('v'));
                        if (isset($number[0])) {
                            $currentArray['number'] = $number[0];
                        }

                        // Save the current match:
                        $matches[] = $currentArray;
                    }
                }
            }
        }

        return $matches;
    }

    /**
     * Return an array of non-empty subfield values found in the provided MARC
     * field.  If $concat is true, the array will contain either zero or one
     * entries (empty array if no subfields found, subfield values concatenated
     * together in specified order if found).  If concat is false, the array
     * will contain a separate entry for each subfield value found.
     *
     * @param object $currentField Result from File_MARC::getFields.
     * @param array  $subfields    The MARC subfield codes to read
     * @param bool   $concat       Should we concatenate subfields?
     *
     * @return array
     * @access protected
     */
    protected function getSubfieldArray($currentField, $subfields, $concat = true)
    {
        // Start building a line of text for the current field
        $matches = array();
        $currentLine = '';

        // Loop through all subfields, collecting results that match the whitelist;
        // note that it is important to retain the original MARC order here!
        $allSubfields = $currentField->getSubfields();
        if (count($allSubfields) > 0) {
            foreach ($allSubfields as $currentSubfield) {
                if (in_array($currentSubfield->getCode(), $subfields)) {
                    // Grab the current subfield value and act on it if it is
                    // non-empty:
                    $data = trim($currentSubfield->getData());
                    if (!empty($data)) {
                        // Are we concatenating fields or storing them separately?
                        if ($concat) {
                            $currentLine .= $data . ' ';
                        } else {
                            $matches[] = $data;
                        }
                    }
                }
            }
        }

        // If we're in concat mode and found data, it will be in $currentLine and
        // must be moved into the matches array.  If we're not in concat mode,
        // $currentLine will always be empty and this code will be ignored.
        if (!empty($currentLine)) {
            $matches[] = trim($currentLine);
        }

        // Send back our result array:
        return $matches;
    }

    /**
     * Get an array of summary strings for the record.
     *
     * @return array
     * @access protected
     */
    protected function getSummary()
    {
        return $this->getFieldArray('520');
    }

    /**
     * Get an array of technical details on the item represented by the record.
     *
     * @return array
     * @access protected
     */
    protected function getSystemDetails()
    {
        return $this->getFieldArray('538');
    }

    /**
     * Get an array of note about the record's target audience.
     *
     * @return array
     * @access protected
     */
    protected function getTargetAudienceNotes()
    {
        return $this->getFieldArray('521');
    }

    /**
     * Get the text of the part/section portion of the title.
     *
     * @return string
     * @access protected
     */
    protected function getTitleSection()
    {
        return $this->getFirstFieldValue('245', array('n', 'p'));
    }

    /**
     * Get the statement of responsibility that goes with the title (i.e. "by John
     * Smith").
     *
     * @return string
     * @access protected
     */
    protected function getTitleStatement()
    {
        return $this->getFirstFieldValue('245', array('c'));
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
        $retVal = array();

        $urls = $this->marcRecord->getFields('856');
        if ($urls) {
            foreach ($urls as $url) {
                // Is there an address in the current field?
                $address = $url->getSubfield('u');
                if ($address) {
                    $address = $address->getData();

                    // Is there a description?  If not, just use the URL itself.
                    $desc = $url->getSubfield('y');
                    if (!$desc) {
                        $desc = $url->getSubfield('z');
                    }
                    if ($desc) {
                        $desc = $desc->getData();
                    } else {
                        $desc = $address;
                    }

                    $retVal[$address] = $desc;
                }
            }
        }

        // Check for URLs in the Cumulative Index/Finding Aids note:
        $urls = $this->marcRecord->getFields('555');
        if ($urls) {
            foreach ($urls as $url) {
                // Is there an address in the current field?
                $address = $url->getSubfield('u');
                if ($address) {
                    $address = $address->getData();

                    // Is there a note?  If not, just use the URL itself.
                    $desc = $url->getSubfield('a');
                    if ($desc) {
                        $desc = $desc->getData();
                    } else {
                        $desc = $address;
                    }

                    $retVal[$address] = $desc;
                }
            }
        }

        return $retVal;
    }

    /**
     * Redirect to the RefWorks site and then die -- support method for getExport().
     *
     * @return void
     * @access protected
     */
    protected function redirectToRefWorks()
    {
        global $configArray;

        // Build the URL to pass data to RefWorks:
        $exportUrl = $configArray['Site']['url'] . '/Record/' .
            urlencode($this->getUniqueID()) . '/Export?style=refworks_data';

        // Build up the RefWorks URL:
        $url = $configArray['RefWorks']['url'] . '/express/expressimport.asp';
        $url .= '?vendor=' . urlencode($configArray['RefWorks']['vendor']);
        $url .= '&filter=RefWorks%20Tagged%20Format&url=' . urlencode($exportUrl);

        header("Location: {$url}");
        die();
    }

    /**
     * Get all record links related to the current record. Each link is returned as
     * array.
     * Format:
     * array(
     *        array(
     *               'title' => label_for_title
     *               'value' => link_name
     *               'link'  => link_URI
     *        ),
     *        ...
     * )
     *
     * @return null|array
     * @access protected
     */
    protected function getAllRecordLinks()
    {
        global $configArray;

        $fieldsNames = isset($configArray['Record']['marc_links'])
            ? explode(',', $configArray['Record']['marc_links']) : array();
        $retVal = array();
        foreach ($fieldsNames as $value) {
            $value = trim($value);
            $fields = $this->marcRecord->getFields($value);
            if (!empty($fields)) {
                foreach ($fields as $field) {
                    $indicator = $field->getIndicator('2');
                    switch ($value) {
                    case '780':
                        if ($indicator == '0' || $indicator == '1'
                            || $indicator == '5'
                        ) {
                            $value .= '_' . $indicator;
                        }
                        break;
                    case '785':
                        if ($indicator == '0' || $indicator == '7') {
                            $value .= '_' . $indicator;
                        }
                        break;
                    }
                    $tmp = $this->getFieldData($field, $value);
                    if (is_array($tmp)) {
                        $retVal[] = $tmp;
                    }
                }
            }
        }
        if (empty($retVal)) {
            $retVal = null;
        }
        return $retVal;
    }

    /**
     * Returns the array element for the 'getAllRecordLinks' method
     *
     * @param File_MARC_Data_Field $field Field to examine
     * @param string               $value Field name for use in label
     *
     * @access protected
     * @return array|bool                 Array on success, boolean false if no
     * valid link could be found in the data.
     */
    protected function getFieldData($field, $value)
    {
        global $configArray;

        $labelPrfx   = 'note_';
        $baseURI     = $configArray['Site']['url'];

        // There are two possible ways we may want to link to a record -- either
        // we will have a raw bibliographic record in subfield w, or else we will
        // have an OCLC number prefixed by (OCoLC).  If we have both, we want to
        // favor the bib number over the OCLC number.  If we have an unrecognized
        // parenthetical prefix to the number, we should simply ignore it.
        $bib = $oclc = '';
        $linkFields = $field->getSubfields('w');
        foreach ($linkFields as $current) {
            $text = $current->getData();
            // Extract parenthetical prefixes:
            if (preg_match('/\(([^)]+)\)(.+)/', $text, $matches)) {
                // Is it an OCLC number?
                if ($matches[1] == 'OCoLC') {
                    $oclc = $baseURI . '/Search/Results?lookfor=' .
                        urlencode($matches[2]) . '&type=oclc_num&jumpto=1';
                }
            } else {
                // No parenthetical prefix found -- assume raw bib number:
                $bib = $baseURI . '/Record/' . $text;
            }
        }

        // Check which link type we found in the code above... and fail if we
        // found nothing!
        if (!empty($bib)) {
            $link = $bib;
        } else if (!empty($oclc)) {
            $link = $oclc;
        } else {
            return false;
        }

        return array(
            'title' => $labelPrfx.$value,
            'value' => $field->getSubfield('t')->getData(),
            'link'  => $link
        );
    }

    /**
     * Return an associative array of image URLs associated with this record 
     * (key = URL, value = empty), if available; false otherwise. 
     *
     * @return mixed
     * @access public
     */
    public function getAllImages()
    {
        $urls = array();
        $url = '';
        $type = '';
        foreach ($this->marcRecord->getFields('856') as $url) {
            $type = $url->getSubfield('q');
            if ($type) {
                $type = $type->getData();
                if ("IMAGE" == $type) {
                    $address = $url->getSubfield('u');
                    if ($address) {
                        $address = $address->getData();
                        $urls[$address] = '';
                    }       
                }    
            }
        }
        return $urls;
    }    
    
    /**
     * Return an external URL where a displayable description text
     * can be retrieved from, if available; false otherwise.
     *
     * @return mixed
     * @access public
     */
    public function getDescriptionURL()
    {
        $url = '';
        $type = '';
        foreach ($this->marcRecord->getFields('856') as $url) {
            $type = $url->getSubfield('q');
            if ($type) {
                $type = $type->getData();
                if ("TEXT" == $type) {
                    $address = $url->getSubfield('u');
                    if ($address) {
                        $address = $address->getData();
                        return $address;
                    }       
                }    
            }
        }
        return false;
    }

    /**
     * Return a URL to a thumbnail preview of the record, if available; false
     * otherwise.
     *
     * @param array $size Size of thumbnail (small, medium or large -- small is
     * default).
     *
     * @return mixed
     * @access public
     */
    public function getThumbnail($size = 'small')
    {
        global $configArray;
        foreach ($this->marcRecord->getFields('856') as $url) {
            $type = $url->getSubfield('q');
            if ($type) {
                $type = $type->getData();
                if ("IMAGE" == $type) {
                    $address = $url->getSubfield('u');
                    if ($address) {
                        $address = $address->getData();
                        return $address;
                    }       
                }    
            }
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
        foreach ($this->marcRecord->getFields('856') as $url) {
            $type = $url->getSubfield('q');
            if ($type) {
                $type = $type->getData();
                if ("IMAGE" == $type) {
                    $address = $url->getSubfield('u');
                    if ($address) {
                        $address = $address->getData();
                        return $address;
                    }       
                }    
            }
        }      
        return false;
    }

    /**
     * Overload the IndexRecord method to include other references from MARC field 787. 
     * @return string Name of Smarty template file to display.
     * @access public
     */
    public function getCoreMetadata()
    {
        global $configArray;
        global $interface;
    
        $interface->assign('coreOtherLinks', $this->getOtherLinks());     
        
        // Call the parent method:
        return parent::getCoreMetadata();
    }
        
    /**
     * Get the "other links" from MARC field 787.
     *
     * @return array
     * @access protected
     */
    protected function getOtherLinks()
    {
        $retval = array();
        foreach ($this->marcRecord->getFields('787') as $link) {
            $heading = $link->getSubfield('i');
            if ($heading) {
                $heading = $heading->getData();
            } else {
                $heading = '';
            }
            // Normalize heading
            $heading = str_replace(':', '', $heading);
            $title = $link->getSubfield('t');
            if ($title) {
                $title = $title->getData();
            } else {
                $title = '';
            }
            $author = $link->getSubfield('a');
            if ($author) {
                $author = $author->getData();
            } else {
                $author = '';
            }
            $isbn = $link->getSubfield('z');
            $issn = $link->getSubfield('x');
            if ($isbn) {
                $isn = $isbn->getData();
            } else if ($issn) {
                $isn = $issn->getData();
            } else {
                $isn = '';
            }
            
            $retval[] = compact('heading', 'title', 'author', 'isn');
        }
        return $retval;
    }

    /**
     * Get the main author of the record (without year and role).
     *
     * @return string
     * @access protected
     */
    protected function getPrimaryAuthorForSearch()
    {
        return $this->getFirstFieldValue('100', array('a', 'b', 'c'));
    }

    /**
     * Get the publication end date of the record
     *
     * @return number|false
     * @access protected
     */
    protected function getPublicationEndDate()
    {
        $field = $this->marcRecord->getField('008');
        if ($field) {
            $data = $field->getData();
            $year = substr($data, 11, 4);
            if (is_numeric($year) && $year != 0) {
                return $year;
            } 
        }
        return false;
    }

    /**
     * Strip trailing spaces and punctuation characters from a string
     *
     * @param string|string[] $input String to strip
     * 
     * @return string
     */
    public function stripTrailingPunctuation($input)
    {
        $array = is_array($input);
        if (!$array) {
            $input = array($input);
        }
        foreach ($input as &$str) {
            $str = preg_replace('/[\s\/:;\,=\(]+$/', '', $str);
            // Don't replace an initial letter (e.g. string "Smith, A.") followed by period
            $thirdLast = substr($str, -3, 1);
            if (substr($str, -1) == '.' && $thirdLast != ' '  && $thirdLast != ' ') {
                if (!in_array(substr($str, -4), array('nid.', 'sid.', 'kuv.', 'ill.', 'säv.', 'col.'))) {
                    $str = substr($str, 0, -1);
                }
            }
        }
        return $array ? $input : $input[0];
    }
    
    /**
     * Get an array of alternative titles for the record.
     *
     * @return array
     * @access protected
     */
    protected function getAlternativeTitles()
    {
        return $this->getFieldArray('246', array('a', 'b', 'f'));
    }

    /**
     * Get an array of all ISBNs associated with the record (may be empty).
     *
     * @return array
     * @access protected
     */
    protected function getISBNs()
    {
        return $this->getFieldArray('020', array('a'));
    }

    /**
     * Get an array of all ISSNs associated with the record (may be empty).
     *
     * @return array
     * @access protected
     */
    protected function getISSNs()
    {
        $fields = array(
            '022' => array('a'),
            '440' => array('x'),
            '490' => array('x'),
            '730' => array('x'),
            '773' => array('x'),
            '776' => array('x'),
            '780' => array('x'),
            '785' => array('x')
        ); 
        $issn = array();
        foreach ($fields as $field => $subfields) {
            $issn = array_merge($issn, $this->stripTrailingPunctuation($this->getFieldArray($field, $subfields)));
        }
        return array_values(array_unique($issn));
    }
    
}

?>
