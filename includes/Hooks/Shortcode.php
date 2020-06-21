<?php

namespace ET\CustomerCollector\Hooks;

use ET\CustomerCollector\Repository\CustomerRepository;
use Twig\Environment;

class Shortcode implements IHook
{

    private $template;

    public function __construct(Environment $template)
    {
        $this->template = $template;
    }

    public function registerHooks()
    {
        add_action('init', array($this, 'add'));
    }

    public function add()
    {
        add_shortcode('et_customer_frontend_form', array($this, 'render'));
    }

    public function render($atts)
    {
        $postDate = wp_remote_get('http://worldtimeapi.org/api/timezone/America/Sao_Paulo');

        if ( is_wp_error($postDate) ) {
            $postDate = '';
        } else {
            $postDate = json_decode($postDate['body'], true);
            $postDate = $postDate['datetime'];
        }

        return $this->template->render('shortcode.twig', [
            'fields'   => [
                'name'    => [
                    'label'      => isset($atts['name_label']) ? $atts['name_label'] : __('Your Name', CUSTOMER_COLLECTOR_TEXTDOMAIN),
                    'max_length' => isset($atts['name_max_length']) ? $atts['name_max_length'] : 50,
                    'name'       => CustomerRepository::NAME_FIELD,
                    'type'       => 'input',
                    'required'   => true,
                ],
                'email'   => [
                    'label'      => isset($atts['email_label']) ? $atts['email_label'] : __('Your Email', CUSTOMER_COLLECTOR_TEXTDOMAIN),
                    'max_length' => isset($atts['email_max_length']) ? $atts['email_max_length'] : 50,
                    'name'       => CustomerRepository::EMAIL_CF,
                    'type'       => 'email',
                    'required'   => true,
                ],
                'phone'   => [
                    'label'      => isset($atts['phone_label']) ? $atts['phone_label'] : __('Your Phone', CUSTOMER_COLLECTOR_TEXTDOMAIN),
                    'max_length' => isset($atts['phone_max_length']) ? $atts['phone_max_length'] : 50,
                    'name'       => CustomerRepository::PHONE_CF,
                    'type'       => 'tel',
                    'required'   => false,
                ],
                'budget'  => [
                    'label'      => isset($atts['budget_label']) ? $atts['budget_label'] : __('Your Budget', CUSTOMER_COLLECTOR_TEXTDOMAIN),
                    'max_length' => isset($atts['budget_max_length']) ? $atts['budget_max_length'] : 50,
                    'name'       => CustomerRepository::BUDGET_CF,
                    'type'       => 'number',
                    'required'   => false,
                ],
                'message' => [
                    'label'      => isset($atts['message_label']) ? $atts['message_label'] : __('Your Message', CUSTOMER_COLLECTOR_TEXTDOMAIN),
                    'max_length' => isset($atts['message_max_length']) ? $atts['message_max_length'] : 50,
                    'type'       => 'textarea',
                    'rows'       => isset($atts['message_rows']) ? $atts['message_rows'] : 15,
                    'cols'       => isset($atts['message_cols']) ? $atts['message_cols'] : 95,
                    'name'       => CustomerRepository::MESSAGE_CF,
                    'required'   => false,
                ],
            ],
            'submit'   => __('Sent Message', CUSTOMER_COLLECTOR_TEXTDOMAIN),
            'info'     => __('Wait, message is being sent...', CUSTOMER_COLLECTOR_TEXTDOMAIN),
            'error'    => __('Error! We could not send your message.', CUSTOMER_COLLECTOR_TEXTDOMAIN),
            'success'  => __('Your message was successfully delivered!', CUSTOMER_COLLECTOR_TEXTDOMAIN),
            'required' => __('This field is required', CUSTOMER_COLLECTOR_TEXTDOMAIN),
            'invalid'  => __('This field is invalid', CUSTOMER_COLLECTOR_TEXTDOMAIN),
            'postDate' => $postDate,
        ]);
    }
}