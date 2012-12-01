<?php

namespace Prontotype\Service\Scraper;

class ScrapCollection {

	protected $collection;

    public function __construct()
    {
        $this->collection = array();
    }

    public function add(Scrap $scrap) {
        $this->collection[] = $scrap;
    }

    public function insert($filter, $html)
    {
        $collection = new ScrapCollection();
        foreach ($this->collection as $scrap) {
            $collection->add($scrap->insert($filter, $html));
        }

        return $collection;
    }

    public function __toString() {
        $content = '';
        foreach ($this->collection as $scrap) {
            $content .= (string) $scrap;
        }

        return $content;
    }
}
