<?php
/**
 * Subclass this abstract test class to add mock index capability to your test.
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
 * @author   Eero Heikkinen <eero.heikkinen@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/unit_tests Wiki
 */
@require_once 'HTTP/Request.php';

/**
 * Abstract Mock Index Test Class
 *
 * @category VuFind
 * @package  Tests
 * @author   Eero Heikkinen <eero.heikkinen@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/unit_tests Wiki
 */
abstract class AbstractMockIndexTest extends PHPUnit_Framework_TestCase
{
    protected $mock;
    
    /**
     * Rigs given index engine to being feed the given mock response. 
     * Returns the mock response, which can be used to retrieve the request
     * received from the index engine in retrospective.
     * 
     * @param IndexEngine $indexEngine        The engine to rig
     * @param string      $sampleResponseFile The sample to use (from tests/samples folder)
     * 
     * @return MockRequest
     */
    public function mockIndexEngineWithSampleResponse($indexEngine, $sampleResponseFile)
    {
        $actualdir = dirname(__FILE__);
        $sample = file_get_contents($actualdir . "/../samples/" . $sampleResponseFile);
        
        $this->mock = new MockRequest();
        $this->mock->setResponse($sample);
        $indexEngine->client = $this->mock;
        
        return $this->mock;
    }
    
    /**
     * Returns the mock request backing this class
     * 
     * @return MockRequest The mock request
     */
    public function getMock()
    {
        return $this->mock;
    }
    
    /**
     * Helper function that retrieves last received query from mock object
     * 
     * @return string The last query the mock received
     */
    public function getLastQuery()
    {
        return $this->mock->getLastQuery();
    }
}

/**
 * Class for mocking response body, code, and supressing sending data.
 *
 * @category VuFind
 * @package  Tests
 * @author   Eero Heikkinen <eero.heikkinen@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/unit_tests Wiki
 */
class MockRequest extends HTTP_Request
{
    protected $responseBody;
    protected $responseCode;
    protected $lastQuery;
    
    /**
     * Stubbed parent class method.
     * 
     * @return void
     */
    public function sendRequest()
    {
    }
    
    /**
     * Method that sets the mock response this class will send
     * 
     * @param string $response The response
     * 
     * @return void
     */
    public function setResponse($response)
    {
        $this->responseBody = $response;
    }

    
    /**
     * Method that sets the mock response code this class will send
     * 
     * @param number $code Code to mock response with
     * 
     * @return void
     */
    public function setResponseCode($code)
    {
        $this->responseCode = $code;
    }
    
    /**
     * Get mocked response code
     *
     * @return number Response code
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }
    
    /**
     * Overridden parent method to get mock response body
     * 
     * @return string
     */
    public function getResponseBody()
    {
        return $this->responseBody;
    }
    
    /**
     * Overridden parent class method that sets request query (GET)
     * 
     * @param string $query The query
     * 
     * @return void 
     */
    public function addRawQueryString($query)
    {
        $this->lastQuery = $query;
    }
    
    /**
     * Overridden parent class method that sets request query (POST)
     *
     * @param string $query The query
     *
     * @return void
     */
    public function setBody($query)
    {
        $this->lastQuery = $query;
    }
    
    /**
     * Helper method that retrieves the last query received.
     * 
     * @return string Last query
     */
    public function getLastQuery()
    {
        return $this->lastQuery;
    }
}