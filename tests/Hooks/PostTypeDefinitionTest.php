<?php

namespace ET\CustomerCollector\Tests\Hooks;

use ET\CustomerCollector\Hooks\PostTypeDefinition;
use WP_Mock\Tools\TestCase;

class PostTypeDefinitionTest extends TestCase
{
    public function setUp(): void {
        \WP_Mock::setUp();
    }

    /**
     * @test
     */
    public function itRegisterPostType()
    {
        $labels = array(
            'name'               => __('Customers', CUSTOMER_COLLECTOR_TEXTDOMAIN),
            'singular_name'      => __('Customers', CUSTOMER_COLLECTOR_TEXTDOMAIN),
            'menu_name'          => __('Customers', CUSTOMER_COLLECTOR_TEXTDOMAIN),
            'name_admin_bar'     => __('Customer', CUSTOMER_COLLECTOR_TEXTDOMAIN),
            'add_new'            => __('Add New', CUSTOMER_COLLECTOR_TEXTDOMAIN),
            'add_new_item'       => __('Add New Customer', CUSTOMER_COLLECTOR_TEXTDOMAIN),
            'new_item'           => __('New Customer', CUSTOMER_COLLECTOR_TEXTDOMAIN),
            'edit_item'          => __('Edit Customer', CUSTOMER_COLLECTOR_TEXTDOMAIN),
            'view_item'          => __('View Customer', CUSTOMER_COLLECTOR_TEXTDOMAIN),
            'all_items'          => __('All Customers', CUSTOMER_COLLECTOR_TEXTDOMAIN),
            'search_items'       => __('Search Customers', CUSTOMER_COLLECTOR_TEXTDOMAIN),
            'parent_item_colon'  => __('Parent Customer:', CUSTOMER_COLLECTOR_TEXTDOMAIN),
            'not_found'          => __('No customers found.', CUSTOMER_COLLECTOR_TEXTDOMAIN),
            'not_found_in_trash' => __('No customers found in Trash.', CUSTOMER_COLLECTOR_TEXTDOMAIN)
        );

        $args = array(
            'labels'             => $labels,
            'description'        => __('Description.', CUSTOMER_COLLECTOR_TEXTDOMAIN),
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => false,
            'rewrite'            => array('slug' => 'et-customer'),
            'taxonomies'         => array('category', 'post_tag'),
            'capabilities' => array(
                'edit_post'          => 'update_core',
                'read_post'          => 'update_core',
                'delete_post'        => 'update_core',
                'edit_posts'         => 'update_core',
                'edit_others_posts'  => 'update_core',
                'delete_posts'       => 'update_core',
                'publish_posts'      => 'update_core',
                'read_private_posts' => 'update_core'
            ),
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array('title')
        );

        \WP_Mock::userFunction('register_post_type', [
            'times' => 1,
            'args' => [
                'et-customer',
                $args
            ]
        ]);

        $subject = new PostTypeDefinition();
        $subject->register();
    }

    /**
     * @test
     */
    public function itRegistersHooks()
    {
        $subject = new PostTypeDefinition();
        \WP_Mock::expectActionAdded('init', array($subject, 'register'));
        $subject->registerHooks();
    }

    public function tearDown(): void {
        $this->assertConditionsMet();
        \WP_Mock::tearDown();
    }
}
