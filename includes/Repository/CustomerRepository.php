<?php

namespace ET\CustomerCollector\Repository;

class CustomerRepository
{
    const PHONE_CF   = 'et-customer-phone';
    const EMAIL_CF   = 'et-customer-email';
    const BUDGET_CF  = 'et-customer-budget';
    const MESSAGE_CF = 'et-customer-message';

    private $customerFactory;

    public function __construct(CustomerFactory $customerFactory)
    {
        $this->customerFactory = $customerFactory;
    }

    public function getById($id)
    {
        $post = get_post($id);

        if ( ! $post) {
            throw new \RuntimeException('Customer not found with ID ' . $id);
        }

        $customer = $this->customerFactory->create();
        $customer->setId($id)->setName($post->post_title)
            ->setBudget(get_post_meta($id, self::BUDGET_CF, true))
            ->setEmail(get_post_meta($id, self::EMAIL_CF, true))
            ->setMessage(get_post_meta($id, self::MESSAGE_CF, true))
            ->setPhone(get_post_meta($id, self::PHONE_CF, true));

        return $customer;
    }

    public function save($id)
    {
        $fields = [
            self::EMAIL_CF   => sanitize_email($_POST['et-customer-email']),
            self::BUDGET_CF  => sanitize_text_field($_POST['et-customer-budget']),
            self::MESSAGE_CF => sanitize_textarea_field($_POST['et-customer-message']),
            self::PHONE_CF   => sanitize_text_field($_POST['et-customer-phone']),
        ];

        foreach ($fields as $key => $val) {
            update_post_meta($id, $key, $val);
        }
    }
}