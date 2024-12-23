<?php if (!defined('CCH')) die('Этот скрипт не может работать самостоятельно.'); ?>

<form action='?mode=week_results&season=<?=$season;?>&week=<?=$week;?>' method='POST' id='theForm'>
	<div class='gbox'>
		<div class='header'>
			Итоги недели (<a href='?mode=season_results&season=<?=$season;?>'>перейти к результатам конкурса</a>)
		</div>
		<center>
			<input type='hidden' id='step' name='step' value='<?=($step+1);?>'>

			<?if(($user['group'] == 'guest') && ($step != 4)):?>
			Ведущий пока не подвёл итоги голосования.
			<?else:?>
				<?if($step == 0):?>
				<!-- upload poll data -->
				<table style='width: 100%;' id='upload_poll_results'>
					<tr>
						<td style='width: 100%; text-align: center;'>
							Нужно выделить результаты голосования, начиная с первой буквы первого ника и заканчивая последними процентами, а затем скопировать и вставить в это текстовое поле. Если скопировано правильно, то после отправки этой формы автоматика со всем разберётся и предложит ввести дополнительные голоса, которые, увы, пока придётся считать вручную по сообщениям в теме голосования.<br>&nbsp;
						</td>
					</tr>
					<tr>
						<td style='width: 100%; text-align: center;'>
							<textarea style='width: 95%; height: 200px' name='poll_results'></textarea>
						</td>
					</tr>
				</table>
				<input type="submit" class="input-button green" value="Загрузить результаты">
				<?endif;?>

				<?if($step == 1):?>
				<!-- additional votes -->
				<table>
					<tr>
						<td>
							Ник
						</td>
						<td>
							Голоса
						</td>
						<td>
							Бонус
						</td>
						<td>
							Всего
						</td>
						<td>
							Баллы
						</td>
					</tr>
					<tr>
						<td colspan="5"><hr></td>
					</tr>

					<?for ($i = 0; $i < count($commentators_names); ++$i):?>
						<?if (isset($links[$commentators_names[$i]])):?>
							<?if (count($links[$commentators_names[$i]]) > 0):?>
							<tr<?=(($commentators[$commentators_names[$i]]['removed']) ? ' style="background-color: #882200;"' : '');?>>
								<td><?=$commentators_names[$i];?></td>
								<td><?=$results[$commentators_names[$i]]['votes'];?></td>
								<td><input style='width: 3em;' type="text" name='<?=$i;?>' value='0'></td>
								<td><?=$results[$commentators_names[$i]]['votes_total'];?></td>
								<td><?=$results[$commentators_names[$i]]['score'];?></td>
							</tr>
							<?endif;?>
						<?endif;?>
					<?endfor;?>
				</table>
				<input type="submit" class="input-button green" value="Сохранить дополнительные голоса">
				<?endif;?>

				<?if($step == 2):?>
				<!-- verify and save -->
				<table>
					<tr>
						<td>
							Ник
						</td>
						<td>
							Голоса
						</td>
						<td>
							Бонус
						</td>
						<td>
							Всего
						</td>
						<td>
							Баллы
						</td>
					</tr>
					<tr>
						<td colspan="5"><hr></td>
					</tr>
					<?for($i = 0; $i < count($commentators_names); ++$i):?>
					<?if(isset($results[$commentators_names[$i]])):?>
					<tr>
						<td><?=$commentators_names[$i];?></td>
						<td><?=$results[$commentators_names[$i]]['votes'];?></td>
						<td><?=$results[$commentators_names[$i]]['additional_votes'];?></td>
						<td><?=$results[$commentators_names[$i]]['votes_total'];?></td>
						<td><?=$results[$commentators_names[$i]]['score'];?></td>
					</tr>
					<?endif;?>
					<?endfor;?>
				</table>
				<input type="submit" class="input-button green" value="Утвердить результаты">
				<?endif;?>

				<?if($step == 3):?>
				<!-- preview -->
				<table>
					<tr>
						<td>
							Ник
						</td>
						<td>
							Голоса
						</td>
						<td>
							Бонус
						</td>
						<td>
							Всего
						</td>
						<td>
							Баллы
						</td>
					</tr>
					<tr>
						<td colspan="5"><hr></td>
					</tr>
					<?foreach($results as $nickname => $array):?>
						<?if(isset($array['score'])):?>
						<tr<?=(($commentators[$commentators_names[$i]]['removed']) ? ' style="background-color: #882200;"' : '');?>>
							<td><?=$nickname;?></td>
							<td><?=$results[$nickname]['votes'];?></td>
							<td><?=$results[$nickname]['additional_votes'];?></td>
							<td><?=$results[$nickname]['votes_total'];?></td>
							<td><?=round(100*$results[$nickname]['score'])/100;?></td>
						</tr>
						<?endif;?>
					<?endforeach;?>
				</table>
				<input type="submit" class="input-button green" value="Посчитать заново" onClick="javascript: document.getElementById('step').setAttribute('value', '0');">
				<?endif;?>

				<?if($step == 5):?>
				<?endif;?>

			<?endif;?>

			<?if($step == 4):?>
			<!-- view -->
			<table cellspacing="0" style='text-align: center'>
				<tr>
					<td>
						Ник
					</td>
					<td>
						Голоса
					</td>
					<td>
						Бонус
					</td>
					<td>
						Всего
					</td>
					<td>
						Баллы
					</td>
				</tr>
				<tr>
					<td colspan="5"><hr></td>
				</tr>

				<?for($i = 0; $i < $values['winners_total']; ++$i): $nickname = $values['winners'][$i][0]; ?>
				<?if(isset($results[$nickname]['votes'])):?>
				<tr<?=(($commentators[$nickname]['removed']) ? ' style="background-color: #882200;"' : '');?>
				 <?=($results[$nickname]['score'] > 0) ? ' style="background-color:#113311;"' : '';?>>
					<td><?=$nickname;?></td>
					<td><?=$results[$nickname]['votes'];?></td>
					<td><?=$results[$nickname]['additional_votes'];?></td>
					<td><?=$results[$nickname]['votes_total'];?></td>
					<td><?=round(100*$results[$nickname]['score'])/100;?></td>
				</tr>
				<?endif;?>
				<?endfor;?>

				<tr>
					<td colspan="5"><hr></td>
				</tr>
				<tr>
					<td>Всего:</td>
					<td><?=$values['votes'];?></td>
					<td><?=$values['additional_votes'];?></td>
					<td><?=$values['votes_total'];?></td>
					<td><?=$values['score'];?></td>
				</tr>
			</table>
				<?if(($user['group'] != 'guest')):?>
			<input type="submit" class="input-button green" value="Посчитать заново" onClick="javascript: document.getElementById('step').setAttribute('value', '0');">
				<?endif;?>
			<?endif;?>

		</center>
	</div>
	<br>
	<div class='gbox'>
		<div class='header'>
		Цветовые обозначения
		</div>
		<center>
			<table style="text-align: center" cellspacing="5px">
				<tr>
					<td style="background-color: #113311;">Получены призовые баллы</td>
					<td>Призовых баллов нет</td>
					<td style="background-color: #882200;">Комментатор снят с конкурса</td>
				</tr>
			</table>
		</center>
	</div>
</form>