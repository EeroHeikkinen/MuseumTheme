<?php
/**
 * Preview Service Test Class
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
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @author   Eero Heikkinen <eero.heikkinen@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/unit_tests Wiki
 */
require_once dirname(__FILE__) . '/../../prepend.inc.php';
require_once dirname(__FILE__) . '/../../AbstractMockIndexTest.php';
require_once 'sys/SearchObject/Factory.php';
require_once 'HTTP/Request2/Adapter/Mock.php';
require_once 'services/Record/Preview.php';
require_once 'sys/Interface.php';

/**
 * JSON Range Visualization Test Class
 *
 * @category VuFind
 * @package  Tests
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @author   Eero Heikkinen <eero.heikkinen@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/unit_tests Wiki
 */
class PreviewTest extends PHPUnit_Framework_TestCase
{
    protected $mockService;
    protected $mockDriver;
    protected $mockPreview;
    
    /**
     * PHPUnit setup method
     * 
     * @return void
     */
    public function setUp() 
    {     
        // Mock dependencies to create a true unit test
        $this->mockService = $this->getMockBuilder('Normalization_Service')
            ->setMethods(array('normalize'))
            ->getMock();
        
        $this->mockDriver = $this->getMockBuilder('IndexRecord')
            ->setMethods(array('getCoreMetadata'))
            ->getMock();
        
        global $interface;
        $interface = $this->getMockBuilder('UInterface')
            ->setMethods(array('display', 'setPageTitle'))
            ->disableOriginalConstructor()
            ->getMock();
        
        // Mock the SUT as well to remove dependency to a static call
        $this->mockPreview = $this->getMockBuilder('Preview')
            ->setMethods(array('getDriver'))
            ->setConstructorArgs(array($this->mockService))
            ->getMock();
    }

    /**
     * Tests range visualization using linear range grouping
     * 
     * @return void
     */
    public function testUnit()
    {
        global $interface;
        
        $indexFields = array('thumbnail' => 'http://foo.bar/woo.yay', 'foo' => 'bar', 'woo' => 'yay');
        $driverFields = array('hoopla' => 'doo');
        
        $this->mockDriver
            ->expects($this->once())
            ->method('getCoreMetadata')
            ->will($this->returnValue("fooTemplate.tpl"));

        $this->mockService
            ->expects($this->once())
            ->method('normalize')
            ->will($this->returnValue($indexFields));
        
        $this->mockPreview
            ->expects($this->once())
            ->method('getDriver')
            ->with($this->equalTo($indexFields))
            ->will($this->returnValue($this->mockDriver));

        $this->mockPreview->launch();
        
        $vars = $interface->_tpl_vars;
        $this->assertEquals("fooTemplate.tpl", $vars['coreMetadata']);
        $this->assertEquals("http://foo.bar/woo.yay", $vars['coreThumbMedium']);
        
        // The rest of the fields won't be here because getCoreMetadata is mocked
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
