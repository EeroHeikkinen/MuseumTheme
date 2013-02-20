<?php
/**
 * Solr Pseudo Facets Test Class
 *
 * PHP version 5
 *
 * Copyright (C) Villanova University 2010.
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
 * @package  Tests
 * @author   Eero Heikkinen <eero.heikkinen@nba.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/unit_tests Wiki
 */
require_once dirname(__FILE__) . '/../../prepend.inc.php';
require_once dirname(__FILE__) . '/../../AbstractMockIndexTest.php';
require_once 'sys/SearchObject/Factory.php';

/**
 * Solr Pseudo Facets Test Class
 *
 * @category VuFind
 * @package  Tests
 * @author   Eero Heikkinen <eero.heikkinen@nba.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/unit_tests Wiki
 */
class SolrPseudoFacetsTest extends AbstractMockIndexTest
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
        
        $indexEngine = $this->_searchObject->getIndexEngine();
        $this->mockIndexEngineWithSampleResponse($indexEngine, 'mockResponse.json');
        
        $result = $this->_searchObject->processSearch(true, true);
        if (PEAR::isError($result)) {
            $this->fail("PEAR error");
        }
        
        // Verify that the sent query was correct
        $query = $this->getLastQuery();
        $this->assertRegExp('/facet.query='. $this->field .'%3A ?%5B-500000-01-01T00%3A00%3A00Z+.?TO.?+1000-01-01T00%3A00%3A00Z%5D/', $query);
        $this->assertRegExp('/facet.query='. $this->field .'%3A ?%5B1000-01-01T00%3A00%3A00Z+.?TO.?+1500-01-01T00%3A00%3A00Z%5D/', $query);
        $this->assertRegExp('/facet.query='. $this->field .'%3A ?%5B1500-01-01T00%3A00%3A00Z+.?TO.?+2000-01-01T00%3A00%3A00Z%5D/', $query);

        // Get and analyze the returned facet
        $facets = $this->_searchObject->getFacetList(array($this->field));
        $this->assertArrayHasKey($this->field, $facets);
        $facet = $facets[$this->field];
        
        $this->assertEquals("Date", $facet['label']);
        
        $this->assertEquals(1201, $facet['list'][0]['count']);
        $this->assertEquals("[-500000-01-01T00:00:00Z TO 1000-01-01T00:00:00Z]", $facet['list'][0]['untranslated']);
        
        $this->assertEquals(1330, $facet['list'][1]['count']);
        $this->assertEquals("[1000-01-01T00:00:00Z TO 1500-01-01T00:00:00Z]", $facet['list'][1]['untranslated']);
        
        $this->assertEquals(1997970, $facet['list'][2]['count']);
        $this->assertEquals("[1500-01-01T00:00:00Z TO 2000-01-01T00:00:00Z]", $facet['list'][2]['untranslated']);
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
