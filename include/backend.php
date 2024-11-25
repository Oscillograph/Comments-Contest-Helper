<?php
if (!defined('CCH')) die('Этот скрипт не может работать самостоятельно.');

if (!authorized())
{
	if (!login())
	{
		$user['login'] = 'гость';
		$user['group'] = 'guest';
		$user['seasons'] = [];
	}
}

if (authorized())
{
	$user['userid'] = $_SESSION['userid'];
	if ($user['userid'] >= 0)
	{
		$user['login'] = $users[$user['userid']]['login'];
		$user['group'] = $users[$user['userid']]['group'];
		$user['seasons'] = $users[$user['userid']]['seasons'];
	}

	if ($user['userid'] == 0)
	{
		$user['seasons'] = $seasons;
	}
}

// load session vars - we trust them most
if (isset($_SESSION['current_season']))
{
	$workspace['current_season'] = $_SESSION['current_season'];
} else {
	$workspace['current_season'] = $config['current_season'];
	$_SESSION['current_season'] = $workspace['current_season'];
}
if (isset($_SESSION['current_week']))
{
	$workspace['current_week'] = $_SESSION['current_week'];
} else {
	$workspace['current_week'] = 0;
	$_SESSION['current_week'] = $workspace['current_week'];
}

// get GET/POST variables
$mode = get_var('mode');
$season = get_var('season');
$week = get_var('week');

// load seasons
$seasons = load_array_old('./data/seasons/seasons.txt');
if (!$seasons)
	$seasons = load_array('./data/seasons/seasons.php');
// no seasons yet!
if (!$seasons)
{
	$seasons = [];
	$files = scandir(CCH_BASE_DIR . '/data/seasons');
	foreach($files as $file)
	{
		if (is_dir(CCH_BASE_DIR . '/data/seasons/' . $file) && ($file != '..') && ($file != '.') && ($file != 'trash'))
		{
			$seasons[$file]['name'] = $file;
			$seasons[$file]['starting_date'] = mktime(0, 0, 0, intval(date('m')), intval(date('d')), intval(date('Y')));
			$seasons[$file]['ending_date'] = mktime(0, 0, 0, intval(date('m')), intval(date('d')), intval(date('Y')));
			$seasons[$file]['closed'] = true;
			$seasons[$file]['users'] = [];
		}
	}
	save_array('./data/seasons/seasons.php', $seasons);
}

// upgrade old database format
// locate_old_format_and_upgrade();

// select the season from $season GET-variable or from session
if (!$season)
{
	$season = $workspace['current_season'];
} else {
	if (!isset($seasons[$season]))
	{
		// TODO: make these work
		$workspace['current_season'] = 'none';
		$_SESSION['current_season'] = 'none';
		error_message('Такой сезон в системе не зарегистрирован.');
	} else {
		$workspace['current_season'] = $season;
		$_SESSION['current_season'] = $season;
	}
}

// update the season master flag
if ($user['group'] == 'admin')
{
	$season_master = true;
}
if ($user['group'] != 'guest')
{
	if (isset($user['seasons'][$workspace['current_season']]))
	{
		$season_master = true;
	}
}

// select the week from $week GET variable or from session
if (!$week)
{
	$week = $workspace['current_week'];
} else {
	$workspace['current_week'] = intval($week);
	$_SESSION['current_week'] = $workspace['current_week'];
}

// prepare timestamps in seconds
$week_length = (mktime(0,0,0,1,7,2024) - mktime(0,0,0,1,0,2024)); // basically, it's 7 * 86400 seconds
$time_past_season_started = 0;
$week_number = 0;
$week_latest = 1;
$week_start = 0;
$week_end = 0;

if ($workspace['current_season'] !== 'none')
{
	$time_past_season_started = (mktime(intval(date('H')),intval(date('i')),intval(date('s')),intval(date('m')),intval(date('d')),intval(date('Y'))) - $seasons[$workspace['current_season']]['starting_date']); 

	$week_number = 0; // initial value to check if the season started yet

	$week_latest = ceil($time_past_season_started / $week_length);
	$week_latest = ($week_latest < 14) ? $week_latest : 14; // prevent week number from going to infinity. TODO: allow to close a season

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

	if ($week_number <= 0)
	{
		trace_message('Выбран сезон "'.$seasons[$season]['name'].'", который ещё не начался.');
	}
} else {
	$mode = 'seasons';
}

// create an array for temporary variables
$values = [];

include './include/week_selection.php';

if ($mode)
{
	switch($mode)
	{
		case 'week_nominations':
		{
			include './include/week_nominations.php';
			break;
		}
		case 'week_post':
		{
			include './include/week_post.php';
			break;
		}
		case 'week_results':
		{
			include './include/week_results.php';
			break;
		}
		case 'season_results':
		{
			include './include/season_results.php';
			break;
		}
		case 'seasons':
		{
			include './include/seasons.php';
			break;
		}
		case 'settings':
		{
			include './include/settings.php';
			break;
		}
		case 'login':
		{
			include './include/login.php';
			break;
		}
		case 'logout':
		{
			// DONE
			include './include/logout.php';
			break;
		}

		// if we can't parse $mode
		default:
		{
			include './include/seasons.php';
		}
	}
} else {
	// if no $mode set
	include './include/seasons.php';
}

?>