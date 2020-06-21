<?php

namespace ET\CustomerCollector\Hooks;

use ET\CustomerCollector\Repository\CustomerFactory;
use ET\CustomerCollector\Repository\CustomerRepository;
use Twig\Environment;

class CustomFieldsMetaBox implements IHook
{

    private $template;
    private $customerRepository;
    private $customerFactory;

    public function __construct(
        Environment $template,
        CustomerRepository $customerRepository,
        CustomerFactory $customerFactory
    ) {
        $this->template           = $template;
        $this->customerRepository = $customerRepository;
        $this->customerFactory    = $customerFactory;
    }

    public function registerHooks()
    {
        add_action('add_meta_boxes', array($this, 'register'));
        add_action('save_post', array($this, 'save'));
    }

    public function register()
    {
        add_meta_box('et_custom_data',
            __('Customer Information', CUSTOMER_COLLECTOR_TEXTDOMAIN),
            array($this, 'html'), PostTypeDefinition::POST_TYPE);
    }

    public function html(\WP_Post $post)
    {
        $customer = $this->customerRepository->getById($post->ID);

        if ( ! $customer) {
            echo $this->template->render('meta-box.twig', []);
            return;
        }

        echo $this->template->render('meta-box.twig', [
            'email'   => $customer->getEmail(),
            'phone'   => $customer->getPhone(),
            'budget'  => $customer->getBudget(),
            'message' => $customer->getMessage(),
        ]);
    }

    public function save($postId)
    {
        if (array_key_exists('et-customer-data', $_POST)) {
            remove_action('save_post', array($this, 'save'));

            $customer = $this->customerFactory->create();
            $customer->setId($postId)
                ->setName(isset($_POST['et-customer-name']) ? sanitize_text_field($_POST['et-customer-name']) : '')
                ->setBudget(isset($_POST['et-customer-budget']) ? sanitize_text_field($_POST['et-customer-budget']) : '')
                ->setEmail(isset($_POST['et-customer-email']) ? sanitize_email($_POST['et-customer-email']) : '')
                ->setMessage(isset($_POST['et-customer-message']) ? sanitize_textarea_field($_POST['et-customer-message']) : '')
                ->setPhone(isset($_POST['et-customer-phone']) ? sanitize_text_field($_POST['et-customer-phone']) : '');

            $this->customerRepository->save($customer);
            add_action('save_post', array($this, 'save'));
        }
    }
}