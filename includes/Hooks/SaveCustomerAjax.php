<?php

namespace ET\CustomerCollector\Hooks;

use ET\CustomerCollector\Repository\CustomerFactory;
use ET\CustomerCollector\Repository\CustomerRepository;

class SaveCustomerAjax implements IHook
{

    private $customerRepository;
    private $customerFactory;

    public function __construct(
        CustomerRepository $customerRepository,
        CustomerFactory $customerFactory
    ) {
        $this->customerRepository = $customerRepository;
        $this->customerFactory    = $customerFactory;
    }

    public function registerHooks()
    {
        add_action('wp_ajax_et_save_customer', array($this, 'save'));
    }

    public function save()
    {
        if ( ! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'et_save_customer') ) {
            wp_send_json_error();
            return;
        }

        $customer = $this->customerFactory->create();
        $customer->setName(isset($_POST['et-customer-name']) ? sanitize_text_field($_POST['et-customer-name']) : '')
            ->setBudget(isset($_POST['et-customer-budget']) ? sanitize_text_field($_POST['et-customer-budget']) : '')
            ->setEmail(isset($_POST['et-customer-email']) ? sanitize_email($_POST['et-customer-email']) : '')
            ->setMessage(isset($_POST['et-customer-message']) ? sanitize_textarea_field($_POST['et-customer-message']) : '')
            ->setPhone(isset($_POST['et-customer-phone']) ? sanitize_text_field($_POST['et-customer-phone']) : '')
            ->setDate(isset($_POST['et-customer-date']) ? sanitize_text_field($_POST['et-customer-date']) : '');

        $this->customerRepository->save($customer);
        wp_send_json_success();
    }
}