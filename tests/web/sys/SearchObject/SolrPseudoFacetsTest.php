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
require_once 'sys/SearchObject/Factory.php';
require_once 'HTTP/Request2/Adapter/Mock.php';

/**
 * SearchObject Factory Test Class
 *
 * @category VuFind
 * @package  Tests
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/unit_tests Wiki
 */
class SearchObjectFactoryTest extends PHPUnit_Framework_TestCase
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
     * Test pseudo facets
     *
     * @return void
     * @access public
     */
    public function testPseudoFacets()
    {
    	$queries = array("[-500000-01-01T00:00:00Z TO 1000-01-01T00:00:00Z]", 
    			"[1000-01-01T00:00:00Z TO 1500-01-01T00:00:00Z]",
    			"[1500-01-01T00:00:00Z TO 2000-01-01T00:00:00Z]");
    	
    	$this->_searchObject->addPseudoFacet($this->field, "Date", $queries);
    	
    	$result = $this->_searchObject->processSearch(true, true);
    	if (PEAR::isError($result)) {
    		$this->markTestSkipped(
              'Index not available for testing.'
            );
    		return;
    	}
    	
    	$facets = $this->_searchObject->getFacetList(array($this->field));
    	$this->assertCount(1, $facets);
    	$this->assertArrayHasKey($this->field, $facets);
    	
    	$facet = $facets[$this->field];
    	$this->assertEquals("Date", $facet['label']);
    	foreach($facet['list'] as $i => $value) {
    		$this->assertEquals($value['untranslated'], $queries[$i]);
    		$this->assertArrayHasKey('count', $value);
    		$this->assertArrayHasKey('isApplied', $value);
    		$this->assertArrayHasKey('url', $value);
    	}
    	
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
