<?php

namespace ET\CustomerCollector\Template;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Loader
{

    public function get()
    {
        return new Environment(
            new FilesystemLoader(CUSTOMER_COLLECTOR_PATH . '/templates'),
            []
        );
    }
}