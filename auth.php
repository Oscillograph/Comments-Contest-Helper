<?php

session_start();

function logged_in()
{
	global $admin_login;
	global $admin_password;

	if (isset($_SESSION['login']))
	{
		return true;
	} else {
		if (isset($_POST['login']) && isset($_POST['password']))
		{
			$login = $_POST['login'];
			$password = $_POST['password'];

			if ($login == $admin_login)
			{
				if ($password == $admin_password)
				{
					$_SESSION['login'] = 'login';
					return true;
				} else {
					echo '<div class="red">Ошибка: неверный пароль!</div>';
				}
			} else {
				echo '<div class="red">Ошибка: неверный логин!</div>';
			}
		}
	}
	return false;
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

?>
		<div class='gbox'>
			<center>
				Вы вышли.
			</center>
		</div>
<?php
}

?>