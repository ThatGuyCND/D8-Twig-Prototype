<?php

namespace Prontotype\Scraper;

class ScrapCollection implements ScrapInterface {

	protected $collection;

    public function __construct()
    {
        $this->collection = array();
    }

    public function add(ScrapInterface $scrap) {
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

    public function filter ($filter)
    {
        $collection = new ScrapCollection();
        foreach ($this->collection as $scrap) {
            $collection->add($scrap->filter($filter));
        }

        return $collection;
    }

    public function content()
    {
        $content = '';
        foreach ($this->collection as $scrap) {
            $content .= $scrap->content();
        }

        return $content;
    }

    public function __toString() {
        return $this->content();
    }
}
