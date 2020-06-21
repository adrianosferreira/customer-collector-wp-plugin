<?php

namespace ET\CustomerCollector\Hooks;

use ET\CustomerCollector\Repository\CustomerRepository;
use Twig\Environment;

class CustomFieldsMetaBox implements IHook
{

    private $template;
    private $customerRepository;

    public function __construct(Environment $template, CustomerRepository $customerRepository)
    {
        $this->template = $template;
        $this->customerRepository = $customerRepository;
    }

    public function registerHooks()
    {
        add_action('add_meta_boxes', array($this, 'register'));
        add_action('save_post', array($this, 'save'));
    }

    public function register()
    {
        add_meta_box(
            'et_custom_data',
            __('Customer Information', CUSTOMER_COLLECTOR_TEXTDOMAIN),
            array($this, 'html'), PostTypeDefinition::POST_TYPE
        );
    }

    public function html($post)
    {
        $customer = $this->customerRepository->getById($post->ID);
        echo $this->template->render('meta-box.twig', [
            'email' => $customer->getEmail(),
            'phone' => $customer->getPhone(),
            'budget' => $customer->getBudget(),
            'message' => $customer->getMessage(),
        ]);
    }

    public function save($postId)
    {
        if (array_key_exists('et-customer-data', $_POST)) {
            $this->customerRepository->save($postId);
        }
    }
}