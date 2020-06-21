<?php

namespace ET\CustomerCollector\Tests\Template;

use ET\CustomerCollector\Template\Loader;
use Twig\Environment;
use WP_Mock\Tools\TestCase;

class LoaderTest extends TestCase
{

    /**
     * @test
     */
    public function itReturnsInstanceOfEnvironmentTemplate()
    {
        $subject = new Loader();
        $this->assertInstanceOf(Environment::class, $subject->get());
    }
}