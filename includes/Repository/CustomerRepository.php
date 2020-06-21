<?php

namespace ET\CustomerCollector\Repository;

use ET\CustomerCollector\Hooks\PostTypeDefinition;
use ET\CustomerCollector\Model\Customer;

class CustomerRepository
{
    const PHONE_CF   = 'et-customer-phone';
    const EMAIL_CF   = 'et-customer-email';
    const BUDGET_CF  = 'et-customer-budget';
    const MESSAGE_CF = 'et-customer-message';
    const NAME_FIELD = 'et-customer-name';

    private $customerFactory;

    public function __construct(CustomerFactory $customerFactory)
    {
        $this->customerFactory = $customerFactory;
    }

    public function getById($id)
    {
        $post = get_post($id);

        if ( ! $post) {
            return null;
        }

        $customer = $this->customerFactory->create();
        $customer->setId($id)->setName($post->post_title)
            ->setBudget(get_post_meta($id, self::BUDGET_CF, true))
            ->setEmail(get_post_meta($id, self::EMAIL_CF, true))
            ->setMessage(get_post_meta($id, self::MESSAGE_CF, true))
            ->setPhone(get_post_meta($id, self::PHONE_CF, true));

        return $customer;
    }

    public function save(Customer $customer)
    {
        if ($customer->getId()) {
            wp_update_post(array(
                'ID' => $customer->getId(),
                'post_status' => 'private',
            ));
        } else {
            $args = [
                'post_title'  => $customer->getName(),
                'post_type'   => PostTypeDefinition::POST_TYPE,
                'post_status' => 'private',
            ];

            if($customer->getDate()) {
                $args['post_date'] = date('Y-m-d H:i:s', strtotime($customer->getDate()));
            }

            $id = wp_insert_post($args);
            $customer->setId($id);
        }

        $fields = [
            self::EMAIL_CF   => sanitize_email($customer->getEmail()),
            self::BUDGET_CF  => sanitize_text_field($customer->getBudget()),
            self::MESSAGE_CF => sanitize_textarea_field($customer->getMessage()),
            self::PHONE_CF   => sanitize_text_field($customer->getPhone()),
        ];

        foreach ($fields as $key => $val) {
            update_post_meta($customer->getId(), $key, $val);
        }
    }
}