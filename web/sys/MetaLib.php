<?php
/**
 * MetaLib Search API Interface for VuFind
 *
 * PHP version 5
 *
 * Copyright (C) Andrew Nagy 2009.
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
 * @package  Support_Classes
 * @author   Andrew S. Nagy <vufind-tech@lists.sourceforge.net>
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.exlibrisgroup.org/display/MetaLibOI/X-Services
 */

require_once 'sys/Proxy_Request.php';
require_once 'sys/ConfigArray.php';
require_once 'sys/SolrUtils.php';
require_once 'services/MyResearch/lib/Resource.php';

/**
 * MetaLib X-Server API Interface
 *
 * @category VuFind
 * @package  Support_Classes
 * @author   Andrew S. Nagy <vufind-tech@lists.sourceforge.net>
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.exlibrisgroup.org/display/MetaLibOI/X-Services
 */
class MetaLib
{
    /**
     * A boolean value determining whether to print debug information
     * @var bool
     */
    protected $debug = false;

    /**
     * The URL of the MetaLib X-Server
     * @var string
     */
    protected $xServer;

    /**
     * The login user id on the MetaLib X-Server
     *
     * @var string
     */
    protected $xUser;
    
    /**
     * The login user id on the MetaLib X-Server
     *
     * @var string
     */
    protected $xPassword;

    /**
     * MetaLib institution code
     * @var string
     */
    protected $institution;

    /**
     *
     * Configuration settings from web/conf/MetaLib.ini
     * @var array
     */
    protected $config;

    /**
     * Should boolean operators in the search string be treated as
     * case-insensitive (false), or must they be ALL UPPERCASE (true)?
     */
    protected $caseSensitiveBooleans = true;

    /**
     * Will we highlight text in responses?
     * @var bool
     */
    protected $highlight = false;

    /**
     * Will we include snippets in responses?
     * @var bool
     */
    protected $snippets = false;

    /**
     * Constructor
     *
     * Sets up the MetaLib X-Server Client
     *
     * @access public
     */
    public function __construct()
    {
        global $configArray;

        if ($configArray['System']['debug']) {
            $this->debug = true;
        }

        $this->config = getExtraConfigArray('MetaLib');

        // Store preferred boolean behavior:
        if (isset($this->config['General']['case_sensitive_bools'])) {
            $this->caseSensitiveBooleans
                = $this->config['General']['case_sensitive_bools'];
        }

        // Store highlighting/snippet behavior:
        if (isset($this->config['General']['highlighting'])) {
            $this->highlight = $this->config['General']['highlighting'];
        }
        if (isset($this->config['General']['snippets'])) {
            $this->snippets = $this->config['General']['snippets'];
        }
    }

    /**
     * Check if MetaLib is configured and available
     * 
     * @return bool Whether MetaLib is available
     */
    public function available()
    {
        return isset($this->config['General']);
    }
    
    /**
     * Retrieves a document specified by the ID.
     *
     * @param string $id The document to retrieve from the MetaLib API/cache
     *
     * @throws object    PEAR Error
     * @return string    The requested resource
     * @access public
     */
    public function getRecord($id)
    {
        if ($this->debug) {
            echo "<pre>Get Record: $id</pre>\n";
        }

        list($queryId, $index) = explode('_', $id);
        $result = $this->getCachedResults($queryId);
        if ($result === false) {
            // Check from database, this could be in a favorite list
            $resource = new Resource();
            $resource->record_id = $id;
            $resource->source = 'MetaLib';
            if ($resource->find(true)) {
                return unserialize($resource->data);
            }
            return new PEAR_Error('Record not found');
        }
        if ($index < 1 || $index > count($result['documents'])) {
            return new PEAR_Error('Invalid record id');
        }
        $result['documents'] = array_slice($result['documents'], $index - 1, 1);
        return $result['documents'][0];
    }

    /**
     * Escape a string for inclusion as part of a MetaLib parameter.
     *
     * @param string $input The string to escape.
     *
     * @return string       The escaped string.
     * @access protected
     */
    protected function escapeParam($input)
    {
        // TODO: needed?
        return $input;
    }

    /**
     * Build Query string from search parameters
     *
     * @param array $search An array of search parameters
     *
     * @return string       The query
     * @access protected
     */
    protected function buildQuery($search)
    {
        $groups   = array();
        $excludes = array();
        if (is_array($search)) {
            $query = '';

            foreach ($search as $params) {
                // Advanced Search
                if (isset($params['group'])) {
                    $thisGroup = array();
                    // Process each search group
                    foreach ($params['group'] as $group) {
                        // Build this group individually as a basic search
                        $thisGroup[] = $this->buildQuery(array($group));
                    }
                    // Is this an exclusion (NOT) group or a normal group?
                    if ($params['group'][0]['bool'] == 'NOT') {
                        $excludes[] = join(" OR ", $thisGroup);
                    } else {
                        $groups[] = join(
                            " " . $params['group'][0]['bool'] . " ", $thisGroup
                        );
                    }
                }

                // Basic Search
                if (isset($params['lookfor']) && $params['lookfor'] != '') {
                    // Clean and validate input -- note that index may be in a
                    // different field depending on whether this is a basic or
                    // advanced search.
                    $lookfor = $params['lookfor'];
                    if (isset($params['field'])) {
                        $index = $params['field'];
                    } else if (isset($params['index'])) {
                        $index = $params['index'];
                    } else {
                        $index = 'AllFields';
                    }

                    // Force boolean operators to uppercase if we are in a
                    // case-insensitive mode:
                    if (!$this->caseSensitiveBooleans) {
                        $lookfor = VuFindSolrUtils::capitalizeBooleans($lookfor);
                    }

                    // Prepend the index name, unless it's the special "AllFields"
                    // index:
                    if ($index != 'AllFields') {
                        $query .= "{$index}=($lookfor)";
                    } else {
                        $query .= "WRD=($lookfor)";
                    }
                }
            }
        }

        // Put our advanced search together
        if (count($groups) > 0) {
            $query = "(" . join(") " . $search[0]['join'] . " (", $groups) . ")";
        }
        // and concatenate exclusion after that
        if (count($excludes) > 0) {
            $query .= " NOT ((" . join(") OR (", $excludes) . "))";
        }

        // Ensure we have a valid query to this point
        return isset($query) ? $query : '';
    }

    /**
     * Execute a search.
     *
     * @param string $irdList    Comma-separated list of IRD IDs
     * @param array  $query      The search terms from the Search Object
     * @param array  $filterList The fields and values to filter results on
     *                           (currently unused)
     * @param string $start      The record to start with
     * @param string $limit      The number of records to return
     * @param string $sortBy     The value to be used by for sorting
     * @param array  $facets     The facets to include (null for defaults) (unused)
     * @param bool   $returnErr  On fatal error, should we fail outright (false) or
     * treat it as an empty result set with an error key set (true)?
     *
     * @throws object            PEAR Error
     * @return array             An array of query results
     * @access public
     */
    public function query($irdList, $query, $filterList = null, $start = 1, 
        $limit = 20, $sortBy = null, $facets = null, $returnErr = false
    ) {
        $queryStr = $this->buildQuery($query);
        if (!$queryStr) {
            PEAR::raiseError(new PEAR_Error('Search terms are required'));
        }
        
        $failed = array();
        $disallowed = array();
        $irdArray = array();
        $authorized = UserAccount::isAuthorized();
        foreach (explode(',', $irdList) as $ird) {
            $irdInfo = $this->getIRDInfo($ird);
            if (strcasecmp($irdInfo['access'], 'guest') != 0 && !$authorized) {
                $disallowed[] = $irdInfo['name'];
            } else {
                $irdArray[] = $ird;
            }
        }
        
        if (empty($irdArray)) {
            return array(
                'recordCount' => 0,
                'failedDatabases' => $failed,
                'disallowedDatabases' => $disallowed,
            );
        }
        
        // Put together final list of IRDs to search
        $irdList = implode(',', $irdArray);
        
        // Use a metalib. prefix everywhere so that it's easy to see the record source
        $queryId = 'metalib.' . md5($irdList . '_' . $queryStr . '_' . $start . '_' . $limit);
        $findResults = $this->getCachedResults($queryId);
        if ($findResults !== false && empty($findResults['failedDatabases']) && empty($findResults['disallowedDatabases'])) {
            return $findResults;
        }
                
        $options = array();
        $options['find_base/find_base_001'] = $irdArray;
        $options['find_request_command'] = $queryStr;
        
        // TODO: add configurable authentication mechanisms to identify authorized
        // users and switch this to use it
        $options['requester_ip'] = $_SERVER['REMOTE_ADDR'];
        
        // TODO: local highlighting?
        
        if ($this->debug) {
            echo '<pre>Query: ';
            print_r($options);
            echo "</pre>\n";
        }
        
        $sessionId = $this->getSession();

        // Do the find request
        $findRequestId = md5($irdList . '_' . $queryStr);
        if (isset($_SESSION['MetaLibFindResponse']) 
            && $_SESSION['MetaLibFindResponse']['requestId'] == $findRequestId
            && $disallowed == $_SESSION['MetaLibFindResponse']['disallowed']
        ) {
            $databases = $_SESSION['MetaLibFindResponse']['databases'];
            $totalRecords = $_SESSION['MetaLibFindResponse']['totalRecords'];
            $failed = $_SESSION['MetaLibFindResponse']['failed'];
        } else {
            $options['session_id'] = $sessionId;
            $options['wait_flag'] = 'Y';
            $findResults = $this->callXServer('find_request', $options);
            if (PEAR::isError($findResults)) {
                PEAR::raiseError($findResults);
            }
                
            // Gather basic information
            $databases = array();
            $totalRecords = 0;
            foreach ($findResults->find_response->base_info as $baseInfo) {
                if ($baseInfo->find_status != 'DONE') {
                    error_log(
                        'MetaLib search in ' . $baseInfo->base_001 . ' (' . $baseInfo->full_name . ') failed: '
                        . $baseInfo->find_error_text
                    );
                    $failed[] = (string)$baseInfo->full_name;
                    continue;
                }
                $count = ltrim((string)$baseInfo->no_of_documents, ' 0');
                if ($count === '') {
                    continue;
                }
                $totalRecords += $count;
                $databases[] = array(
                    'ird' => (string)$baseInfo->base_001,
                    'count' => $count,
                    'set' => (string)$baseInfo->set_number,
                    'records' => array()
                );
            }
            $_SESSION['MetaLibFindResponse']['requestId'] = $findRequestId;
            $_SESSION['MetaLibFindResponse']['databases'] = $databases;
            $_SESSION['MetaLibFindResponse']['totalRecords'] = $totalRecords;
            $_SESSION['MetaLibFindResponse']['failed'] = $failed;
            $_SESSION['MetaLibFindResponse']['disallowed'] = $disallowed;
        }

        $documents = array();
        $databaseCount = count($databases);
        if ($databaseCount > 0) {
            // Sort the array by number of results
            usort(
                $databases, 
                function($a, $b) {
                    return $a['count'] - $b['count'];    
                }
            );
            
            // Find cut points where a database is exhausted of results
            $sum = 0;
            for ($k = 0; $k < $databaseCount; $k++) {
                $sum += ($databases[$k]['count'] - ($k > 0 ? $databases[$k - 1]['count'] : 0)) * ($databaseCount - $k);
                $databases[$k]['cut'] = $sum;
            }
            
            // Find first item for the given page
            $firstRecord = ($start - 1) * $limit;
            $i = 0;
            $iCount = false;
            for ($k = 0; $k < $databaseCount; $k++) {
                if ($iCount === false || $databases[$k]['count'] < $iCount) {
                    if ($databases[$k]['cut'] >= $firstRecord) {
                        $i = $k;
                        $iCount = $databases[$k]['count'];
                    }
                }
            }
            $l = $databases[$i]['cut'] - $firstRecord - 1;
            if ($l < 0) {
                PEAR::raiseError(new PEAR_Error('Invalid page index'));
            }
            $m = $l % ($databaseCount - $i);
            $startDB = $databaseCount - $m - 1;
            $startRecord = floor($databases[$i]['count'] - ($l + 1) / ($databaseCount - $i) + 1) - 1;
            
            // Loop until we have enough record indices or run out of records from any of the databases
            $currentDB = $startDB;
            $currentRecord = $startRecord;
            $haveRecords = true;
            for ($count = 0; $count < $limit;) {
                if ($databases[$currentDB]['count'] > $currentRecord) {
                    $databases[$currentDB]['records'][] = $currentRecord + 1;
                    ++$count;
                    $haveRecords = true;
                }
                if (++$currentDB >= $databaseCount) {
                    if (!$haveRecords) {
                        break;
                    }
                    $haveRecords = false;
                    $currentDB = 0;
                    ++$currentRecord;
                }
            }
            
            // Fetch records
            $baseIndex = 0;
            for ($i = 0; $i < $databaseCount; $i++) {
                $database = $databases[($startDB + $i) % $databaseCount];
                ++$baseIndex;
                
                if (empty($database['records'])) {
                    continue;
                }
                
                $params = array(
                    'session_id' => $sessionId,
                    'present_command' => array(
                        'set_number' => $database['set'],
                        'set_entry' => $database['records'][0] . '-' . end($database['records']),
                        'view' => 'full',
                        'format' => 'marc'
                    )
                );
                
                $result = $this->callXServer('present_request', $params);
                if (PEAR::isError($result)) {
                    PEAR::raiseError($result);
                }
    
                // Go through the records one by one. If there is a MOR tag 
                // in the record, it means that a single record present 
                // command is needed to fetch full record. 
                $currentDocs = array();
                $recIndex = -1;
                foreach ($result->present_response->record as $record) {
                    ++$recIndex;
                    $record->registerXPathNamespace('m', 'http://www.loc.gov/MARC21/slim');
                    if ($record->xpath("./m:controlfield[@tag='MOR']")) {
                        $params = array(
                            'session_id' => $sessionId,
                            'present_command' => array(
                                'set_number' => $database['set'],
                                'set_entry' => $database['records'][$recIndex],
                                'view' => 'full',
                                'format' => 'marc'
                            )
                        );
                    
                        $singleResult = $this->callXServer('present_request', $params);
                        if (PEAR::isError($singleResult)) {
                            PEAR::raiseError($singleResult);
                        }
                        $currentDocs[] = $this->process($singleResult->present_response->record[0]);
                    } else {
                        $currentDocs[] = $this->process($record);
                    }
                }                
                
                $docIndex = 0;
                foreach ($currentDocs as $doc) {
                    $foundRecords = true;
                    $documents[sprintf('%09d_%09d', $docIndex++, $baseIndex)] = $doc;
                }
            }
            
            ksort($documents);
            $documents = array_values($documents);
    
            $i = 1;
            foreach ($documents as $key => $doc) {
                $documents[$key]['ID'] = array($queryId . '_' . $i);
                $i++;
            }
        }
        
        $results = array(
            'recordCount' => $totalRecords,
            'documents' => $documents,
            'failedDatabases' => $failed,
            'disallowedDatabases' => $disallowed
        );
        $this->putCachedResults($queryId, $results);
        return $results;
    }

    /**
     * Get information regarding the IRD
     *
     * @param string $ird IRD ID
     *
     * @return array Array with e.g. 'name' and 'access'
     * @access public
     */
    public function getIRDInfo($ird)
    {
        $queryId = "metalib_ird.$ird";
        $cached = $this->getCachedResults($queryId);
        if ($cached) {
            return $cached;
        }
        $sessionId = $this->getSession();
        
        // Do the source locate request
        $params = array(
            'session_id' => $sessionId,
            'locate_command' => "IDN=$ird",
            'source_full_info_flag' => 'Y'
        );
        $result = $this->callXServer('source_locate_request', $params);
        if (PEAR::isError($result)) {
            PEAR::raiseError($result);
        }

        $info = array();
        $info['name'] = (string)$result->source_locate_response->source_full_info->source_info->source_short_name;
        $record = $result->source_locate_response->source_full_info->record;
        $record->registerXPathNamespace('m', 'http://www.loc.gov/MARC21/slim');
        $info['access'] = $this->getSingleValue($record, 'AF3a');
        $this->putCachedResults($queryId, $info);
        return $info;
    }
    
    /**
     * Return current session id (if valid) or create a new session
     * 
     * @return string session id
     * @access protected
     */
    protected function getSession()
    {
        $sessionId = '';
        if (isset($_SESSION['MetaLibSessionID'])) {
            // Check for valid session
            $params = array(
                'session_id' => $_SESSION['MetaLibSessionID'],
                'view' => 'customize',
                'logical_set' => 'ml_sys_info',
                'parameter_name' => 'ML_VERSION'
            );
            $result = $this->callXServer('retrieve_metalib_info_request', $params);
            if (!PEAR::isError($result)) {
                $sessionId = $_SESSION['MetaLibSessionID'];
            }
        }
        
        if (!$sessionId) {
            // Login to establish a session
            $params = array(
                'user_name' => $this->config['General']['x_user'],
                'user_password' => $this->config['General']['x_password']
            );
            $result = $this->callXServer('login_request', $params);
            if (PEAR::isError($result)) {
                PEAR::raiseError($result);
            }
            if ($result->login_response->auth != 'Y') {
                $logger = new Logger();
                $logger->log("X-Server login failed: \n" . $xml, PEAR_LOG_ERR);
                PEAR::raiseError(new PEAR_Error('X-Server login failed'));
            }
            $sessionId = (string)$result->login_response->session_id;
            $_SESSION['MetaLibSessionID'] = $sessionId;
            unset($_SESSION['MetaLibFindResponse']);
        }
        return $sessionId;        
    }
    
    /**
     * Convert array of X-Server call parameters to XML
     * 
     * @param simpleXMLElement $node  The target node
     * @param array            $array Array to convert
     * 
     * @return void
     * @access protected
     */
    protected function paramsToXml($node, $array)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $path = explode('/', $key, 2);
                if (isset($path[1])) {
                    foreach ($value as $single) {
                        $child = $node->addChild($path[0]);
                        $child->addChild($path[1], $single);
                    }
                } else {
                    $child = $node->addChild($path[0]);
                    foreach ($value as $vkey => $single) {
                        $child->addChild($vkey, $single);
                    }
                }
            } else {
                $node->addChild($key, $value);
            }
        }
    }
    
    /**
     * Call MetaLib X-Server
     *
     * @param string $operation X-Server operation
     * @param array  $params    URL Parameters
     * 
     * @return mixed simpleXMLElement | PEAR_Error
     * @access protected
     */
    protected function callXServer($operation, $params)
    {
        $request = new Proxy_Request($this->config['General']['url'], array('method' => 'POST'));
        $xml = simplexml_load_string('<x_server_request/>');
        $op = $xml->addChild($operation);
        $this->paramsToXml($op, $params);
        
        $request->addPostdata('xml', $xml->asXML());
        
        if ($this->debug) {
            echo "<!-- $operation\n";
            if ($operation != 'login_request') {
                echo $xml->asXML();
            }
            echo "-->\n";
        }
        
        $result = $request->sendRequest();
        if (PEAR::isError($result)) {
            return $result;
        }
        if ($this->debug) {
            echo "<!-- \n";
            echo $request->getResponseBody();
            echo "-->\n\n\n";
        }
        if ($request->getResponseCode() >= 400) {
            return new PEAR_Error("HTTP Request failed: " . $request->getResponseCode());
        }
        $xml = simplexml_load_string($request->getResponseBody());
        $errors = $xml->xpath('//local_error | //global_error');
        if (!empty($errors)) {
            if ($errors[0]->error_code == 6026) {
                return new PEAR_Error('Search timed out');
            }
            return new PEAR_Error($errors[0]->asXML());
        }
        return $xml;
    }
    
    /**
     * Perform normalization and analysis of MetaLib return value
     * (a single record)
     *
     * @param simplexml $record The xml record from MetaLib
     *
     * @return array The processed record array
     * @access protected
     */
    protected function process($record)
    {
        global $configArray;
        
        $record->registerXPathNamespace('m', 'http://www.loc.gov/MARC21/slim');

        // TODO: can we get anything reliable from MetaLib results for format?
        $format = ''; 
        $title = $this->getSingleValue($record, '245ab', ' : ');
        if ($addTitle = $this->getSingleValue($record, '245h')) {
            $title .= " $addTitle";
        }
        $author = $this->getSingleValue($record, '100a');
        $addAuthors = $this->getSingleValue($record, '700a');
        $sources = $this->getMultipleValues($record, 'SIDt');
        $year = $this->getSingleValue($record, 'YR a');
        $languages = $this->getMultipleValues($record, '041a');

        $urls = array();
        $res = $record->xpath("./m:datafield[@tag='856']");
        foreach ($res as $value) {
            $value->registerXPathNamespace('m', 'http://www.loc.gov/MARC21/slim');
            $url = $value->xpath("./m:subfield[@code='u']");
            if ($url) {
                $desc = $value->xpath("./m:subfield[@code='y']");
                if ($desc) {
                    $urls[(string)$url[0]] = (string)$desc[0];
                } else {
                    $urls[(string)$url[0]] = (string)$url[0];
                }
            }
        }
        
        $openurl = array();
        if (isset($configArray['OpenURL']['url']) && $configArray['OpenURL']['url']) {
            $opu = $this->getSingleValue($record, 'OPUa');
            if ($opu) {
                $opuxml = simplexml_load_string($opu);
                $opuxml->registerXPathNamespace('ctx', 'info:ofi/fmt:xml:xsd:ctx');
                $opuxml->registerXPathNamespace('rft', ''); //info:ofi/fmt:xml:xsd');
                foreach ($opuxml->xpath('//*') as $element) {
                    if (in_array($element->getName(), array('journal', 'author'))) {
                        continue;
                    }
                    $value = trim((string)$element);
                    if ($value) {
                        $openurl[$element->getName()] = $value;
                        
                        // OpenURL might have many nicely parsed elements we can use
                        switch ($element->getName()) {
                        case 'date': 
                            if (empty($year)) {
                                $year = $value;
                            }
                            break;
                        case 'volume': 
                            $volume = $value; 
                            break;
                        case 'issue': 
                            $issue = $value; 
                            break;
                        case 'spage':
                            $startPage = $value;
                            break;
                        case 'epage':
                            $endPage = $value;
                            break;
                        }
                    }
                }
                if (!empty($openurl)) {
                    $openurl['rfr_id'] = $configArray['OpenURL']['rfr_id'];
                }
            }
        }
        
        $isbn = $this->getMultipleValues($record, '020a');
        $issn = $this->getMultipleValues($record, '022a');
        $snippet = $this->getMultipleValues($record, '520a');
        $subjects = $this->getMultipleValues(
            $record, 
            '600abcdefghjklmnopqrstuvxyz'
            . ':610abcdefghklmnoprstuvxyz'
            . ':611acdefghjklnpqstuvxyz'
            . ':630adefghklmnoprstvxyz'
            . ':650abcdevxyz', 
            ' : '
        );
        $notes = $this->getMultipleValues($record, '500a');
        $field773g = $this->getSingleValue($record, '773g');
        
        $matches = array();
        if (preg_match('/(\d*)\s*\((\d{4})\)\s*:\s*(\d*)/', $field773g, $matches)) {
            if (!isset($volume)) {
                $volume = $matches[1];
            }
            if (!isset($issue)) {
                $issue = $matches[3];
            }
        } elseif (preg_match('/(\d{4})\s*:\s*(\d*)/', $field773g, $matches)) {
            if (!isset($volume)) {
                $volume = $matches[1];
            }
            if (!isset($issue)) {
                $issue = $matches[2];
            }
        }
        if (preg_match('/,\s*\w\.?\s*([\d,\-]+)/', $field773g, $matches)) {
            $pages = explode('-', $matches[1]);
            if (!isset($startPage)) {
                $startPage = $pages[0];
            }
            if (isset($pages[1]) && !isset($endPage)) {
                $endPage = $pages[1];
            }
        }
        $hostTitle = $this->getSingleValue($record, '773t');
        if ($hostTitle && $field773g) {
            $hostTitle .= " $field773g";
        }

        $year = str_replace('^^^^', '', $year);
        return array(
            'Title' => array($title), 
            'Author' => $author ? array($author) : null,
            'AdditionalAuthors' => $addAuthors, 
            'Source' => $sources,
            'PublicationDate' => $year ? array($year) : null,
            'PublicationTitle' => $hostTitle ? array($hostTitle) : null,
            'openUrl' => !empty($openurl) ? http_build_query($openurl) : null,
            'url' => $urls,
            'fullrecord' => $record->asXML(),
            'id' => '',
            'recordtype' => 'marc',
            'format' => array($format),
            'ISBN' => $isbn,
            'ISSN' => $issn,
            'Language' => $languages,
            'SubjectTerms' => $subjects,
            'Snippet' => $this->snippets ? $snippet : null,
            'Notes' => $notes,
            'Volume' => isset($volume) ? $volume : '',
            'Issue' => isset($issue) ? $issue : '',
            'StartPage' => isset($startPage) ? $startPage : '',
            'EndPage' => isset($endPage) ? $endPage : ''
        );
    }
    
    /**
     * Return the contents of a single MARC data field
     * 
     * @param simpleXMLElement $xml       MARC Record
     * @param string           $fieldspec Field and subfields (e.g. '245ab')
     * @param string           $glue      Delimiter used between subfields
     * 
     * @return string
     * @access protected
     */
    protected function getSingleValue($xml, $fieldspec, $glue = '')
    {
        $values = $this->getMultipleValues($xml, $fieldspec, $glue);
        if ($values) {
            return $values[0];
        }
        return '';
    }

    /**
     * Return the contents of MARC data fields as an array
     * 
     * @param simpleXMLElement $xml        MARC Record
     * @param string           $fieldspecs Fields and subfields (e.g. '100a:700a')
     * @param string           $glue       Delimiter used between subfields
     * 
     * @return array
     * @access protected
     */
    protected function getMultipleValues($xml, $fieldspecs, $glue = '')
    {
        $values = array();
        foreach (explode(':', $fieldspecs) as $fieldspec) {
            $field = substr($fieldspec, 0, 3);
            $subfields = substr($fieldspec, 3);
            $xpath = "./m:datafield[@tag='$field']";
            
            $res = $xml->xpath($xpath);
            foreach ($res as $datafield) {
                $strings = array();
                foreach ($datafield->subfield as $subfield) {
                    if (strstr($subfields, (string)$subfield['code'])) {
                        $strings[] .= (string)$subfield;
                    }
                }
                if ($strings) {
                    $values[] = implode($glue, $strings);
                }
            }
        }    
        return $values;
    }
    
    /**
     * Return search results from cache
     * 
     * @param string $queryId Query identifier (hash)
     * 
     * @return mixed Search results array | false
     * @access protected
     */
    protected function getCachedResults($queryId)
    {
        global $configArray;
        
        $cacheFile = $configArray['Site']['local']
            . "/interface/cache/$queryId.dat";
        if (file_exists($cacheFile)) {
            // Default caching time is 60 minutes (note that cache is required
            // for full record display)
            $cacheTime = isset($this->config['General']['cache_timeout'])
                ? $this->config['General']['cache_timeout'] : 60;
            if (time() - filemtime($cacheFile) < $cacheTime * 60) { 
                return unserialize(file_get_contents($cacheFile));
            }
        }
        return false;
    }
    
    /**
     * Add search results into the cache
     * 
     * @param string $queryId Query identifier (hash)
     * @param array  $results Search results
     * 
     * @return void
     * @access protected
     */
    protected function putCachedResults($queryId, $results)
    {
        global $configArray;
        $cacheFile = $configArray['Site']['local'] 
            . "/interface/cache/$queryId.dat";
        file_put_contents($cacheFile, serialize($results));
    }
}
