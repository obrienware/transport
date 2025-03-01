<?php
declare(strict_types=1);

namespace Transport;

trait Log
{
  public static function log(mixed $message): void
  {
    $out = fopen('php://stdout', 'w'); //output handler
    $output = json_encode($message, JSON_PRETTY_PRINT);
    fputs($out, $output.PHP_EOL); //writing output operation
    fclose($out); //closing handler
  }
}