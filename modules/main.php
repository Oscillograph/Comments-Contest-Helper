<?php

// Выбор сезона
if(isset($_POST['season_select']))
{
	// Старт сезона
	if ($_POST['season_select'] == 'new_season')
	{
		if(isset($_POST['season_type']))
		{
			$new = '';
			switch($_POST['season_type'])
			{
				case 'vpf':
				{
					$year = date('Y');
					$new = 'vpf-' . $year;
					$seasons[$new]['name'] = 'ВПФ ' . $year;
					$seasons[$key]['starting_date'] = mktime(0, 0, 0, intval(date('m')), intval(date('d')), intval(date('Y')));
					break;
				}

				case 'opf':
				{
					$year = date('Y');
					$new = 'opf-' . $year;
					$seasons[$new]['name'] = 'ОПФ ' . $year;
					$seasons[$key]['starting_date'] =  mktime(0, 0, 0, intval(date('m')), intval(date('d')), intval(date('Y')));
					break;
				}

				case 'sf':
				{
					$year = date('Y');
					$new = 'sf-' . $year;
					$seasons[$new]['name'] = 'ОК НФ ' . $year;
					$seasons[$key]['starting_date'] =  mktime(0, 0, 0, intval(date('m')), intval(date('d')), intval(date('Y')));
					break;
				}
			}
			mkdir('./seasons/'.$new);
			file_put_contents('./seasons/seasons.txt', serialize($seasons));
		}
	} else {
		if (is_dir('./seasons/'.$_POST['season_select']))
		{
			$workspace['current_season'] = $_POST['season_select'];
			$workspace['current_week'] = 0;
			file_put_contents('./seasons/workspace.txt', serialize($workspace));
		}
	}
}

?>
<div class='gbox'>
	<div class='header'>
		Выбор сезона
	</div>
	<div style='margin: 0.25em; text-align: center;'>
<?php
// render buttons to select a season to work with
foreach($seasons as $season => $value)
{
?>
		<div style='display: inline-block;'>
			<form action='' method="POST">
				<input type="hidden" name= "season_select" value="<?=$season;?>">
				<input type="submit" class="input-button<?=($season == $workspace['current_season']) ? ' red' : '';?>" name="submit" value="<?=$seasons[$season]['name'];?>">
			</form>
		</div>
<?php
}
?>

		<br>
		<div style='display: inline-block;'>
			<form action='' method="POST">
				<input type="hidden" name= "season_select" value="new_season">
				<input type="button" class="input-button green" value="Начать новый сезон" onClick="javascript: document.getElementById('season_type').parentNode.style.display = 'inline-block';">
				<div style='display: none;'>
					<select id='season_type' name='season_type'>
						<option value='vpf'>Весенний ПФ</option>
						<option value='opf'>Осенний ПФ</option>
						<option value='sf'>Открытый Конкурс НФ</option>
					</select>
					<input type="submit" class="input-button green" name="submit" value="Создать">
				</div>
			</form>
		</div>
	</div>
</div>