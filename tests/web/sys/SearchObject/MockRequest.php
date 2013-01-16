<?php
@require_once 'HTTP/Request.php';

/**
 * Class for mocking response body, code, and supressing sending data.
 */
class MockRequest extends HTTP_Request
{
    protected $responseBody;
    protected $responseCode;
    protected $lastQuery;
	
    public function sendRequest()
    {
    }
    
    public function setResponse($response) {
        $this->responseBody = $response;
    }

    public function getResponseCode()
    {
        return $this->responseCode;
    }
    
    public function setResponseCode($code)
    {
        $this->responseCode = $code;
    }

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