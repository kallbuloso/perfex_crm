<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Sms_twilio extends App_sms
{
    // Account SID from twilio.com/console
    private $sid;

    // Auth Token from twilio.com/console
    private $token;

    // Twilio Phone Number
    private $phone;

    // Alphanumeric Sender ID
    private $senderId;

    public function __construct()
    {
        parent::__construct();

        $this->sid      = $this->get_option('twilio', 'account_sid');
        $this->token    = $this->get_option('twilio', 'auth_token');
        $this->phone    = $this->get_option('twilio', 'phone_number');
        $this->senderId = $this->get_option('twilio', 'sender_id');

        $this->add_gateway('twilio', [
            'name'    => 'Twilio',
            'info'    => '<p>A integração do Twilio SMS é uma mensagem unilateral, o que significa que seus clientes não poderão responder ao SMS. Os números de telefone devem estar no formato <a href="https://www.twilio.com/docs/glossary/what-e164" target="_blank">E.164</a>. Clique <a href="https://support.twilio.com/hc/en-us/articles/223183008-Formatting-International-Phone-Numbers" target="_blank">aqui</a> para saber mais como usar o telefone os números devem ser formatados.</p><hr class="hr-10" />',
            // 'info'    => '<p>Twilio SMS integration is one way messaging, means that your customers won\'t be able to reply to the SMS. Phone numbers must be in format <a href="https://www.twilio.com/docs/glossary/what-e164" target="_blank">E.164</a>. Click <a href="https://support.twilio.com/hc/en-us/articles/223183008-Formatting-International-Phone-Numbers" target="_blank">here</a> to read more how phone numbers should be formatted.</p><hr class="hr-10" />',
            'options' => [
                [
                    'name'  => 'account_sid',
                    'label' => 'Account SID',
                ],
                [
                    'name'  => 'auth_token',
                    'label' => 'Auth Token',
                ],
                [
                    'name'  => 'phone_number',
                    'label' => 'Twilio Phone Number',
                ],
                [
                    'name'     => 'sender_id',
                    'label'    => 'Alphanumeric Sender ID',
                    'info' => '<p><a href="https://www.twilio.com/blog/personalize-sms-alphanumeric-sender-id" target="_blank">https://www.twilio.com/blog/personalize-sms-alphanumeric-sender-id</a></p>',
                ],
            ],
        ]);
    }

    public function send($number, $message)
    {
        try {
            $client = new Twilio\Rest\Client($this->sid, $this->token);
        } catch (Exception $e) {
            $this->set_error($e->getMessage(), false);

            return false;
        }

        try {
            $client->messages->create(
                // The number to send the SMS
                $number,
                [
                    'from' => $this->senderId ?: $this->phone,
                    'body' => $message,
                ]
            );

            $this->logSuccess($number, $message);
        } catch (Exception $e) {
            $this->set_error($e->getMessage());

            return false;
        }

        return true;
    }
}
