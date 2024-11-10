<?php

session_start();

include './config.php';
include './common.php';
include './auth.php'; 

// prepare html buffer string
$html_buffer = file_get_contents('./skins/original/layout.html');
$html_buffer = str_replace('<% meta.html %>', file_get_contents('./skins/original/meta.html'), $html_buffer);
$html_buffer = str_replace('<% header.html %>', file_get_contents('./skins/original/header.html'), $html_buffer);
$html_buffer = str_replace('<% content.html %>', file_get_contents('./skins/original/content.html'), $html_buffer);
$html_buffer = str_replace('<% footer.html %>', file_get_contents('./skins/original/footer.html'), $html_buffer);
$html_buffer = str_replace('<% scripts.html %>', file_get_contents('./skins/original/scripts.html'), $html_buffer);


$html_content = '';
$html_title = '';

$week_selector_form_action = '/'; // костыль для адреса в форме week_selector.php

ob_start();

if (logged_in())
{
	if (isset($_GET['mode']))
	{
		switch ($_GET['mode']) 
		{
			case 'week_nominations':
			{
				$html_title = 'Номинации недели';
				$week_selector_form_action = '?mode=week_nominations';
				include './modules/week_selector.php';
				include './modules/week_nominations.php';
				break;
			}

			case 'week_post':
			{
				$html_title = 'Пост голосования';
				$week_selector_form_action = '?mode=week_post';
				include './modules/week_selector.php';
				include './modules/week_post.php';
				break;
			}

			case 'week_results':
			{
				$html_title = 'Итоги недели';
				$week_selector_form_action = '?mode=week_results';
				include './modules/week_selector.php';
				include './modules/week_results.php';
				break;
			}

			case 'contest_results':
			{
				$html_title = 'Результаты конкурса';
				$week_selector_form_action = '?mode=contest_results';
				include './modules/week_selector.php';
				include './modules/contest_results.php';
				break;
			}

			case 'logout':
			{
				$html_title = 'Выход';
				logout();
				break;
			}

			default:
			{
				$html_title = 'Такой страницы нет.';
				$html_content = 'Ошибка 404! Такой страницы нет, и Помощник Ведущего не знает, что с этим делать.';
			}
		}
	} else {
		// Мы на главной
		$html_title = 'Главная';
		include './modules/main.php';
	}	
} else {
	$html_title = 'Вход';
	login_form();
}



// render page
$html_content = ob_get_clean();

$html_buffer = str_replace('<% html_content %>', $html_content, $html_buffer);
$html_buffer = str_replace('<% title %>', $html_title, $html_buffer);

echo $html_buffer;

?>