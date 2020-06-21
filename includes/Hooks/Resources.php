<?php

namespace ET\CustomerCollector\Hooks;

class Resources implements IHook
{
    public function registerHooks()
    {
        add_action( 'admin_enqueue_scripts', array($this, 'enqueueCustomerEditResources') );
    }

    public function enqueueCustomerEditResources($screen)
    {
        if (is_admin() && $this->isOnCustomersPage($screen)) {
            wp_enqueue_style( 'et_customer_edit_style', CUSTOMER_COLLECTOR_URL . '/admin/css/customer-edit.css', array(), '1.0' );
        }
    }

    private function isOnCustomersPage($screen) {
       return ($screen === 'post-new.php' && array_key_exists('post_type', $_GET) && $_GET['post_type'] === PostTypeDefinition::POST_TYPE) ||
           ($screen === 'post.php' && array_key_exists('post', $_GET) && get_post_type($_GET['post']) === PostTypeDefinition::POST_TYPE);
    }
}