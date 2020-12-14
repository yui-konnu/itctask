<?php

namespace App\Services;

use App\Exceptions\ApiClientException;
use Http;


class ApiClientService {

    protected $endpoint;

    public function __construct() {
        $this->endpoint = 'https://www.itccompliance.co.uk/recruitment-webservice/api/';
    }

    /**
     * Get the list of products, then info for each and return in an array
     * 
     */
    public function getProducts() {
        $response = $this->retryRequest('list');

        $result = [ ];

        foreach($response['products'] as $product => $name) {
            $info = $this->retryRequest('info', [ 'id' => $product ]);

            $result[] = $this->sanitiseArray(
                $info[$product]
            );
        }

        return $result;
    }

    /**
     * Run the sanitiseText() recursively for each text item in an array
     * 
     */
    public function sanitiseArray($arr) {
        foreach($arr as &$item) {
            if(is_array($item)) {
                $item = $this->sanitiseArray($item);
            }
            else {
                $item = $this->sanitiseText($item);
            }
        }
        
        return $arr;
    }

    /**
     * Remove the following instances from an input string:
     * - Html tags and any content contained within
     * - Any characters that are outside of printable ASCII range
     * - Surrounding quotes
     *
     */
    public function sanitiseText($text) {
        // Regex to remove html tags, and any content within them
        $text = preg_replace('/<\w+.+>/', '', $text);

        // Remove any html tags that were not caught in the above regex
        // This will still keep any content within the tags
        $text = strip_tags($text);

        // Remove chars outside the alphanumeric zone of the ascii table (0x00 - 0x1f)
        $text = preg_replace('/[\x00-\x1f]/', '', $text);

        // Strip quotes from the start/end of the string
        $text = trim($text, '"');

        return $text;
    }

    /**
     * Test a HTTP response was successful, it is valid JSON, and it doesn't contain an 'error' key
     * 
     */
    public function isResponseSuccess($response) {
        if($response->failed()) {
            return false;
        }

        $body = json_decode($response->body(), true);

        return $body && !array_key_exists('error', $body);
    }

    /**
     * Retry a request a max. 10 times until it succeeds.
     * As we don't want to hang the page indefinitley, we will throw an ApiClientException if the tries are exceeded
     *  
     */
    public function retryRequest($method, $params = null) {
        $tries = 10;

        for($i = 0; $i < $tries; $i++) {
            $resp = Http::get(
                $this->makeMethodUrl($method, $params)
            );

            if($this->isResponseSuccess($resp)) {
                return json_decode($resp->body(), true);
            }
        }

        throw new ApiClientException('Failed to retrieve data after max retries');
    }

    /**
     * Compose a URL for the API using a method, and optionally query params
     * 
     */
    public function makeMethodUrl($method, $params = null) {
        return sprintf('%s%s?%s',
            $this->endpoint,
            $method,
            $params ? http_build_query($params) : '');
    }

}