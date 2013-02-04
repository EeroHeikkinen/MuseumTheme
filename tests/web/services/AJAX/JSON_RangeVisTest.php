<?php
/**
 * SearchObject Factory Test Class
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
 * @package  Tests
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/unit_tests Wiki
 */
require_once dirname(__FILE__) . '/../../prepend.inc.php';
require_once dirname(__FILE__) . '/../../AbstractMockIndexTest.php';
require_once 'sys/SearchObject/Factory.php';
require_once 'HTTP/Request2/Adapter/Mock.php';
require_once 'services/AJAX/JSON_RangeVis.php';

/**
 * SearchObject Factory Test Class
 *
 * @category VuFind
 * @package  Tests
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/unit_tests Wiki
 */
class JSON_RangeVisTest extends AbstractMockIndexTest
{
    /**
     * Standard setup method.
     *
     * @return void
     * @access public
     */
    public function setUp()
    {
        global $configArray;

        $this->oldConfigArray = $configArray;

        // Load the default configuration:
        $configArray = parse_ini_file(
            dirname(__FILE__) . '/../../conf/config.ini', true
        );
        
        $this->_searchObject = SearchObjectFactory::initSearchObject('Solr');
        
        // TODO: load from config
        $this->field = "unit_daterange";
    }

    /**
     * Tests range visualization using linear range grouping
     * 
     * @return void
     */
    public function testRangeVisLinear()
    {
        $rangeVis = new JSON_RangeVis();
        
        $indexEngine = $rangeVis->getSearchObject()->getIndexEngine();
        $this->mockIndexEngineWithSampleResponse($indexEngine, 'linearRangeQueryMockResponse.json');
        
        $_REQUEST['field'] = 'unit_daterange';
        $_REQUEST['shape'] = 'linear';
        $_REQUEST['start'] = -8000;
        $_REQUEST['end'] = 2000;
        $_REQUEST['n'] = 20;
        
        $visData = $rangeVis->getVisData();
        $this->assertArrayHasKey('unit_daterange', $visData);
        $this->assertArrayHasKey('data', $visData['unit_daterange']);
        
        // TODO: check the sent query was correct
        
        $data = $visData['unit_daterange']['data'];
        $this->assertEquals(20, count($data));
        
        // TODO: verify the returned data more thoroughly
    }
    
    /**
     * Tests range visualization using parametric bezier curve range grouping
     * 
     * @return void
     */
    public function testRangeVisBezier()
    {
        $rangeVis = new JSON_RangeVis();
        
        $indexEngine = $rangeVis->getSearchObject()->getIndexEngine();
        $this->mockIndexEngineWithSampleResponse($indexEngine, 'rangeQueryMockResponse.json');
        
        $_REQUEST['field'] = 'unit_daterange';
        $_REQUEST['shape'] = 'bezier';
        $_REQUEST['x0'] = 0.99;
        $_REQUEST['y0'] = 0.01;
        $_REQUEST['x1'] = 0.99;
        $_REQUEST['y1'] = 0.01;
        $_REQUEST['start'] = -8000;
        $_REQUEST['end'] = 2000;
        $_REQUEST['n'] = 20;
        
        $visData = $rangeVis->getVisData();
        
        // TODO: check the sent query was correct
        
        $data = $visData['unit_daterange']['data'];
        $this->assertEquals(20, count($data));
        
        // TODO: verify the returned data more thoroughly
    }

    /**
     * Standard teardown method.
     *
     * @return void
     * @access public
     */
    public function tearDown()
    {
        global $configArray;

        $configArray = $this->oldConfigArray;
    }
}
        
?>
