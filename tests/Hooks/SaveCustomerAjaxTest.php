<?php

namespace ET\CustomerCollector\Tests\Hooks;

use ET\CustomerCollector\Hooks\SaveCustomerAjax;
use ET\CustomerCollector\Model\Customer;
use ET\CustomerCollector\Repository\CustomerFactory;
use ET\CustomerCollector\Repository\CustomerRepository;
use WP_Mock\Tools\TestCase;

class SaveCustomerAjaxTest extends TestCase
{
    public function setUp(): void {
        \WP_Mock::setUp();
    }

    /**
     * @test
     */
    public function itRegisterHooks()
    {
        $repository = $this->getMockBuilder(CustomerRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $factory = $this->getMockBuilder(CustomerFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $subject = new SaveCustomerAjax($repository, $factory);
        \WP_Mock::expectActionAdded('wp_ajax_et_save_customer', array($subject, 'save'));
        $subject->registerHooks();
    }

    /**
     * @test
     */
    public function itDoesNotSaveWrongNonce()
    {
        $repository = $this->getMockBuilder(CustomerRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $factory = $this->getMockBuilder(CustomerFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $_POST['nonce'] = 'wrong nonce';

        \WP_Mock::userFunction('wp_verify_nonce', [
            'args' => [ $_POST['nonce'], 'et_save_customer' ],
            'return' => false,
        ]);

        \WP_Mock::userFunction('wp_send_json_success', [
            'times' => 0
        ]);

        \WP_Mock::userFunction('wp_send_json_error', [
            'times' => 1
        ]);

        $subject = new SaveCustomerAjax($repository, $factory);
        $subject->save();
    }

    /**
     * @test
     */
    public function itSavesCustomer()
    {
        $repository = $this->getMockBuilder(CustomerRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $factory = $this->getMockBuilder(CustomerFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $_POST['nonce'] = 'wrong nonce';

        \WP_Mock::userFunction('wp_verify_nonce', [
            'args' => [ $_POST['nonce'], 'et_save_customer' ],
            'return' => true,
        ]);

        \WP_Mock::userFunction('wp_send_json_success', [
            'times' => 1
        ]);

        \WP_Mock::userFunction('wp_send_json_error', [
            'times' => 0
        ]);

        $_POST['et-customer-name'] = 'name';
        $_POST['et-customer-email'] = 'email';
        $_POST['et-customer-budget'] = 123456;
        $_POST['et-customer-phone'] = 123456456798;
        $_POST['et-customer-message'] = 'messa';

        $customer = new Customer();
        $customer->setName($_POST['et-customer-name'])
            ->setMessage($_POST['et-customer-message'])
            ->setBudget($_POST['et-customer-budget'])
            ->setEmail($_POST['et-customer-email'])
            ->setPhone($_POST['et-customer-phone']);

        $factory->method('create')->willReturn($customer);
        $repository->expects($this->once())
            ->method('save')
            ->with($customer);

        $subject = new SaveCustomerAjax($repository, $factory);
        $subject->save();
    }

    public function tearDown(): void {
        $this->assertConditionsMet();
        \WP_Mock::tearDown();
    }
}