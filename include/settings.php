<?php

$userid = get_var('userid');
$section = get_var('section');
$season = get_var('season'); // костыль, потому что backend записывает сюда своё

if ($section == 'seasons')
{
	// Новый сезон
	$season_select = get_var('season_select');
	$season_type = get_var('season_type');
	$season_year = get_var('season_year');

	if ($season_select == 'new_season')
	{
		$new = '';
		$season_year = intval($season_year);
		switch($season_type)
		{
			case 'vpf':
			{
				$new = 'vpf-' . $season_year;
				$seasons[$new]['name'] = 'ВПФ ' . $season_year;
				break;
			}

			case 'opf':
			{
				$new = 'opf-' . $season_year;
				$seasons[$new]['name'] = 'ОПФ ' . $season_year;
				break;
			}

			case 'sf':
			{
				$new = 'sf-' . $season_year;
				$seasons[$new]['name'] = 'ОК НФ ' . $season_year;
				break;
			}
		}

		$seasons[$new]['starting_date'] =  mktime(0, 0, 0, intval(date('m')), intval(date('d')), $season_year);
		$seasons[$new]['ending_date'] =  mktime(0, 0, 0, intval(date('m')), intval(date('d')), $season_year);
		$seasons[$new]['closed'] = false;
		$seasons[$new]['users'] = [];

		mkdir('./data/seasons/'.$new);
		save_array('./data/seasons/seasons.php', $seasons);
		success_message('Новый сезон добавлен.');
	}

	// Править сезон
	if ($season)
	{
		$season_name = get_var('season_name');

		$new_starting_day = get_var('new_starting_day');
		$new_starting_month = get_var('new_starting_month');
		$new_starting_year = get_var('new_starting_year');

		$new_ending_day = get_var('new_ending_day');
		$new_ending_month = get_var('new_ending_month');
		$new_ending_year = get_var('new_ending_year');

		$season_closed = get_var('season_closed');
		$season_user = get_var('season_user');

		if ($season_name)
		{
			$seasons[$season]['name'] = $season_name;
			$seasons[$season]['starting_date'] = mktime(0, 0, 0, intval($new_starting_month), intval($new_starting_day), intval($new_starting_year));
			$seasons[$season]['ending_date'] = mktime(0, 0, 0, intval($new_ending_month), intval($new_ending_day), intval($new_ending_year));
			$seasons[$season]['closed'] = ($season_closed == '1') ? true : false;
			$seasons[$season]['user'] = intval($season_user);

			save_array('./data/seasons/seasons.php', $seasons);
			success_message('Сезон настроен.');
		}
	}
}

if ($section == 'users')
{
}

if ($section == 'edit_user')
{
	if ($user['group'] != 'guest')
	{
		$login = get_var('login');
		$password = get_var('password');
		$change_password = get_var('change_password');
		$userid = get_var('userid');
		$delete = get_var('delete');

		if (isset($users[$userid]))
		{
			if (!$change_password)
			{
				$password = $users[$userid]['password'];
			}

			// edit own profile
			if ($login && $password && ($userid == $user['userid']))
			{
				if ($userid != 0)
				{
					$users[$userid]['login'] = $login;
					$users[$userid]['password'] = $password;
					$user['login'] = $login;

					save_array('./data/users.php', $users);
					success_message('Профиль отредактирован.');
				} else {
					error_message('Профиль админа нужно править в настройках системы.');
				}
			}

			// edit someone's profile
			if ($login && $password && ($userid != $user['userid']) && ($user['group'] == 'admin'))
			{
				if (!$delete)
				{
					$users[$userid]['login'] = $login;
					$users[$userid]['password'] = $password;
					$user['login'] = $login;
				} else {
					unset($users[$userid]);
				}

				save_array('./data/users.php', $users);
				success_message('Профиль "'.$login.'" отредактирован.');
			}
		} else {
			error_message('Пользователь не найден.');
		}
	}
}

if ($section == 'system')
{
	$admin_login = get_var('admin_login');
	$admin_password = get_var('admin_password');
	$admin_password_reset = get_var('admin_password_reset');
	$open_registration = get_var('open_registration'); // boolean!
	$skin = get_var('skin');
	$current_season = get_var('current_season');

	if ($admin_login && $admin_password && $admin_password_reset && $skin && $current_season)
	{
		$config['admin_login'] = $admin_login;
		$config['admin_password'] = $admin_password;
		$config['admin_password_reset'] = $admin_password_reset;
		$config['open_registration'] = $open_registration;
		$config['skin'] = $skin;
		$config['current_season'] = $current_season;

		$users = load_array('./data/users.php');
		if (!$users)
		{
			$users = [];
		}
		$users[0]['login'] = $config['admin_login'];
		$users[0]['password'] = $config['admin_password'];
		$users[0]['group'] = 'admin';
		$users[0]['seasons'] = [];

		save_array('./data/config.php', $config);
		save_array('./data/users.php', $users);
		success_message('Настройки сохранены.');
	}

	$values['skins'] = [];
	$skin_dirs = scandir(CCH_BASE_DIR . '/skins');
	foreach ($skin_dirs as $dir)
	{
		if (is_dir(CCH_BASE_DIR . '/skins/' . $dir) && ($dir != '..') && ($dir != '.'))
		{
			$values['skins'][] = $dir;
		}
	}
}