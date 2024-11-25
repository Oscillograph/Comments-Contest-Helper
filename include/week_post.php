<?php

// load commentators and links to comments
load_commentators_and_links();

// strings to paste
$months = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];

$week_start_day = intval(date('d', $week_start));
$week_end_day = intval(date('d', $week_end));
$week_start_month = $months[intval(date('m', $week_start))-1];
$week_end_month = $months[intval(date('m', $week_end))-1];
$week_start_year = intval(date('Y', $week_start));
$week_end_year = intval(date('Y', $week_end));

?>