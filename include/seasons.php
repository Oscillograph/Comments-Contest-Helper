<?php

// Выбор сезона
$season_select = get_var('season_select');
$season_type = get_var('season_type');
$season_year = get_var('season_year');

if($season_select || $season_type || $season_year)
{
	// Старт сезона
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
	} else {
		if (is_dir('./data/seasons/'.$season_select))
		{
			$workspace['current_season'] = $season_select;
			$workspace['current_week'] = 0;
			$_SESSION['current_season'] = $workspace['current_season'];
			$_SESSION['current_week'] = $workspace['current_week'];
		}
	}

	header('location: ?mode=week_nominations&season='.$workspace['current_season']);
}

?>