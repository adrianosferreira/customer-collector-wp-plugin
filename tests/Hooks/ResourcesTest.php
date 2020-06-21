<?php

namespace ET\CustomerCollector\Tests\Hooks;

use ET\CustomerCollector\Hooks\PostTypeDefinition;
use ET\CustomerCollector\Hooks\Resources;
use WP_Mock\Tools\TestCase;

class ResourcesTest extends TestCase
{
    public function setUp(): void {
        \WP_Mock::setUp();
    }

    /**
     * @test
     */
    public function itRegisterHooks() {
        $subject = new Resources();
        \WP_Mock::expectActionAdded('admin_enqueue_scripts', array($subject, 'enqueueCustomerEditResources'));
        \WP_Mock::expectActionAdded('wp_enqueue_scripts', array($subject, 'enqueueCustomerFrontEndFormResources'));
        $subject->registerHooks();
    }

    /**
     * @test
     */
    public function itDoesNotEnqueueCustomerResourcesInNonAdminPages()
    {
        \WP_Mock::userFunction('is_admin', [
            'return' => false,
        ]);

        \WP_Mock::userFunction('wp_enqueue_style', [
            'times' => 0,
            'arg' => [
                'et_customer_edit_style',
                'http://site.com/admin/css/customer-edit.css',
                array(), '1.0'
            ]
        ]);

        $subject = new Resources();
        $subject->enqueueCustomerEditResources(null);
    }

    /**
     * @test
     */
    public function itDoesNotEnqueueFrontEndScriptOnAdmin()
    {
        \WP_Mock::userFunction('is_admin', [
            'return' => true,
        ]);

        \WP_Mock::userFunction('wp_enqueue_style', [
            'times' => 0,
        ]);

        $subject = new Resources();
        $subject->enqueueCustomerFrontEndFormResources();
    }

    /**
     * @test
     */
    public function itEnqueuesFrontEndScripts()
    {
        \WP_Mock::userFunction('is_admin', [
            'return' => false,
        ]);

        \WP_Mock::userFunction('wp_enqueue_style', [
            'times' => 1,
            'args' => ['etCustomerFrontEndForm', CUSTOMER_COLLECTOR_URL . '/public/css/et-frontend-form.css']
        ]);

        \WP_Mock::userFunction('wp_enqueue_script', [
            'times' => 1,
            'args' => ['etCustomerFrontEndForm', CUSTOMER_COLLECTOR_URL . '/public/js/save-customer-ajax.js', array('jquery'), '1.0', true]
        ]);

        $nonce = 'fdafdasfdsa';

        \WP_Mock::userFunction('wp_create_nonce', [
            'args' => 'et_save_customer',
            'return' => $nonce,
        ]);

        \WP_Mock::userFunction('admin_url', [
            'args' => 'admin-ajax.php',
            'return' => 'admin-ajax.php',
        ]);


        \WP_Mock::userFunction('wp_localize_script', [
            'times' => 1,
            'args' => ['etCustomerFrontEndForm', 'etCustomerFrontEndForm', [
                'nonce' => $nonce,
                'ajaxUrl' => 'admin-ajax.php',
            ]]
        ]);

        $subject = new Resources();
        $subject->enqueueCustomerFrontEndFormResources();
    }

    /**
     * @test
     */
    public function itEnqueuesCustomerResourcesWhenIsAddNewCustomerAdminPage()
    {
        \WP_Mock::userFunction('is_admin', [
            'return' => true,
        ]);

        \WP_Mock::userFunction('wp_enqueue_style', [
            'times' => 1,
            'arg' => [
                'et_customer_edit_style',
                'http://site.com/admin/css/customer-edit.css',
                array(), '1.0'
            ]
        ]);

        $_GET['post_type'] = PostTypeDefinition::POST_TYPE;

        $subject = new Resources();
        $subject->enqueueCustomerEditResources('post-new.php');

        unset($_GET['post_type']);
    }

    /**
     * @test
     */
    public function itEnqueuesCustomerResourcesWhenIsEditCustomerAdminPage()
    {
        \WP_Mock::userFunction('is_admin', [
            'return' => true,
        ]);

        \WP_Mock::userFunction('wp_enqueue_style', [
            'times' => 1,
            'arg' => [
                'et_customer_edit_style',
                'http://site.com/admin/css/customer-edit.css',
                array(), '1.0'
            ]
        ]);

        $_GET['post'] = 1;

        \WP_Mock::userFunction('get_post_type', [
            'arg' => 1,
            'return' => PostTypeDefinition::POST_TYPE
        ]);

        $subject = new Resources();
        $subject->enqueueCustomerEditResources('post.php');

        unset($_GET['post']);
    }

    public function tearDown(): void {
        $this->assertConditionsMet();
        \WP_Mock::tearDown();
    }
}