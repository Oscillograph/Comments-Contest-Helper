<?php

// session_start();

$logged_in = false;

function authorized()
{
	if (isset($_SESSION['userid']))
	{
		if ($_SESSION['userid'] >= 0)
			return true;
	}
	return false;
}

function login()
{
	global $config;
	global $users;

	$login = get_var('login');
	$password = get_var('password');
	$register = $config['open_registration'] ? get_var('register') : false;

	if ($login && $password)
	{
		if ($register)
		{
			register_new_user($login, $password);
		}

		if (($login == $config['admin_login']) && ($password == $config['admin_password']))
		{
			$_SESSION['userid'] = 0;
			$_SESSION['group'] = 'admin';
			return true;
		} elseif (($login == $config['admin_login']) || ($password == $config['admin_password'])) {
			error_message($config['admin_login'] . ' ' . $config['admin_password']);
			error_message($login . ' ' . $password);
			error_message('Неверный логин или пароль!');
			return false;
		}

		for ($i = 0; $i < count($users); ++$i)
		{
			if (($login == $users[$i]['login']) && ($password == $users[$i]['password']))
			{
				$_SESSION['userid'] = $i;
				$_SESSION['group'] = 'user';
				return true;
			} elseif (($login == $users[$i]['login']) || ($password == $users[$i]['password'])) {
				error_message('Неверный логин или пароль!');
				return false;
			}
		}

		return false;
	} else {
		$_SESSION['userid'] = -1;
		$_SESSION['group'] = 'guest';
		return false;
	}
}

function login_form()
{
?>
	<form action='./' method='POST' id='theForm'>
		<div class='gbox'>
			<div class='header'>
				Форма входа
			</div>

			<center>
				Введите логин: <br>
				<input type='text' class='input-edit' name='login' value=''><br>
				Введите пароль: <br>
				<input type='text' class='input-edit' name='password' value=''>
				<br> &nbsp; <br>
				<input type='submit' class='input-button green' name='submit' value='Войти'>
			</center>
		</div>
	</form>
<?php
}

function logout()
{
	unset($_SESSION['login']);
	session_destroy();
}

?>