<?php if (!defined('CCH')) die('Этот скрипт не может работать самостоятельно.'); ?>

<?if($workspace['current_season']!=='none'):?>
<form action='<?=$week_selector_form_action;?>' id='week_selector_form' method='POST'>
	<div class='gbox'>
		<div class='header'>
			Начало сезона: 
			<?=date('d.m.Y', $seasons[$workspace['current_season']]['starting_date']);?>
		</div>

		<center>
			Выбрана неделя №: 
			<select name='select_week' onChange="javascript: document.getElementById('week_selector_form').submit();">
				<?for ($i = $week_latest; $i > 0; --$i):?>
				<option value=<?=$i;?> <?=($i == $workspace['current_week']) ? 'selected' : '';?>><?=$i;?> <?=($i == $week_latest) ? '(текущая)' : '';?></option>
				<?endfor;?>
			</select>
			<br>
			(с <?=date('d.m.Y', $week_start);?> по <?=date('d.m.Y', $week_end);?> включительно)
		</center>
	</div>

	<input type="hidden" name="week_selector_todo" id="week_selector_todo" value="change_current_week">
</form>
<?endif;?>