<?php

class Template 
{
  private $template;
  private $data;

  public function __construct($template) {
    $this->template = $template;
  }

  public function render($data) {
    $this->data = $data;
    return $this->parseTemplate($this->template);
  }

  private function parseTemplate($template) {
    $pattern = '/{{(.*?)}}/';
    return preg_replace_callback($pattern, array($this, 'replaceVariable'), $template);
  }

  private function replaceVariable($matches) {
    $variable = $matches[1];
    $parts = explode('.', $variable);
    $value = $this->data;
    foreach ($parts as $part) {
      if (isset($value[$part])) {
        $value = $value[$part];
      } else {
        return '';
      }
    }
    return $value;
  }
}