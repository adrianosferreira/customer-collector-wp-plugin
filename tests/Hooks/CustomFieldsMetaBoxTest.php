<?php

namespace ET\CustomerCollector\Tests\Hooks;

use ET\CustomerCollector\Hooks\CustomFieldsMetaBox;
use ET\CustomerCollector\Hooks\PostTypeDefinition;
use ET\CustomerCollector\Model\Customer;
use ET\CustomerCollector\Repository\CustomerFactory;
use ET\CustomerCollector\Repository\CustomerRepository;
use Twig\Environment;
use WP_Mock\Tools\TestCase;

class CustomFieldsMetaBoxTest extends TestCase
{

    public function setUp(): void {
        \WP_Mock::setUp();
    }

    /**
     * @test
     */
    public function itRegisterHooks()
    {
        $template = $this->getMockBuilder(Environment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repository = $this->getMockBuilder(CustomerRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $customerFactory = $this->getMockBuilder(CustomerFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $subject = new CustomFieldsMetaBox($template, $repository, $customerFactory);
        \WP_Mock::expectActionAdded('add_meta_boxes', array($subject, 'register'));
        \WP_Mock::expectActionAdded('save_post', array($subject, 'save'));
        $subject->registerHooks();
    }

    /**
     * @test
     */
    public function itRegisterMetabox()
    {
        $customerFactory = $this->getMockBuilder(CustomerFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $template = $this->getMockBuilder(Environment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repository = $this->getMockBuilder(CustomerRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $subject = new CustomFieldsMetaBox($template, $repository, $customerFactory);

        \WP_Mock::userFunction('add_meta_box', [
            'times' => 1,
            'args' => [
                'et_custom_data',
                'Customer Information',
                [$subject, 'html'],
                PostTypeDefinition::POST_TYPE
            ],
        ]);

        $subject->register();
    }

    /**
     * @test
     */
    public function itRendersHTML()
    {
        $customerFactory = $this->getMockBuilder(CustomerFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $template = $this->getMockBuilder(Environment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repository = $this->getMockBuilder(CustomerRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $subject = new CustomFieldsMetaBox($template, $repository, $customerFactory);
        $post = $this->getMockBuilder(\WP_Post::class)
            ->disableOriginalConstructor()
            ->getMock();
        $post->ID = 1;

        $customer = new Customer();
        $customer->setPhone(111)
            ->setMessage('message')
            ->setEmail('email')
            ->setBudget(222)
            ->setName('name')
            ->setId(123);

        $repository->method('getById')
            ->with(1)
            ->willReturn($customer);

        $template->expects($this->once())
            ->method('render')
            ->with('meta-box.twig', [
                'email'   => $customer->getEmail(),
                'phone'   => $customer->getPhone(),
                'budget'  => $customer->getBudget(),
                'message' => $customer->getMessage(),
            ])
            ->willReturn('rendered meta box HTML');

        ob_start();
        $subject->html($post);
        $output = ob_get_clean();
        $this->assertEquals('rendered meta box HTML', $output);
    }

    /**
     * @test
     */
    public function itRendersHTMLWithoutValues()
    {
        $customerFactory = $this->getMockBuilder(CustomerFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $template = $this->getMockBuilder(Environment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repository = $this->getMockBuilder(CustomerRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $subject = new CustomFieldsMetaBox($template, $repository, $customerFactory);
        $post = $this->getMockBuilder(\WP_Post::class)
            ->disableOriginalConstructor()
            ->getMock();
        $post->ID = 1;

        $repository->method('getById')
            ->with(1)
            ->willReturn(null);

        $template->expects($this->once())
            ->method('render')
            ->with('meta-box.twig', [])
            ->willReturn('rendered meta box HTML');

        ob_start();
        $subject->html($post);
        $output = ob_get_clean();
        $this->assertEquals('rendered meta box HTML', $output);
    }

    /**
     * @test
     */
    public function itDoesntSavePostFieldsWhenItIsNotCustomerBeingSaved()
    {
        $customerFactory = $this->getMockBuilder(CustomerFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $template = $this->getMockBuilder(Environment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repository = $this->getMockBuilder(CustomerRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $subject = new CustomFieldsMetaBox($template, $repository, $customerFactory);
        unset($_POST['et-customer-data']);

        $repository->expects($this->never())
            ->method('save');

        $subject->save(1);
    }

    /**
     * @test
     */
    public function itSavesCustomerPostFields()
    {
        $customerFactory = $this->getMockBuilder(CustomerFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $template = $this->getMockBuilder(Environment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repository = $this->getMockBuilder(CustomerRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $customer = $this->getMockBuilder(Customer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $customerFactory->method('create')
            ->willReturn($customer);

        $_POST['et-customer-name'] = 'name';
        $_POST['et-customer-email'] = 'email';
        $_POST['et-customer-budget'] = 1000;
        $_POST['et-customer-phone'] = 456123789;
        $_POST['et-customer-message'] = 'message';

        \WP_Mock::userFunction('sanitize_text_field', [
            'args' => $_POST['et-customer-name'],
            'return' => $_POST['et-customer-name'],
        ]);

        \WP_Mock::userFunction('sanitize_email', [
            'args' => $_POST['et-customer-email'],
            'return' => $_POST['et-customer-email'],
        ]);

        \WP_Mock::userFunction('sanitize_text_field', [
            'args' => $_POST['et-customer-budget'],
            'return' => $_POST['et-customer-budget'],
        ]);

        \WP_Mock::userFunction('sanitize_textarea_field', [
            'args' => $_POST['et-customer-message'],
            'return' => $_POST['et-customer-message'],
        ]);

        \WP_Mock::userFunction('sanitize_text_field', [
            'args' => $_POST['et-customer-phone'],
            'return' => $_POST['et-customer-phone'],
        ]);

        $id = 1;

        $customer->method('setId')
            ->with($id)
            ->willReturn($customer);

        $customer->method('setName')
            ->with($_POST['et-customer-name'])
            ->willReturn($customer);

        $customer->method('setBudget')
            ->with($_POST['et-customer-budget'])
            ->willReturn($customer);

        $customer->method('setEmail')
            ->with($_POST['et-customer-email'])
            ->willReturn($customer);

        $customer->method('setPhone')
            ->with($_POST['et-customer-phone'])
            ->willReturn($customer);

        $customer->method('setMessage')
            ->with($_POST['et-customer-message'])
            ->willReturn($customer);

        $subject = new CustomFieldsMetaBox($template, $repository, $customerFactory);

        $_POST['et-customer-data'] = 'some data';

        $repository->expects($this->once())
            ->method('save')
            ->with($customer);

        \WP_Mock::userFunction('remove_action', [
            'times' => 1,
            'args' => ['save_post', array($subject, 'save')]
        ]);

        \WP_Mock::expectActionAdded('save_post', array($subject, 'save'));

        $subject->save($id);

        unset($_POST['et-customer-data']);
    }

    public function tearDown(): void {
        $this->assertConditionsMet();
        \WP_Mock::tearDown();
    }
}