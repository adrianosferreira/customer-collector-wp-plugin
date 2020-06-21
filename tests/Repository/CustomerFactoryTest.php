<?php

namespace ET\CustomerCollector\Tests\Repository;

use ET\CustomerCollector\Model\Customer;
use ET\CustomerCollector\Repository\CustomerFactory;
use WP_Mock\Tools\TestCase;

class CustomerFactoryTest extends TestCase
{

    /**
     * @test
     */
    public function itReturnsInstanceOfCustomer()
    {
        $subject = new CustomerFactory();
        $this->assertInstanceOf(Customer::class, $subject->create());
    }
}