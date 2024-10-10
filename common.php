<?php

// $commentators is an unordered map of structures with fields 'score_weeks', 'score_total', 'removed' and 'removed_date'.
// A commentator's nickname is used as a key.
$commentators = [];

// $commentators_names is an array of commentators' nicknames.
// It is used for sorting purposes.
$commentators_names = [];

// $links is an unordered map of arrays that store hyperlinks to nominated comments.
// A commentator's nickname is used as a key.
$links = [];

// workspace is a structure of variables: 'current_season', 'urrent_week'
$workspace = [];
if (is_file('./seasons/workspace.txt'))
{
	$workspace = unserialize(file_get_contents('./seasons/workspace.txt'));
}

// $seasons is an unordered map of structures with fields 'name' and 'starting_date'
$seasons = [];
if (is_file('./seasons/seasons.txt'))
{
	$seasons = unserialize(file_get_contents('./seasons/seasons.txt'));
}

// prepare timestamps in seconds
$week_length = (mktime(0,0,0,1,7,2024) - mktime(0,0,0,1,0,2024)); // basically, it's 7 * 86400 seconds
$time_past_season_started = (mktime(0,0,0,intval(date('m')),intval(date('d')),intval(date('Y'))) - $seasons[$workspace['current_season']]['starting_date']); 

$week_number = 0; // initial value to check if the season started yet

$week_latest = ceil($time_past_season_started / $week_length);

if (isset($workspace['current_week']))
{
	if (intval($workspace['current_week']) > 0)
	{
		$week_number = intval($workspace['current_week']);
	} else {
		$week_number = $week_latest;
	}
} else {
	$week_number = $week_latest;
}

$week_start = $seasons[$workspace['current_season']]['starting_date']; // starting with the season
$week_end = $week_start + $week_length - 1; // ending in seven days

if ($week_number > 0)
{
	$week_start = $seasons[$workspace['current_season']]['starting_date'] + ($week_number - 1)*$week_length;
	$week_end = $seasons[$workspace['current_season']]['starting_date'] + $week_number*($week_length-1);
}

$week_number = ($week_number > 14) ? $week_number : 14; // prevent week number from going to infinity. TODO: allow to close a season


// = = = = = functions block = = = = =
// proudly heavily based on sectus' edit of Jaison Erick's answer on https://stackoverflow.com/questions/7929796/how-can-i-sort-an-array-of-utf-8-strings-in-php
function mb_strcasecmp($str1, $str2, $encoding = null)
{
	if (null === $encoding)
	{
		$encoding = mb_internal_encoding();
	}

	// // lower case 	
	// $letters_order = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я'];
	// $str1 = mb_strtolower($str1, $encoding);
	// $str2 = mb_strtolower($str2, $encoding);

	// upper case
	$letters_order = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я'];
	$str1 = mb_strtoupper($str1, $encoding);
	$str2 = mb_strtoupper($str2, $encoding);

	$str1_length = mb_strlen($str1);
	$str2_length = mb_strlen($str2);

	for ($i = 0; ($i < $str1_length) && ($i < $str2_length); ++$i)
	{
		$a = mb_substr($str1, $i, 1);
		$b = mb_substr($str2, $i, 1);
		$a_index = array_search($a, $letters_order);
		$b_index = array_search($b, $letters_order);

		if ($a_index == $b_index) 
			continue;
		if ($a_index > $b_index)
		{
			return 1;
		} else {
			return -1;
		}
	}

	if ($str1_length == $str2_length)
		return 0;
	if ($str1_length > $str2_length)
	{
		return -1;
	} else {
		return 1;
	}
}

function mb_sort(&$array)
{
	usort($array, function($a, $b) { 
		return mb_strcasecmp($a, $b);
	});
}

function sort_winners(&$array)
{
	usort($array, function($a, $b) {
		if ($a[1] > $b[1])
			return -1;
		if ($a[1] == $b[1])
			return 0;
		if ($a[1] < $b[1])
			return 1;
	});
}
?>