<?php
date_default_timezone_set('America/Denver');

function showDate($date) {
  $baseline = Date('Y-m-d', strtotime($date));
  if (Date('Y-m-d') == Date('Y-m-d', strtotime($baseline))) return 'TODAY';
  if (Date('Y-m-d') == Date('Y-m-d', strtotime($baseline.' -1 day'))) return 'TOMORROW';
  return Date('l @ g:ia', strtotime($date));
}