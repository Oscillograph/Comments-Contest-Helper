<?php
if (!defined('CCH')) die('Этот скрипт не может работать самостоятельно.');

$html['meta'] = render('meta');
$html['header'] = render('header');
$html['title'] = 'Помощник ведущего конкурса комментариев';

if ($mode)
{
	switch($mode)
	{
		case 'week_nominations':
		{
			$html['content'] .= render('week_selection');
			$html['content'] .= render('week_nominations');
			$html['title'] .= ' - Номинации';
			break;
		}
		case 'week_post':
		{
			$html['content'] .= render('week_selection');
			$html['content'] .= render('week_post');
			$html['title'] .= ' - Пост голосования';
			break;
		}
		case 'week_results':
		{
			$html['content'] .= render('week_selection');
			$html['content'] .= render('week_results');
			$html['title'] .= ' - Итоги недели';
			break;
		}
		case 'season_results':
		{
			$html['content'] .= render('season_results');
			$html['title'] .= ' - Результаты сезона';
			break;
		}
		case 'settings':
		{
			$html['content'] .= render('settings');
			$html['title'] .= ' - Настройки';
			break;
		}

		default:
		{
			$html['content'] .= render('seasons');
			$html['title'] .= ' - Выбор сезона';
			break;
		}
	}
} else {
	$html['content'] = render('seasons');
	$html['title'] .= ' - Выбор сезона';
}

$html['footer'] = render('footer');

include './skins/'.$config['skin'].'/layout.html.php';

?>