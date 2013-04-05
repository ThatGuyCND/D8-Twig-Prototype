<?php

namespace Prontotype\Service\Scraper;

interface ScrapInterface {

    function filter($filter);

    function insert($filter, $html);

    function content ();
}
