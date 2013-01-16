<?php
@require_once 'HTTP/Request.php';

/**
 * Class for mocking response body, code, and supressing sending data.
 * 
 * @uses      Services_TwitPic_Request_HTTPRequest
 * @category  Services
 * @package   Services_TwitPic
 * @author    Bill Shupp <hostmaster@shupp.org> 
 * @copyright 2008 Bill Shupp
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link      http://servicestwitpic.googlecode.com
 */
class MockRequest extends HTTP_Request
{
    /**
     * Canned response body for testing
     * 
     * @var string
     */
    private $responseBody;

     /**
     * Canned response code for testing
     * 
     * @var string
     */
    private $responseCode;
    
    protected $lastQuery;

    /**
     * Doesn't actually send anything
     * 
     * @return void
     */
    public function sendRequest()
    {
    }
    
    public function setResponse($response) {
        $this->responseBody = $response;
    }

    /**
     * Returns the canned response code
     * 
     * @return void
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }
    
    /**
     * Returns the canned response code
     *
     * @return void
     */
    public function setResponseCode($code)
    {
        $this->responseCode = $code;
    }

    /**
     * Returns the canned response body
     * 
     * @return void
     */
    public function getResponseBody()
    {
        return $this->responseBody;
    }
    
    public function addRawQueryString($query) {
        $this->lastQuery = $query;
    }
    
    public function setBody($query) {
        $this->lastQuery = $query;
    }
    
    public function getLastQuery() {
        return $this->lastQuery;
    }

}