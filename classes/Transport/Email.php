<?php
declare(strict_types=1);
namespace Transport;

use PHPMailer\PHPMailer\PHPMailer;
require 'phpmailer/Exception.php';
require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';

require_once __DIR__.'/../../autoload.php';

class Email extends PHPMailer
{
  private $content;
  private $altContent;
  private $errorMessage;

  public function __construct()
  {
    parent::__construct(true);
    $keys = Config::get('system')->keys;

    $config = Config::get('organization');
    $this->isSMTP();
    $this->Host = 'smtp.sparkpostmail.com';
    $this->SMTPAuth = true;
    $this->Username = 'SMTP_Injection';
    $this->Password = $keys->SPARKPOST_KEY;
    $this->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $this->Port = 587;
    $this->setFrom($config->email->fromEmailAddress, $config->email->fromEmailName);
  }

  public function setSubject($subject)
  {
    $this->Subject = $subject;
  }

  public function setContent($content)
  {
    $this->Body = $content;
  }

  public function addRecipient($email, $name = '')
  {
    $this->addAddress($email, $name);
  }

  public function setAltContent($content)
  {
    $this->altContent = $content;
  }

  public function sendText()
  {
    try {
      $this->send();
      return true;
    } catch (\Exception $e) {
      $this->errorMessage = "Message could not be sent. Mailer Error: {$this->ErrorInfo}";
      return false;
    }
  }

  public function sendHTML()
  {
    try {
      $this->isHTML(true);
      $this->AltBody = $this->altContent;
      $this->send();
      return true;
    } catch (\Exception $e) {
      $this->errorMessage = "Message could not be sent. Mailer Error: {$this->ErrorInfo}";
      return false;
    }
  }
}