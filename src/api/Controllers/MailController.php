<?php

namespace api\Controllers;

use Exception;
use League\OAuth2\Client\Provider\Google;
use PHPMailer\PHPMailer\OAuth;
use PHPMailer\PHPMailer\PHPMailer;
use Twig_Environment;
use Twig_Loader_Filesystem;

class MailController
{
    const DEFAULT_GOOGLE_HOST = 'smtp.gmail.com';
    const DEFAULT_GOOGLE_PORT = 587;
    const DEFAULT_HTML_UNSUPPORTED_MESSAGE = 'Please enable the HTML view to be able to view this document.';
    const TLS_ENCRYPTION = 'tls';
    const AUTH_TYPE_XOAUTH2 = 'XOAUTH2';
    const ENCODING_UTF8 = 'utf-8';

    const SENDER_MAIL_ADDRESS = 'supp.tool@gmail.com';
    const SENDER_NAME = 'SupportTool Notification';

    private $config;

    private $mail;

    private $email;

    private $twig;

    public function __construct()
    {
        $configController = new ConfigController();
        $this->config = $configController->getConfig()['email-config'];
        $this->mail = new PHPMailer();

        $loader = new Twig_Loader_Filesystem(__DIR__ . '/../MailTemplates/');
        $this->twig = new Twig_Environment($loader);
    }

    public function addAddress($toAddress, $toName)
    {
        $this->mail->addAddress($toAddress, $toName);
    }

    /**
     * @param string $subject
     * @param string $templateName
     * @param array $data
     * @return bool
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function sendMail($subject, $templateName, $data = [])
    {
        $this->setDefaultConfiguration();
        $this->mail->setFrom($this->email, self::SENDER_NAME);

        $this->mail->Subject = $subject;

        try {
            $template = $this->twig->load($templateName);
            $mailBody = $template->render($data);
        } catch (Exception $e) {
            return false;
        }

        $this->mail->CharSet = self::ENCODING_UTF8;
        $this->mail->msgHTML($mailBody);

        $this->mail->AltBody = self::DEFAULT_HTML_UNSUPPORTED_MESSAGE;

        if (!$this->mail->send()) {
            $this->mail->ClearAddresses();
            return false;
        } else {
            $this->mail->ClearAddresses();
            return true;
        }
    }

    private function setDefaultConfiguration() {
        $this->mail->isSMTP();
        $this->mail->SMTPDebug = 0;
        $this->mail->Host = self::DEFAULT_GOOGLE_HOST;
        $this->mail->Port = self::DEFAULT_GOOGLE_PORT;
        $this->mail->SMTPSecure = self::TLS_ENCRYPTION;
        $this->mail->SMTPAuth = true;
        $this->mail->AuthType = self::AUTH_TYPE_XOAUTH2;
        $this->mail->Priority = 2;

        $this->email = self::SENDER_MAIL_ADDRESS;
        $clientId = $this->config['client-id'];
        $clientSecret = $this->config['client-secret'];
        $refreshToken = $this->config['refresh-token'];

        $provider = new Google([
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
        ]);

        $this->mail->setOAuth(new OAuth([
            'provider' => $provider,
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
            'refreshToken' => $refreshToken,
            'userName' => $this->email,
        ]));
    }
}
