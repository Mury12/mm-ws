<?php

namespace MMWS\Service;

/**
 * Do the emailing service using Mailgun's keys and CURL
 * @param Array $data is the indexed array with 'name', 'target', 'msg' and 'subject' properties
 * 
 * -------------
 * 
 * Example Usage:
 * 
 * use MMWS\Service\EmailService;
 * 
 * $es = new EmailService(['name' => 'Garry', 'target' => 'example@mail.com', 'msg' => 'How are you, Jon?', 'subject' => 'Hey there!']);
 * 
 * $es->send();
 * 
 * -------------
 * @package MMWS
 * @author Andre Mury <mury_gh@hotmail.com>
 * @version MMWS^0.1.0-alpha
 */
class EmailService
{

    public $email = DEFAULT_NOREPLY_EMAIL;
    public $msg;
    public $nome;
    public $target;
    public $subject;
    public $mailGun = array(
        'domain' => DEFAULT_MAILGUN_URL,
        'apiKey' => DEFAULT_MAILGUN_KEY,
        'defPwd' => DEFAULT_MAILGUN_PWD,
        'smtpLn' => 'postmaster@mg.moneyright.com.br',
        'smtpHn' => 'smtp.mailgun.org',
    );

    public function __construct($data)
    {
        if (\is_array($data)) {
            if (\array_key_exists('name', $data)) {
                $this->nome     = $data['name'];
            }
            if (\array_key_exists('target', $data)) {
                $this->target   = $data['target'];
            }
            if (\array_key_exists('msg', $data)) {
                $this->msg      = $data['msg'];
            }
            if (\array_key_exists('subject', $data)) {
                $this->subject  = $data['subject'];
            }
        }
    }

    /**
     * Sends the email with CURL method
     * 
     * @return Bool the request result
     */
    public function send()
    {
        $message = array();
        $message['from'] = "Money Right <" . $this->email . ">";
        $message['to'] = $this->target;
        $message['h:Reply-To'] = "<No Reply>";
        $message['subject'] = $this->subject;
        $message['html'] = $this->msg;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->mailGun['domain']);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, "api:" . $this->mailGun['apiKey']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $message);
        $result = curl_exec($curl);
        curl_close($curl);
        if ($result != 'Forbidden')
            return true;
        return false;
    }
}
