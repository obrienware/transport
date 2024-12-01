<?php
require_once 'class.data.php';
if (!$db) $db = new data();

function stepIndicator ($stepsCompleted, $numberOfSteps) {
  $output = '<div class="d-flex justify-content-center align-items-center">';
  $output .= '<div class="progresses">';
  for ($i = 1; $i <= $numberOfSteps; $i++) {
    if ($i <= $stepsCompleted) {
      $output .= '<div class="steps complete">';
      $output .= '<span><i class="fa fa-check"></i></span>';
      $output .= '</div>';
    } else {
      $output .= '<div class="steps">';
      $output .= '<span class="font-weight-bold">'.$i.'</span>';
      $output .= '</div>';

    }
    if ($i < $numberOfSteps) {
      if ($i < $stepsCompleted) {
        $output .= '<span class="line complete"></span>';
      } else {
        $output .= '<span class="line"></span>';
      }
    }
  }
  $output .= '</div>';
  $output .= '</div>';
  return $output;
}