<?php
if (!defined('CCH')) die('Этот скрипт не может работать самостоятельно.');

// Load config
$config = load_array('./data/config.php'); // contains admin_login and admin_password among other things
if (!$config)
{
	$config = [];
	$config['admin_login'] = 'Admin';
	$config['admin_password'] = 'Password123';
	$config['admin_password_reset'] = true;
	$config['open_registration'] = false;
	$config['skin'] = 'original';
	$config['current_season'] = 'opf-2024'; // or 'none'
	save_array('./data/config.php', $config);
}

// Load registered users
// $users is an array of unordered maps consisting of fields: 'login', 'password', 'group', 'seasons'
// groups are: 'admin', 'user', 'guest'
$users = load_array('./data/users.php');
if (!$users)
{
	$users = [];

	$users[0]['login'] = $config['admin_login'];
	$users[0]['password'] = $config['admin_password'];
	$users[0]['group'] = 'admin';
	$users[0]['seasons'] = [];

	save_array('./data/users.php', $users);
}

// Current user associative array
$user = [];
$season_master = false; // set to true if the user is allowed to administer the season


// $commentators is an unordered map of structures with fields 'score_weeks', 'score_total', 'removed' and 'removed_date'.
// A commentator's nickname is used as a key.
$commentators = [];

// $commentators_names is an array of commentators' nicknames.
// It is used for sorting purposes.
$commentators_names = [];

// $links is an unordered map of arrays that store hyperlinks to nominated comments.
// A commentator's nickname is used as a key.
$links = [];

// $seasons is an unordered map of structures with fields 'name' and 'starting_date'
$seasons = [];

// workspace is a structure of variables: 'current_season', 'current_week'
$workspace = [];

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

function error_message($text)
{
	global $html;
	$html['error'] .= '<div class="red">Ошибка: ' . $text .'</div>';
}

function trace_message($text)
{
	global $html;
	$html['error'] .= '<div class="yellow">Сообщение: ' . $text .'</div>';
}

function success_message($text)
{
	global $html;
	$html['error'] .= '<div class="green">Сообщение: ' . $text .'</div>';
}

// there is a chance i would consider storing variables in a different manner, so it could be useful to have these two functions
function get_var($name)
{
	if (isset($_POST[$name]))
		return $_POST[$name];

	if (isset($_GET[$name]))
		return $_GET[$name];

	return false;
}

function get_session_var($name)
{
	if (isset($_SESSION[$name]))
		return $_SESSION[$name];

	return false;
}

//
function render($template)
{
	// system globals
	global $mode, $section, $userid, $season, $week;
	global $config, $users, $user, $season_master, $workspace, $html;

	// page globals
	global $commentators, $commentators_names, $commentators_count, $links, $links_count, $seasons, $values; // many pages
	global $week_selector_form_action, $week_start, $week_end, $week_latest, $week_number; // week_selection
	global $week_start_day, $week_end_day, $week_start_month, $week_end_month, $week_start_year, $week_end_year, $months; // week_post
	global $results, $step; // week_results

	ob_start();
	include './skins/'.$config['skin'].'/'.$template.'.html.php';
	return ob_get_clean();
}

function load_array($file)
{
	if (file_exists($file))
	{
		return unserialize(mb_substr(file_get_contents($file), 14, null, mb_internal_encoding()));
	} else {
		return false;
	}
}

function load_array_old($file)
{
	if (file_exists($file))
	{
		return unserialize(file_get_contents($file));
	} else {
		return false;
	}
}

function save_array($file, &$array)
{
	file_put_contents($file, '<?php die();?>'.serialize($array));
}

function load_commentators_and_links()
{
	global $commentators, $links, $commentators_names, $commentators_count, $links_count, $workspace, $week_number;

	// load commentators and links to comments
	$commentators = load_array_old('./data/seasons/'.$workspace['current_season'].'/commentators.php');
	if (!$commentators)
	{
		$commentators = load_array("./data/seasons/".$workspace['current_season']."/commentators.php");
	}

	// count how many commentators we have this season
	$commentators_count = 0;
	if ($commentators)
	{
		$commentators_count = count($commentators);
	}

	// sort commentators' names and store their sorted order
	if ($commentators_count > 0)
	{
		foreach ($commentators as $key => $value)
		{
			$commentators_names[] = $key;
		}
		mb_sort($commentators_names);
	}

	$links = load_array_old('./data/seasons/'.$workspace['current_season'].'/'.$week_number.'-links.php');
	if(!$links)
	{
		$links = load_array('./data/seasons/'.$workspace['current_season'].'/'.$week_number.'-links.php');
	}

	// no links added on this week!
	if (!$links && ($week_number > 0))
	{
		$links = [];
		save_array('./data/seasons/'.$workspace['current_season'].'/'.$week_number.'-links.php', $links);
	}

	// count how many links were nominated this week
	if ($links)
	{
		$links_count = count($links);
	} else {
		$links_count = 0;
	}
}

function locate_old_format_and_upgrade()
{
	$files = scandir(CCH_BASE_DIR . '/data/seasons');
	foreach($files as $file)
	{
		if (is_dir(CCH_BASE_DIR . '/data/seasons/' . $file) && ($file != '..') && ($file != '.') && ($file != 'trash'))
		{
			// Old way of updating database files to the new format
			$commentators = load_array_old('./data/seasons/'.$file.'/commentators.txt');
			if ($commentators)
			{
				foreach($commentators as $key => $value)
				{
					if (!isset($commentators[$key]['score_weeks']))
						$commentators[$key]['score_weeks'] = 0;
					if (!isset($commentators[$key]['score_total']))
						$commentators[$key]['score_total'] = 0;
					if (!isset($commentators[$key]['removed']))
						$commentators[$key]['removed'] = false;
					if (!isset($commentators[$key]['removed_date']))
						$commentators[$key]['removed_date'] = 0;
				}
				save_array('./data/seasons/'.$file.'/commentators.php', $commentators);
			}

			for ($i = 1; $i < 50; ++$i)
			{
				$links = load_array_old('./data/seasons/'.$file.'/'.$i.'-links.txt');
				if ($links)
				{
					save_array('./data/seasons/'.$file.'/'.$i.'-links.php', $links);
				}

				$results = load_array_old('./data/seasons/'.$file.'/'.$i.'-results.txt');
				if ($results)
				{
					foreach($results as $key => $value)
					{
						/*
						if (!isset($results[$key]['votes']))
							$results[$key]['votes'] = -1;
						if (!isset($results[$key]['additional_votes']))
							$results[$key]['additional_votes'] = 0;
						if (!isset($results[$key]['votes_total']))
							$results[$key]['votes_total'] = 0;
						if (!isset($results[$key]['score']))
							$results[$key]['score'] = 0;
						*/
					}
					save_array('./data/seasons/'.$file.'/'.$i.'-results.php', $results);
				}
			}
		}
	}
}

function register_new_user(&$login, &$password)
{
	global $users;

	for ($i = 0; $i < count($users); ++$i)
	{
		if ($users[$i]['login'] == $login)
		{
			error_message('Пользователь с таким ником уже существует! В регистрации отказано.');
			return false;
		}
	}

	$users[] = array(
		'login'		=>	$login,
		'password'	=>	$password,
		'group'		=>	'user',
		'seasons'	=> []
		);
	save_array('./data/users.php', $users);
	success_message('Пользователь с ником "'.$login.'" успешно зарегистрирован.');
}

?>