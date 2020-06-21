<?php

namespace ET\CustomerCollector\Repository;

use ET\CustomerCollector\Model\Customer;

class CustomerFactory
{
    public function create() {
        return new Customer();
    }
}