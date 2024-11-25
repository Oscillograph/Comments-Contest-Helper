<?php
if (!defined('CCH')) die('Этот скрипт не может работать самостоятельно.');

$html['meta'] = render('meta');
$html['header'] = render('header');

if ($mode)
{
	switch($mode)
	{
		case 'week_nominations':
		{
			$html['content'] .= render('week_selection');
			$html['content'] .= render('week_nominations');
			break;
		}
		case 'week_post':
		{
			$html['content'] .= render('week_selection');
			$html['content'] .= render('week_post');
			break;
		}
		case 'week_results':
		{
			$html['content'] .= render('week_selection');
			$html['content'] .= render('week_results');
			break;
		}
		case 'season_results':
		{
			$html['content'] .= render('season_results');
			break;
		}
		case 'settings':
		{
			$html['content'] .= render('settings');
			break;
		}

		default:
		{
			$html['content'] .= render('seasons');
			break;
		}
	}
} else {
	$html['content'] = render('seasons');
}

$html['footer'] = render('footer');

include './skins/'.$config['skin'].'/layout.html.php';

?>