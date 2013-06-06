<?php

namespace Prontotype\Scraper;

class Scraper {

	protected $app;
	protected $client;

    public function __construct( $app )
    {
        $this->app = $app;
    }

    public function get( $uri )
    {
        $html = $this->make_external_request($uri);
        $dom = new \DOMDocument('1.0', 'utf8');
        $dom->validateOnParse = false;

        $current = libxml_use_internal_errors(true);
        $dom = new Scrap($html, $uri);
        libxml_use_internal_errors($current);

        return $dom;
    }

    // TODO refactor this and Data::make_external_request into a single function with optional cache
	protected function make_external_request( $url )
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		$data = curl_exec($ch);
		curl_close($ch);

		return $data;
	}
}
