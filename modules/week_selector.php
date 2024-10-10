<?php
	if (isset($_POST['week_selector_todo']))
	{
		// select the right week
		if ($_POST['week_selector_todo'] == 'change_current_week')
		{
			$select_week = $_POST['select_week'];
			$workspace['current_week'] = intval($select_week);
			file_put_contents('./seasons/workspace.txt', serialize($workspace));
			
			$week_number = intval($workspace['current_week']);
			$week_start = $seasons[$workspace['current_season']]['starting_date'] + ($week_number - 1)*$week_length;
			$week_end = $seasons[$workspace['current_season']]['starting_date'] + $week_number*($week_length-1);
		}

		// change season starting date
		if ($_POST['week_selector_todo'] == 'change_starting_date')
		{
			// collect new date
			$new_starting_day = intval($_POST['new_starting_day']);
			$new_starting_month = intval($_POST['new_starting_month']);
			$new_starting_year = intval($_POST['new_starting_year']);
			echo $new_starting_day . '.' . $new_starting_month . '.' . $new_starting_year;

			// update date
			$new_date = mktime(0, 0, 0, $new_starting_month, $new_starting_day, $new_starting_year);
			$seasons[$workspace['current_season']]['starting_date'] = $new_date;

			// save seasons data
			file_put_contents('./seasons/seasons.txt', serialize($seasons));
		}
	}
?>

<form action='<?=$week_selector_form_action;?>' id='week_selector_form' method='POST'>
	<div class='gbox'>
		<div class='header'>
			Начало сезона: 
			<?=date('d.m.Y', $seasons[$workspace['current_season']]['starting_date']);?>
			&nbsp;
			<input type="button" class="input-button green" value="Изменить" onClick="javascript: document.getElementById('new_starting_day').parentNode.style.display = 'inline-block';">
			<div style='display: none;'>
				<select id='new_starting_day' name='new_starting_day'>
					<? for ($i=31; $i>0; $i--) { ?>
					<option value='<?=$i;?>' <?=($i == intval(date('d'))) ? 'selected' : '';?>><?=$i;?></option>
					<? } ?>
				</select>
				<select id='new_starting_month' name='new_starting_month'>
					<? for ($i=12; $i>0; $i--) { ?>
					<option value='<?=$i;?>' <?=($i == intval(date('m'))) ? 'selected' : '';?>><?=$i;?></option>
					<? } ?>
				</select>
				<select id='new_starting_year' name='new_starting_year'>
					<? $this_year = intval(date('Y')); for ($i=$this_year; $i>($this_year - 20); $i--) { ?>
					<option value='<?=$i;?>' <?=($i == intval(date('Y'))) ? 'selected' : '';?>><?=$i;?></option>
					<? } ?>
				</select>
				<input type="button" class="input-button green" name="button" value="Сохранить" onClick="javascript: document.getElementById('week_selector_todo').setAttribute('value', 'change_starting_date'); document.getElementById('week_selector_form').submit();">
			</div> 
		</div>

		<? if ($week_selector_form_action != '?mode=contest_results') { ?>
		<center>
			Выбрана неделя №: 
			<select name='select_week' onChange="javascript: document.getElementById('week_selector_todo').setAttribute('value', 'change_current_week'); document.getElementById('week_selector_form').submit();">
				<?php
				for ($i = $week_latest; $i > 0; --$i)
				{
				?>
				<option value='<?=$i;?>' <?=($i == $workspace['current_week']) ? 'selected' : '';?>><?=$i;?> <?=($i == $week_latest) ? '(текущая)' : '';?></option>
				<?php
				}
				?>
			</select>
			<br>
			(с <?=date('d.m.Y', $week_start);?> по <?=date('d.m.Y', $week_end);?> включительно)
		</center>
		<? } ?>
	</div>

	<input type="hidden" name="week_selector_todo" id="week_selector_todo" value="">
</form>