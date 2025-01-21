<?php
declare(strict_types=1);
namespace Transport;

require_once __DIR__.'/../../autoload.php';

class Template 
{
  private string $template;
  private array $data;

  public function __construct(string $template) 
  {
    $this->template = $template;
  }

  public function render(array $data): string 
  {
    $this->data = $data;
    return $this->parseTemplate($this->template);
  }

  private function parseTemplate(string $template): string 
  {
    $pattern = '/{{(.*?)}}/';
    return preg_replace_callback($pattern, array($this, 'replaceVariable'), $template);
  }

  private function replaceVariable($matches): string
  {
    $variable = $matches[1];
    $parts = explode('.', $variable);
    $value = $this->data;
    foreach ($parts as $part) {
      if (!isset($value[$part])) return '';
      $value = $value[$part];
    }
    return $value;
  }
}