<?php

namespace ET\CustomerCollector\Tests\Repository;

use ET\CustomerCollector\Hooks\PostTypeDefinition;
use ET\CustomerCollector\Model\Customer;
use ET\CustomerCollector\Repository\CustomerFactory;
use ET\CustomerCollector\Repository\CustomerRepository;
use WP_Mock\Tools\TestCase;

class CustomerRepositoryTest extends TestCase
{

    public function setUp(): void
    {
        \WP_Mock::setUp();
    }

    /**
     * @test
     */
    public function itReturnsNullWhenCustomerIsNotFound()
    {
        $factory = $this->getMockBuilder(CustomerFactory::class)
            ->disableOriginalConstructor()->getMock();

        \WP_Mock::userFunction('get_post', [
            'args'   => 1,
            'return' => null,
        ]);

        $subject = new CustomerRepository($factory);
        $this->assertNull($subject->getById(1));
    }

    /**
     * @test
     */
    public function itReturnsCustomer()
    {
        $factory = $this->getMockBuilder(CustomerFactory::class)
            ->disableOriginalConstructor()->getMock();

        $id      = 1;
        $name    = 'customer name';
        $budget  = 1000;
        $email   = 'email@gmail.com';
        $message = 'my message';
        $phone   = 123456789;

        $post             = new \stdClass();
        $post->post_title = $name;

        $customer = $this->getMockBuilder(Customer::class)
            ->disableOriginalConstructor()->getMock();

        $customer->method('setId')->with($id)->willReturn($customer);

        $customer->method('setName')->with($name)->willReturn($customer);

        $customer->method('setBudget')->with($budget)->willReturn($customer);

        $customer->method('setEmail')->with($email)->willReturn($customer);

        $customer->method('setPhone')->with($phone)->willReturn($customer);

        $customer->method('setMessage')->with($message)->willReturn($customer);

        $factory->method('create')->willReturn($customer);

        \WP_Mock::userFunction('get_post_meta', [
            'args'   => [
                $id,
                CustomerRepository::BUDGET_CF,
                true
            ],
            'return' => $budget,
        ]);

        \WP_Mock::userFunction('get_post_meta', [
            'args'   => [
                $id,
                CustomerRepository::EMAIL_CF,
                true
            ],
            'return' => $email,
        ]);

        \WP_Mock::userFunction('get_post_meta', [
            'args'   => [
                $id,
                CustomerRepository::MESSAGE_CF,
                true
            ],
            'return' => $message,
        ]);

        \WP_Mock::userFunction('get_post_meta', [
            'args'   => [
                $id,
                CustomerRepository::PHONE_CF,
                true
            ],
            'return' => $phone,
        ]);

        \WP_Mock::userFunction('get_post', [
            'args'   => 1,
            'return' => $post,
        ]);

        $subject = new CustomerRepository($factory);
        $this->assertEquals($customer, $subject->getById($id));
    }

    /**
     * @test
     */
    public function itInsertsNewCustomer()
    {
        $name    = 'Name';
        $budget  = 1000;
        $email   = 'email';
        $message = 'message';
        $phone   = 123456789;
        $date    = '2020-06-22 23:55:00';

        $customer = new Customer();
        $customer->setName($name)
            ->setPhone($phone)
            ->setMessage($message)
            ->setBudget($budget)
            ->setEmail($email)
            ->setDate($date);

        \WP_Mock::userFunction('wp_update_post', [
                'times' => 0,
            ]);

        \WP_Mock::userFunction('sanitize_email', [
            'args'   => $customer->getEmail(),
            'return' => $customer->getEmail(),
        ]);

        \WP_Mock::userFunction('sanitize_text_field', [
            'args'   => $customer->getName(),
            'return' => $customer->getName(),
        ]);

        \WP_Mock::userFunction('sanitize_text_field', [
            'args'   => $customer->getBudget(),
            'return' => $customer->getBudget(),
        ]);

        \WP_Mock::userFunction('sanitize_textarea_field', [
            'args'   => $customer->getMessage(),
            'return' => $customer->getMessage(),
        ]);

        \WP_Mock::userFunction('sanitize_text_field', [
            'args'   => $customer->getPhone(),
            'return' => $customer->getPhone(),
        ]);

        $id = 1;

        \WP_Mock::userFunction('wp_insert_post', [
            'args'   => [
                [
                    'post_title'  => $customer->getName(),
                    'post_type'   => PostTypeDefinition::POST_TYPE,
                    'post_status' => 'private',
                    'post_date'   => '2020-06-22 23:55:00'
                ]
            ],
            'times'  => 1,
            'return' => $id,
        ]);

        \WP_Mock::userFunction('update_post_meta', [
            'times' => 1,
            'args'  => [
                $id,
                CustomerRepository::EMAIL_CF,
                $customer->getEmail(),
            ]
        ]);

        \WP_Mock::userFunction('update_post_meta', [
            'times' => 1,
            'args'  => [
                $id,
                CustomerRepository::BUDGET_CF,
                $customer->getBudget(),
            ]
        ]);

        \WP_Mock::userFunction('update_post_meta', [
            'times' => 1,
            'args'  => [
                $id,
                CustomerRepository::MESSAGE_CF,
                $customer->getMessage(),
            ]
        ]);

        \WP_Mock::userFunction('update_post_meta', [
            'times' => 1,
            'args'  => [
                $id,
                CustomerRepository::PHONE_CF,
                $customer->getPhone(),
            ]
        ]);

        \WP_Mock::userFunction('wp_update_post', [
            'times' => 0,
        ]);

        \WP_Mock::userFunction('get_post_status', [
            'args'   => $id,
            'return' => 'private',
        ]);

        $factory = $this->getMockBuilder(CustomerFactory::class)
            ->disableOriginalConstructor()->getMock();

        $customerFactory = $this->getMockBuilder(CustomerFactory::class)
            ->disableOriginalConstructor()->getMock();

        $subject = new CustomerRepository($factory, $customerFactory);
        $subject->save($customer);
    }

    /**
     * @test
     */
    public function itUpdatesPostStatus()
    {
        $name    = 'Name';
        $budget  = 1000;
        $email   = 'email';
        $message = 'message';
        $phone   = 123456789;

        $customer = new Customer();
        $customer->setName($name)
            ->setPhone($phone)
            ->setMessage($message)
            ->setBudget($budget)
            ->setEmail($email)
            ->setId(1);

        \WP_Mock::userFunction('wp_update_post', [
            'times' => 1,
            'args' => [[
                'ID' => $customer->getId(),
                'post_status' => 'private',
            ]]
        ]);

        \WP_Mock::userFunction('sanitize_text_field', [
            'args'   => $customer->getName(),
            'return' => $customer->getName(),
        ]);

        \WP_Mock::userFunction('sanitize_email', [
            'args'   => $customer->getEmail(),
            'return' => $customer->getEmail(),
        ]);

        \WP_Mock::userFunction('sanitize_text_field', [
            'args'   => $customer->getBudget(),
            'return' => $customer->getBudget(),
        ]);

        \WP_Mock::userFunction('sanitize_textarea_field', [
            'args'   => $customer->getMessage(),
            'return' => $customer->getMessage(),
        ]);

        \WP_Mock::userFunction('sanitize_text_field', [
            'args'   => $customer->getPhone(),
            'return' => $customer->getPhone(),
        ]);

        $id = 1;

        \WP_Mock::userFunction('wp_insert_post', [
            'times'  => 0,
        ]);

        \WP_Mock::userFunction('update_post_meta', [
            'times' => 1,
            'args'  => [
                $id,
                CustomerRepository::EMAIL_CF,
                $customer->getEmail(),
            ]
        ]);

        \WP_Mock::userFunction('update_post_meta', [
            'times' => 1,
            'args'  => [
                $id,
                CustomerRepository::BUDGET_CF,
                $customer->getBudget(),
            ]
        ]);

        \WP_Mock::userFunction('update_post_meta', [
            'times' => 1,
            'args'  => [
                $id,
                CustomerRepository::MESSAGE_CF,
                $customer->getMessage(),
            ]
        ]);

        \WP_Mock::userFunction('update_post_meta', [
            'times' => 1,
            'args'  => [
                $id,
                CustomerRepository::PHONE_CF,
                $customer->getPhone(),
            ]
        ]);

        \WP_Mock::userFunction('wp_update_post', [
            'times' => 0,
        ]);

        \WP_Mock::userFunction('get_post_status', [
            'args'   => $id,
            'return' => 'private',
        ]);

        $factory = $this->getMockBuilder(CustomerFactory::class)
            ->disableOriginalConstructor()->getMock();

        $customerFactory = $this->getMockBuilder(CustomerFactory::class)
            ->disableOriginalConstructor()->getMock();

        $subject = new CustomerRepository($factory, $customerFactory);
        $subject->save($customer);
    }

    public function tearDown(): void
    {
        $this->assertConditionsMet();
        \WP_Mock::tearDown();
    }
}