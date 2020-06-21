<?php

namespace ET\CustomerCollector\Tests\Hooks;

use ET\CustomerCollector\Hooks\Resources;
use ET\CustomerCollector\Hooks\Shortcode;
use ET\CustomerCollector\Repository\CustomerRepository;
use Twig\Environment;
use WP_Mock\Tools\TestCase;

class ShortcodeTest extends TestCase
{

    public function setUp(): void {
        \WP_Mock::setUp();
    }

    /**
     * @test
     */
    public function itRegisterHooks() {
        $template = $this->getMockBuilder(Environment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $subject = new Shortcode($template);
        \WP_Mock::expectActionAdded('init', array($subject, 'add'));
        $subject->registerHooks();
    }

    /**
     * @test
     */
    public function itAddsShortcodes() {
        $template = $this->getMockBuilder(Environment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $subject = new Shortcode($template);

        \WP_Mock::userFunction('add_shortcode', [
            'times' => 1,
            'args' => [
                'et_customer_frontend_form', array($subject, 'render')
            ],
        ]);

        $subject->add();
    }

    /**
     * @test
     */
    public function itRendersHTML() {
        $template = $this->getMockBuilder(Environment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $response = [
            'body' => json_encode([
                'datetime' => '2020-06-06 00:00:00',
            ])
        ];

        \WP_Mock::userFunction('wp_remote_get', [
            'return' => $response,
            'args' => 'http://worldtimeapi.org/api/timezone/America/Sao_Paulo',
        ]);

        \WP_Mock::userFunction('is_wp_error', [
            'return' => false,
        ]);

        $subject = new Shortcode($template);

        $model = [
            'fields'   => [
                'name'    => [
                    'label'      => isset($atts['name_label']) ? $atts['name_label'] : __('Your Name', CUSTOMER_COLLECTOR_TEXTDOMAIN),
                    'max_length' => isset($atts['name_max_length']) ? $atts['name_max_length'] : 50,
                    'name'       => CustomerRepository::NAME_FIELD,
                    'type'       => 'input',
                    'required'   => true,
                ],
                'email'   => [
                    'label'      => isset($atts['email_label']) ? $atts['email_label'] : 'Your Email',
                    'max_length' => isset($atts['email_max_length']) ? $atts['email_max_length'] : 50,
                    'name'       => CustomerRepository::EMAIL_CF,
                    'type'       => 'email',
                    'required'   => true,
                ],
                'phone'   => [
                    'label'      => isset($atts['phone_label']) ? $atts['phone_label'] : 'Your Phone',
                    'max_length' => isset($atts['phone_max_length']) ? $atts['phone_max_length'] : 50,
                    'name'       => CustomerRepository::PHONE_CF,
                    'type'       => 'tel',
                    'required'   => false,
                ],
                'budget'  => [
                    'label'      => isset($atts['budget_label']) ? $atts['budget_label'] : 'Your Budget',
                    'max_length' => isset($atts['budget_max_length']) ? $atts['budget_max_length'] : 50,
                    'name'       => CustomerRepository::BUDGET_CF,
                    'type'       => 'number',
                    'required'   => false,
                ],
                'message' => [
                    'label'      => isset($atts['message_label']) ? $atts['message_label'] : 'Your Message',
                    'max_length' => isset($atts['message_max_length']) ? $atts['message_max_length'] : 50,
                    'type'       => 'textarea',
                    'rows'       => isset($atts['message_rows']) ? $atts['message_rows'] : 15,
                    'cols'       => isset($atts['message_cols']) ? $atts['message_cols'] : 95,
                    'name'       => CustomerRepository::MESSAGE_CF,
                    'required'   => false,
                ],
            ],
            'submit'   => 'Sent Message',
            'info'     => 'Wait, message is being sent...',
            'error'    => 'Error! We could not send your message.',
            'success'  => 'Your message was successfully delivered!',
            'required' => 'This field is required',
            'invalid'  => 'This field is invalid',
            'postDate' => '2020-06-06 00:00:00',
        ];

        $output = '';

        $template->method('render')
            ->with('shortcode.twig', $model)
            ->willReturn($output);

        $expected = $output;

        $this->assertEquals($expected, $subject->render([], ''));
    }

    /**
     * @test
     */
    public function itRendersHTMLWithFallbackEmptyDate() {
        $template = $this->getMockBuilder(Environment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $response = [
            'body' => json_encode([
                'datetime' => '2020-06-06 00:00:00',
            ])
        ];

        \WP_Mock::userFunction('wp_remote_get', [
            'return' => $response,
            'args' => 'http://worldtimeapi.org/api/timezone/America/Sao_Paulo',
        ]);

        \WP_Mock::userFunction('is_wp_error', [
            'return' => true,
        ]);

        $subject = new Shortcode($template);

        $model = [
            'fields'   => [
                'name'    => [
                    'label'      => isset($atts['name_label']) ? $atts['name_label'] : __('Your Name', CUSTOMER_COLLECTOR_TEXTDOMAIN),
                    'max_length' => isset($atts['name_max_length']) ? $atts['name_max_length'] : 50,
                    'name'       => CustomerRepository::NAME_FIELD,
                    'type'       => 'input',
                    'required'   => true,
                ],
                'email'   => [
                    'label'      => isset($atts['email_label']) ? $atts['email_label'] : 'Your Email',
                    'max_length' => isset($atts['email_max_length']) ? $atts['email_max_length'] : 50,
                    'name'       => CustomerRepository::EMAIL_CF,
                    'type'       => 'email',
                    'required'   => true,
                ],
                'phone'   => [
                    'label'      => isset($atts['phone_label']) ? $atts['phone_label'] : 'Your Phone',
                    'max_length' => isset($atts['phone_max_length']) ? $atts['phone_max_length'] : 50,
                    'name'       => CustomerRepository::PHONE_CF,
                    'type'       => 'tel',
                    'required'   => false,
                ],
                'budget'  => [
                    'label'      => isset($atts['budget_label']) ? $atts['budget_label'] : 'Your Budget',
                    'max_length' => isset($atts['budget_max_length']) ? $atts['budget_max_length'] : 50,
                    'name'       => CustomerRepository::BUDGET_CF,
                    'type'       => 'number',
                    'required'   => false,
                ],
                'message' => [
                    'label'      => isset($atts['message_label']) ? $atts['message_label'] : 'Your Message',
                    'max_length' => isset($atts['message_max_length']) ? $atts['message_max_length'] : 50,
                    'type'       => 'textarea',
                    'rows'       => isset($atts['message_rows']) ? $atts['message_rows'] : 15,
                    'cols'       => isset($atts['message_cols']) ? $atts['message_cols'] : 95,
                    'name'       => CustomerRepository::MESSAGE_CF,
                    'required'   => false,
                ],
            ],
            'submit'   => 'Sent Message',
            'info'     => 'Wait, message is being sent...',
            'error'    => 'Error! We could not send your message.',
            'success'  => 'Your message was successfully delivered!',
            'required' => 'This field is required',
            'invalid'  => 'This field is invalid',
            'postDate' => '',
        ];

        $output = '';

        $template->method('render')
            ->with('shortcode.twig', $model)
            ->willReturn($output);

        $expected = $output;

        $this->assertEquals($expected, $subject->render([], ''));
    }

    public function tearDown(): void {
        $this->assertConditionsMet();
        \WP_Mock::tearDown();
    }
}