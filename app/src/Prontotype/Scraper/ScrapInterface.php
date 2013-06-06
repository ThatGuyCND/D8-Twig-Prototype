<?php

namespace Prontotype\Scraper;

interface ScrapInterface {

    function filter($filter);

    function insert($filter, $html);

    function content ();
}
