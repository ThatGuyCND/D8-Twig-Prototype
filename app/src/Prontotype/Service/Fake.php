<?php

namespace Prontotype\Service;

use Symfony\Component\DomCrawler\Crawler;

class Fake {

	protected $app;
	protected $client;

    public function __construct( $app )
    {
        $this->app = $app;
    }

    public function scrap( $uri = '', $filter = '' )
    {
        $html = $this->make_external_request($uri);
        $dom = new \DOMDocument('1.0', 'utf8');
        $dom->validateOnParse = false;

        $current = libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_use_internal_errors($current);

        // change href and src attribute values
        $xpath = new \DOMXpath($dom);

        foreach (array('href', 'src') as $attr) {
            foreach ($xpath->query("//*[@" . $attr . "]") as $element) {
                $url = $this->absolute_url($uri, $element->getAttribute($attr));
                $element->setAttribute($attr, $url);
            }
        }

        // perform filter
        $crawler = new Crawler($dom->saveHtml());
        $results = $crawler->filter($filter)->each(function ($node, $i) {
            $xml = simplexml_import_dom($node);
            return $xml;
        });

        return $results;
    }

    /*
    * from http://www.php.net/manual/en/function.realpath.php#85388
    */
    protected function absolute_url ($base, $href) {
        if (!$href) {
            return $base;
        }

        $rel_parsed = parse_url($href);
        if (array_key_exists('scheme', $rel_parsed)) {
            return $href;
        }

        $base_parsed = parse_url("$base ");
        if (!array_key_exists('path', $base_parsed)) {
            $base_parsed = parse_url("$base/ ");
        }

        if ($href{0} === "/") {
            $path = $href;
        } else {
            $path = dirname($base_parsed['path']) . "/$href";
        }

        $path = preg_replace('~/\./~', '/', $path);

            $parts = array();
            foreach (
                explode('/', preg_replace('~/+~', '/', $path)) as $part
            ) if ($part === "..") {
                array_pop($parts);
            } elseif ($part!="") {
                $parts[] = $part;
            }

        return (
            (array_key_exists('scheme', $base_parsed)) ?
                $base_parsed['scheme'] . '://' . $base_parsed['host'] : ""
        ) . "/" . implode("/", $parts);
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
