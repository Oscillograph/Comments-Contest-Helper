<?php if (!defined('CCH')) die('Этот скрипт не может работать самостоятельно.'); ?>

<div class='gbox'>
	<div class='header'>
		Выбор сезона
	</div>
	<div style='margin: 0.25em; text-align: center;'>
		<?foreach($seasons as $key => $value):?>
		<div style='display: inline-block;'>
			<form action='' method="POST">
				<input type="hidden" name= "season_select" value="<?=$key;?>">
				<input type="submit" class="input-button<?=($key == $workspace['current_season']) ? ' red' : '';?>" name="submit" value="<?=$seasons[$key]['name'];?>">
			</form>
		</div>
		<?endforeach;?>

		<br>
		<?if($user['group']=='admin'):?>
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
					<select id='season_year' name='season_year'>
					<?for($i = intval(date('Y'))-50; $i < intval(date('Y'))+50; ++$i):?>
						<option value=<?=$i;?><?=(($i == intval(date('Y'))) ? ' selected':'');?>><?=$i;?></option>
					<?endfor;?>
					</select>
					<input type="submit" class="input-button green" name="submit" value="Создать">
				</div>
			</form>
		</div>
		<?endif;?>
	</div>
</div>