<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/Exception.php';
require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';

class Email
{
  private $mail; // Make this public for now while we're testing!
  private $content;
  private $altContent;
  private $errorMessage;

  public function __construct()
  {
    $this->mail = new PHPMailer(true);
    $this->mail->isSMTP();
    $this->mail->Host = 'smtp.sparkpostmail.com';
    $this->mail->SMTPAuth = true;
    $this->mail->Username = 'SMTP_Injection';
    $this->mail->Password = $_ENV['SPARKPOST_KEY'];
    $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $this->mail->Port = 587;
    $this->mail->setFrom('transport@obrienware.com', 'Transport');
  }

  public function setSubject($subject)
  {
    $this->mail->Subject = $subject;
  }

  public function setContent($content)
  {
    $this->content = $content;
  }

  public function addRecipient($email, $name)
  {
    $this->mail->addAddress($email, $name);
  }

  public function setAltContent($content)
  {
    $this->altContent = $content;
  }

  public function sendText()
  {
    try {
      $this->mail->Body = $this->content;
      $this->mail->send();
      return true;
    } catch (Exception $e) {
      $this->errorMessage = "Message could not be sent. Mailer Error: {$this->mail->ErrorInfo}";
      return false;
    }
  }

  public function sendHTML()
  {
    try {
      $this->mail->isHTML(true);
      $this->mail->Body = $this->content;
      $this->mail->AltBody = $this->altContent;
      $this->mail->send();
      return true;
    } catch (Exception $e) {
      $this->errorMessage = "Message could not be sent. Mailer Error: {$this->mail->ErrorInfo}";
      return false;
    }
  }
}