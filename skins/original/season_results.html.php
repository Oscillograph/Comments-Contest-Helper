<?php if (!defined('CCH')) die('Этот скрипт не может работать самостоятельно.'); ?>

<div class='gbox'>
	<div class='header'>
		Результаты конкурса комментариев
	</div>

	<center>
	<?if(!$seasons[$season]['closed']):?>
	<table style='width: 100%' cellspacing="0">
		<tr style='background-color: #224488'>
			<td rowspan=2 style='vertical-align: middle; text-align: center; width: 15%'>Ник</td>
			<td colspan=<?=$values['weeks_count'];?> style='text-align: center;'>Недели</td>
			<td rowspan=2 style='vertical-align: middle; text-align: center; width: 3em;'>Итог</td>
		</tr>
		<tr>
		<?for($i = 1; $i <= $values['weeks_count']; ++$i):?>
			<td style='border-bottom: 1px solid #224488; <?=(($i != $values['weeks_count']) ? " border-right: 1px solid #224488; ":"");?> text-align: center; width: 3em;'><a href='?mode=week_results&season=<?=$season;?>&week=<?=$i;?>'><?=$i;?></a></td>
		<?endfor;?>
		</tr>

		<?for($i = 0; $i < $values['winners_count']; ++$i):?>
		<tr onmouseover="javascript: this.style.backgroundColor = '#224466';"
			onmouseout="javascript: this.style.backgroundColor = '#000000';">
			<td style='text-align: center;'><?=$values['winners'][$i][0];?></td>
			<?for($j = 1; $j <= $values['weeks_count']; ++$j):?>
			<td style=" <?=(($j <= $values['weeks_count']) ? 'border-left: 1px solid #224488; ' : '');?> text-align: center;">
				<?if(isset($commentators[$values['winners'][$i][0]]['score_weeks'][$j])):?>
					<?=round(100*$commentators[$values['winners'][$i][0]]['score_weeks'][$j])/100;?>
				<?else:?>
				&nbsp;.&nbsp;
				<?endif;?>
			</td>
			<?endfor;?>
			<td style='border-left: 1px solid #224488; text-align: center;'><?=round(100*$commentators[$values['winners'][$i][0]]['score_total'])/100;?></td>
		</tr>
		<?endfor;?>
	</table>
	<?else:?>
	Скрыты ведущим.
	<?endif;?>
	</center>
</div>

<br>
<div class='gbox'>
	<div class='header'>
		Статистика сезона
	</div>
	<center>
	<?if(!$seasons[$season]['closed']):?>
	<p>Участников конкурса комментариев: <?=(($commentators)?count($commentators):0);?><br>
	Номинировалось комментариев: <?=$values['comments_total'];?><br>
	Отдано голосов: <?=$values['votes_total'];?><br>
	Самый номинируемый комментатор:
	<?for($i = 0; $i < count($values['most_comments']); ++$i):?>
		<b><?=$values['most_comments'][$i]['nickname'];?></b> (комментариев: <?=$values['most_comments'][$i]['total'];?>)<?=(($i == (count($values['most_comments']) - 1)) ? '' : ', ');?>
	<?endfor;?><br>
	Самый популярный комментатор: <b><?=$values['most_votes'][0]['nickname'];?></b> (всего голосов: <?=$values['most_votes'][0]['total'];?>)
	<p>
	<?else:?>
	Скрыта ведущим.
	<?endif;?>
	</center>

</div>