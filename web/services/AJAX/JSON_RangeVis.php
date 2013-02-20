<?php
/**
 * Date range AJAX functions for the Recommender Visualisation module using JSON as
 * output format.
 *
 * PHP version 5
 *
 * Copyright (C) Eero Heikkinen 2013.
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
 * @author   Eero Heikkinen <eero.heikkinen@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */

require_once 'JSON.php';
require_once 'RecordDrivers/Factory.php';

/**
 * Common AJAX functions for the Recommender Visualisation module using JSON as
 * output format.
 *
 * @category VuFind
 * @package  Controller_AJAX
 * @author   Eero Heikkinen <eero.heikkinen@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */
class JSON_RangeVis extends JSON
{
    private $_searchObject;
    private $_dateFacets = array();

    /**
     * Constructor.
     *
     * @access public
     */
    public function __construct()
    {
        global $action;
        parent::__construct();
        $this->_searchObject = SearchObjectFactory::initSearchObject();
    }
    
    /**
     * Used for unit testing
     * 
     * @return SearchObject_Base
     */
    public function getSearchObject() 
    {
        return $this->_searchObject;
    }
    
    /**
     * Function which outputs requested date range information. 
     * Called through AJAX service interface with method=printVisData.
     * 
     * @return void
     */
    public function printVisData() 
    {
        $visData = $this->getVisData();
        if ($visData == null) {
            $this->output("", JSON::STATUS_ERROR);
        } else { 
            $this->output($visData, JSON::STATUS_OK);
        }
    }

    /**
     * Get range field facets using specified shaping method
     *
     * @param array $fields Fields to process
     *
     * @return void
     * @access public
     */
    public function getVisData($fields = array('unit_daterange'))
    {
        global $interface;

        if (is_a($this->_searchObject, 'SearchObject_Solr')) {
            if (!empty($_REQUEST["ignoreFilter"])) {
                foreach ($fields as $field) {
                    $this->_searchObject->excludeFilterForFacet($field, "exclude");
                }
            }
            
            $n = $_REQUEST['n'];
            if (empty($n)) {
                return;
            }
            
            if ($_REQUEST['shape'] == 'spread') {
                $spread = $_REQUEST['spread'];
                if (empty($spread)) {
                    return;
                }
                
                $start = reset($spread);
                $end = end($spread);
                
                $points = $this->_spread($n, $spread);
            } else {
                $start = $_REQUEST['start'];
                $end = $_REQUEST['end'];
                if (empty($start) || empty($end)) {
                    return;
                }
                
                $points = array();
                switch($_REQUEST['shape']) {
                case 'linear': 
                    $points = $this->_linear($n);
                    break;
                case 'bezier':
                    $x0 = $_REQUEST['x0'];
                    $y0 = $_REQUEST['y0'];
                    $x1 = $_REQUEST['x1'];
                    $y1 = $_REQUEST['y1'];
                    if (empty($x0) || empty($y0) || empty($x1) || empty($y1)) {
                        return;
                    }
                    $points = $this->_bezier($n, $x0, $y0, $x1, $y1);
                    break;
                default:
                    return;
                }
            }
            
            $queries = $this->rangeToQueries($start, $end, $points);
            
            $this->_dateFacets = $fields;
            $this->_searchObject->init();
            
            $filters = $this->_searchObject->getFilters();
            
            $fields = $this->_processDateFacets($filters);
            $facets = $this->_getFacets($fields, $queries);
            
            return $facets;
        } else {
            return null;
        }
    }

    /**
     * Get facet from index using specified queries.
     *
     * @param string $fields  Index field name
     * @param array  $queries Queries to use
     *
     * @return array
     * @access private
     */
    private function _getFacets($fields, $queries)
    {
        $retVal = array();
        foreach ($fields as $field => $fieldContents) {
            $this->_searchObject->addPseudoFacet($field, "description", $queries);
            
            // This causes multiple index calls instead of just one 
            // TODO: refactor out of here 
            $result = $this->_searchObject->processSearch(true, true);
            $facets = $this->_searchObject->getFacetList(array($field => 1));
            
            $facet = $facets[$field];
            
            foreach ($facet["list"] as $range) {
                $value = $range["untranslated"];
                $result = $this->_extractFromAndTo($value);
                $result["count"] = $range["count"];
                
                $retVal[$field]["data"][] = $result;
            }
                
            $n = count($retVal[$field]["data"]);
            if ($n > 0) {
                // Calculate bounds for result set
                $retVal[$field]["min"] = $retVal[$field]["data"][0]["from"];
                $retVal[$field]["max"] = $retVal[$field]["data"][$n-1]["to"];
            }
        }
        
        return $retVal;
    }
    
    /**
     * Splits a range of years to a list of queries, using the specified
     * split points 
     *
     * @param integer      $start  Start year
     * @param integer      $end    End year
     * @param array[float] $points An array which holds the percentage 
     *                             points (0.0-1.0) where to split the range at.
     *
     * @return array
     * @access private
     */
    public function rangeToQueries($start, $end, $points)
    {
        $queries = array();
        
        $span = $end - $start;
        
        $lastDate = '';
        for ($i = 0; $i<count($points); $i++) {
            $point = $points[$i];
            
            // Special case for first point
            if ($i == 0) {
                $lastDate = round($start + ($point * $span)) . "-01-01T00:00:00Z";
                continue;
            }
            
            $date = round($start + ($point * $span)) . "-01-01T00:00:00Z"; 
            $queries[] = "[" . $lastDate . " TO " . $date . "]";
            $lastDate = $date;
        }
        
        $queries[] = "[" . $lastDate . " TO " . $end . "-01-01T00:00:00Z]";
        
        return $queries;
    }
    /**
     * Builds an array of percentage points (0.0 ... 1.0) split linearly.
     * 
     * @param number $n Number of points to create
     * 
     * @return array
     */
    private function _linear($n) 
    {
        $points = array();
        
        for ($i=0; $i<$n; $i++) {
            $points[] = $i / $n;
        }
        
        return $points;
    }
    
    /**
     * Builds an array of percentage points split by bezier curve function.
     * 
     * @param number $n  Number of points to create
     * @param float  $x0 First control point, X
     * @param float  $y0 First control point, Y
     * @param float  $x1 Second control point, X
     * @param float  $y1 Second control point, Y
     * 
     * @return array[float]
     */
    private function _bezier($n, $x0, $y0, $x1, $y1) 
    {
        $points = array();
        
        for ($i = 0; $i < $n; $i++) {
            $points[] = BezierMath::cubicBezier($i / $n, $y0, $x0, $y1, $x1);
        }
        
        return $points;
    }
    
    /**
     * Return array of split points to fit a given spread of values
     * 
     * @param integer       $n      Number of points to generate
     * @param array(number) $spread Array of values to fit points into
     * 
     * @return multitype:number
     */
    private function _spread($n, $spread) 
    {
        $points = array();
        
        // First value in spread
        $first = reset($spread);
        // Span (length) of spread
        $span = end($spread) - $first;
        $nRanges = count($spread) - 1;
        for ($i = 0; $i < $n; $i++) {
            $position = $i / ($n+1) * $nRanges;
            $currentSpread = floor($position);
            $fraction = $position - $currentSpread;
            
            $lower = $spread[$currentSpread];
            $upper = $spread[$currentSpread + 1];
            
            $valueAtN = $lower + $fraction * ($upper - $lower);
            
            $points[] = ($valueAtN - $first) / $span;
        }
        
        return $points;
    }
    
    /** 
     * Extracts numeral 'from' and 'to' from a Solr range query string
     * 
     * @param string $input String to extract from
     * 
     * @return array
     */
    private function _extractFromAndTo($input) 
    {
        preg_match("/\[(.+) TO (.+)\]/i", $input, $results);
        return array('from' => $results[1], 'to' => $results[2]);
    }

    /**
     * Support method for getVisData() -- extract details from applied filters.
     *
     * @param array $filters Current filter list
     *
     * @return array
     * @access private
     */
    private function _processDateFacets($filters)
    {
        $result = array();
        foreach ($this->_dateFacets as $current) {
            $from = $to = '';
            if (isset($filters[$current])) {
                foreach ($filters[$current] as $filter) {
                    if ($range = VuFindSolrUtils::parseRange($filter)) {
                        $from = $range['from'] == '*' ? '' : $range['from'];
                        $to = $range['to'] == '*' ? '' : $range['to'];
                        break;
                    }
                }
            }
            $result[$current] = array($from, $to);
        }
        return $result;
    }
}

/**
 * Class for Bezier Math operations
 * 
 * @category Math
 * @package  Controller_AJAX
 * @author   Eero Heikkinen <eero.heikkinen@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 *
 */
class BezierMath
{
    /**
     * Solves y from x using Newton-Raphson approximation
     * Based on Cubic Spline Math by Don Lancaster
     * 
     * @param float $x X 
     * @param float $a First influence X
     * @param float $b First influence Y
     * @param float $c Second influence X 
     * @param float $d Second influence Y
     * 
     * @return number Y
     * 
     * @link http://cubic-bezier.com
     * @author Eero Heikkinen <eero.heikkinen@gmail.com>
     * @author Don Lancaster <synergetics@tinaja.com>
     */
    public static function cubicBezier ($x, $a, $b, $c, $d)
    {
        $y0a = 0.00; // initial y
        $x0a = 0.00; // initial x
        $y1a = $b;    // 1st influence y
        $x1a = $a;    // 1st influence x
        $y2a = $d;    // 2nd influence y
        $x2a = $c;    // 2nd influence x
        $y3a = 1.00; // final y
        $x3a = 1.00; // final x
    
        $A =   $x3a - 3*$x2a + 3*$x1a - $x0a;
        $B = 3*$x2a - 6*$x1a + 3*$x0a;
        $C = 3*$x1a - 3*$x0a;
        $D =   $x0a;
    
        $E =   $y3a - 3*$y2a + 3*$y1a - $y0a;
        $F = 3*$y2a - 6*$y1a + 3*$y0a;
        $G = 3*$y1a - 3*$y0a;
        $H =   $y0a;
    
        $currentt = $x;
        $nRefinementIterations = 5;
        for ($i=0; $i < $nRefinementIterations; $i++) {
            $currentx = self::_xFromT($currentt, $A, $B, $C, $D);
            $currentslope = self::_slopeFromT($currentt, $A, $B, $C);
            $currentt -= ($currentx - $x)*($currentslope);
            if ($currentt < 0.0) {
                $currentt = 0.0;
            }
            if ($currentt > 1.0) {
                $currentt = 1.0;
            }
        }
    
        $y = self::_yFromT($currentt, $E, $F, $G, $H);
        return $y;
    }
    
    /**
     * Helper function to calculate slope from T
     * 
     * @param float $t t
     * @param float $A a
     * @param float $B b
     * @param float $C c
     * 
     * @return float Slope
     */
    private static function _slopeFromT ($t, $A, $B, $C)
    {
        $dtdx = 1.0/(3.0*$A*$t*$t + 2.0*$B*$t + $C);
        return $dtdx;
    }
    
    /**
     * Helper function to calculate x from T
     * 
     * @param float $t t
     * @param float $A a
     * @param float $B b
     * @param float $C c
     * @param float $D d
     * 
     * @return float X
     */
    private static function _xFromT ($t, $A, $B, $C, $D)
    {
        $x = $A*($t*$t*$t) + $B*($t*$t) + $C*$t + $D;
        return $x;
    }
    
    /**
     * Helper function to calculate y from T
     * 
     * @param float $t t
     * @param float $E e
     * @param float $F f
     * @param float $G g
     * @param float $H h
     * 
     * @return float Y
     */
    private static function _yFromT ($t, $E, $F, $G, $H)
    {
        $y = $E*($t*$t*$t) + $F*($t*$t) + $G*$t + $H;
        return $y;
    }
}
?>
