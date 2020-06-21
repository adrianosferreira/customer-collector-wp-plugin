<?php

namespace ET\CustomerCollector\Hooks;

class Resources implements IHook
{
    public function registerHooks()
    {
        add_action('admin_enqueue_scripts', array($this, 'enqueueCustomerEditResources'));
        add_action('wp_enqueue_scripts', array($this, 'enqueueCustomerFrontEndFormResources'));
    }

    public function enqueueCustomerFrontEndFormResources()
    {
        if(is_admin()) {
            return;
        }

        wp_enqueue_style( 'etCustomerFrontEndForm', CUSTOMER_COLLECTOR_URL . '/public/css/et-frontend-form.css');
        wp_enqueue_script( 'etCustomerFrontEndForm', CUSTOMER_COLLECTOR_URL . '/public/js/save-customer-ajax.js', array('jquery'), '1.0', true );
        wp_localize_script( 'etCustomerFrontEndForm', 'etCustomerFrontEndForm', [
            'nonce' => wp_create_nonce('et_save_customer'),
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        ]);
    }

    public function enqueueCustomerEditResources($screen)
    {
        if (is_admin() && $this->isOnCustomersPage($screen)) {
            wp_enqueue_style('et_customer_edit_style',
                CUSTOMER_COLLECTOR_URL . '/admin/css/customer-edit.css',
                array(), '1.0');
        }
    }

    private function isOnCustomersPage($screen)
    {
        return ($screen === 'post-new.php'
                && array_key_exists('post_type', $_GET)
                && $_GET['post_type'] === PostTypeDefinition::POST_TYPE)
            || ($screen === 'post.php' && array_key_exists('post', $_GET)
                && get_post_type($_GET['post'])
                === PostTypeDefinition::POST_TYPE);
    }
}