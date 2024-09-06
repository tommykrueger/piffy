<?php

namespace Piffy\Plugins\Newsletter\Models;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Email
{

    protected $recipient;

    protected $emailTemplate;

    protected $emailData;

    protected $subject;

    public function __construct($data)
    {
        if (isset($data->recipient)) {
            $this->setRecipient($data->recipient);
        }
        if (isset($data->emailTemplate)) {
            $this->setEmailTemplate($data->emailTemplate);
        }
        if (isset($data->emailData)) {
            $this->setEmailData($data->emailData);
        }
        if (isset($data->subject)) {
            $this->setSubject($data->subject);
        }
    }

    public function setRecipient($recipient)
    {
        $this->recipient = $recipient;
    }

    public function setEmailTemplate($emailTemplate)
    {
        $this->emailTemplate = $emailTemplate;
    }

    public function setEmailData($emailData)
    {
        $this->emailData = $emailData;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    public function send()
    {
        require_once PLUGINS_DIR . '/Newsletter/Lib/PHPMailer/src/Exception.php';
        require_once PLUGINS_DIR . '/Newsletter/Lib/PHPMailer/src/PHPMailer.php';
        require_once PLUGINS_DIR . '/Newsletter/Lib/PHPMailer/src/SMTP.php';

        $mail = new PHPMailer(true);

        try {
            //Server settings
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host = SMTP_HOST;                    // Set the SMTP server to send through
            $mail->SMTPAuth = SMTP_AUTH;                                   // Enable SMTP authentication
            $mail->Username = SMTP_USERNAME;                     // SMTP username
            $mail->Password = SMTP_PASSWORD;                               // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port = SMTP_Port;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
            $mail->CharSet = 'utf-8';
            $mail->SetLanguage("de");

            //Recipients
            $mail->setFrom('noreply@lachvegas.de', 'LachVegas.de');
            $mail->addAddress($this->recipient);     // Add a recipient
            //$mail->addAddress('info@lustige-sprueche-und-witze.de');               // Name is optional
            $mail->addReplyTo('noreply@lachvegas.de', 'LachVegas');

            // Attachments
            //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
            //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $this->subject;

            $emailData = $this->emailData;

            ob_start();
            include(APP_DIR . '/views/email/' . $this->emailTemplate . '.php');
            $data = ob_get_clean();
            $mail->Body = $data;

            return $mail->send();

        } catch (Exception $e) {
            var_dump($e->getMessage());
            exit;
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

        return false;
    }
}