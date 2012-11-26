<?php

namespace Prontotype\Service\Scraper;

use Symfony\Component\CssSelector\CssSelector;
use Symfony\Component\DomCrawler\Crawler;

class Scrap {

	protected $dom;
	protected $xpath;

    public function __construct( $html, $uri )
    {
        $this->dom = new \DOMDocument('1.0', 'utf8');
        $this->dom->validateOnParse = false;

        $current = libxml_use_internal_errors(true);
        $this->dom->loadHTML($html);
        libxml_use_internal_errors($current);

        $this->xpath = new \DOMXpath($this->dom);

        // change href and src attribute values
        foreach (array('href', 'src') as $attr) {
            foreach ($this->xpath->query("//*[@" . $attr . "]") as $element) {
                $url = $this->absolute_url($uri, $element->getAttribute($attr));
                $element->setAttribute($attr, $url);
            }
        }
    }

    public function filter($filter)
    {
        // perform filter
        $crawler = new Crawler($this->dom->saveHtml());
        $results = $crawler->filter($filter)->each(function ($node, $i) {
            $xml = simplexml_import_dom($node);
            return $xml;
        });

        return $results;
    }

    public function insert($filter, $html)
    {
        $html = trim ( (string) $html);
        if (empty($html) ) {
            return $this;
        }

        $tempDom = new \DOMDocument('1.0', 'utf8');
        $tempDom->loadHTML($html);
        $import = $tempDom->getElementsByTagName('body')->item(0);

        foreach ($this->xpath->query(CssSelector::toXPath($filter)) as $element) {
            $firstChild = $element->firstChild;

            foreach ($import->childNodes as $child) {
                $importedNode = $this->dom->importNode($child, true);
                $element->insertBefore($importedNode, $firstChild);
            }
        }

        return $this;
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

    public function __toString() {
        return $this->dom->saveHtml();
    }
}
