<?php

// load commentators and links to comments
if (is_file("./seasons/".$workspace['current_season']."/commentators.txt"))
{
	$commentators = unserialize(file_get_contents("./seasons/".$workspace['current_season']."/commentators.txt"));
	foreach ($commentators as $key => $value)
	{
		$commentators_names[] = $key;
	}
	mb_sort($commentators_names); // to display in alphabetic order
}
if (is_file("./seasons/".$workspace['current_season']."/".$week_number."-links.txt"))
{
	$links = unserialize(file_get_contents("./seasons/".$workspace['current_season']."/".$week_number."-links.txt"));
}

// count how many commentators we have this season
$commentators_count = count($commentators_names);
// count how many links were nominated this week
$links_count = count($links);

?>

<div class='gbox'>
	<div class='header'>
		Результаты конкурса комментариев
	</div>

	<center>
	<table style='width: 100%' cellspacing="0">
		<tr style='background-color: #224488'>
			<td rowspan=2 style='vertical-align: middle; text-align: center; width: 15%'>Ник</td>
			<td colspan=14 style='text-align: center;'>Недели</td>
			<td rowspan=2 style='vertical-align: middle; text-align: center; width: 3em;'>Итог</td>
		</tr>
		<tr>
			<td style='border-bottom: 1px solid #224488; border-right: 1px solid #224488; text-align: center; width: 3em;'>1</td>
			<td style='border-bottom: 1px solid #224488; border-right: 1px solid #224488; text-align: center; width: 3em;'>2</td>
			<td style='border-bottom: 1px solid #224488; border-right: 1px solid #224488; text-align: center; width: 3em;'>3</td>
			<td style='border-bottom: 1px solid #224488; border-right: 1px solid #224488; text-align: center; width: 3em;'>4</td>
			<td style='border-bottom: 1px solid #224488; border-right: 1px solid #224488; text-align: center; width: 3em;'>5</td>
			<td style='border-bottom: 1px solid #224488; border-right: 1px solid #224488; text-align: center; width: 3em;'>6</td>
			<td style='border-bottom: 1px solid #224488; border-right: 1px solid #224488; text-align: center; width: 3em;'>7</td>
			<td style='border-bottom: 1px solid #224488; border-right: 1px solid #224488; text-align: center; width: 3em;'>8</td>
			<td style='border-bottom: 1px solid #224488; border-right: 1px solid #224488; text-align: center; width: 3em;'>9</td>
			<td style='border-bottom: 1px solid #224488; border-right: 1px solid #224488; text-align: center; width: 3em;'>10</td>
			<td style='border-bottom: 1px solid #224488; border-right: 1px solid #224488; text-align: center; width: 3em;'>11</td>
			<td style='border-bottom: 1px solid #224488; border-right: 1px solid #224488; text-align: center; width: 3em;'>12</td>
			<td style='border-bottom: 1px solid #224488; border-right: 1px solid #224488; text-align: center; width: 3em;'>13</td>
			<td style='border-bottom: 1px solid #224488; text-align: center; width: 3em;'>14</td>
		</tr>

<?php
	$winners = [];
	foreach ($commentators as $nickname => $array)
	{
		$commentators[$nickname]['score_total'] = 0;
		if (!isset($array['score_weeks']))
		{
			$commentators[$nickname]['score_weeks'] = [];
		}

		$weeks_count = count($array['score_weeks']);
		$weeks_count = 14; // костыль - устанавливаем максимум недель для цикла, потому что у некоторых комментаторов в массиве score_weeks индексы могут начинаться не с нуля. TODO: исправить
		$commentators[$nickname]['score_total'] = 0;
		for ($i = 0; $i < $weeks_count; ++$i)
		{
			if (isset($commentators[$nickname]['score_weeks'][$i+1]))
			{
				$commentators[$nickname]['score_total'] += $commentators[$nickname]['score_weeks'][$i+1];
			}
		}
		$winners[] = [$nickname, $commentators[$nickname]['score_total']];
	}

	$winners_count = count($winners);
	if ($winners_count > 0)
	{
		sort_winners($winners);
		for ($i = 0; $i < $winners_count; ++$i)
		{
			?>
			<tr onmouseover="javascript: this.style.backgroundColor = '#224466';"
				onmouseout="javascript: this.style.backgroundColor = '#000000';">
			<?php
			echo '<td style=\'text-align: center;\'>'.$winners[$i][0].'</td>';
			for ($j = 0; $j < 14; ++$j)
			{
				echo '<td style=\'' . (($j < 14) ? 'border-left: 1px solid #224488; ' : '') . 'text-align: center;\'>';
				if (isset($commentators[$winners[$i][0]]['score_weeks'][$j+1]))
				{
					 echo (round(100*$commentators[$winners[$i][0]]['score_weeks'][$j+1])/100);
				} else {
					echo ' . ';
				}
				echo '</td>';
			}
			echo '<td style=\'border-left: 1px solid #224488; text-align: center;\'>'.(round(100*$commentators[$winners[$i][0]]['score_total'])/100).'</td>';
			?>
			</tr>
			<?php
		}
	}
?>
	</table>
	</center>
</div>

<?php

// collect stats
$comments_total = 0;
$votes_total = 0;
$commentators_stats = []; // key - nickname; value = array('comments'=>, 'votes'=>)
$most_comments = [];
$most_comments[0] = array(
	'nickname'	=>	'',
	'total'		=>	0
);
$most_votes = [];
$most_votes[0] = array(
	'nickname'	=>	'',
	'total'		=>	0
);

for ($i = 0; $i < 14; ++$i)
{
	if (is_file("./seasons/".$workspace['current_season']."/".$i."-links.txt"))
	{
		$links = unserialize(file_get_contents("./seasons/".$workspace['current_season']."/".$i."-links.txt"));

		foreach($links as $key => $value)
		{
			$comments_total += count($value);
			if (isset($commentators_stats[$key]['comments']))
			{
				$commentators_stats[$key]['comments'] += count($value);
			} else {
				$commentators_stats[$key]['comments'] = count($value);
			}
		}

		foreach($commentators_stats as $key => $value)
		{
			if (isset($commentators_stats[$key]['comments']))
			{
				if ($commentators_stats[$key]['comments'] > $most_comments[0]['total'])
				{
					$most_comments = array();
					$most_comments[0]['nickname'] = $key;
					$most_comments[0]['total'] = $commentators_stats[$key]['comments'];
				}

				if ($commentators_stats[$key]['comments'] == $most_comments[0]['total'])
				{
					$commentator_is_counted = false;
					for ($j = 0; $j < count($most_comments); ++$j)
					{
						if ($key === $most_comments[$j]['nickname'])
						{
							$commentator_is_counted = true;
						}
					}
					if (!$commentator_is_counted)
					{
						$next = count($most_comments);
						$most_comments[$next]['nickname'] = $key;
						$most_comments[$next]['total'] = $commentators_stats[$key]['comments'];
					}
				}
			}
		}
	}

	if (is_file("./seasons/".$workspace['current_season']."/".$i."-results.txt"))
	{
		$results = unserialize(file_get_contents("./seasons/".$workspace['current_season']."/".$i."-results.txt"));

		foreach($results as $key => $value)
		{
			if (isset($value['votes_total']))
			{
				$votes_total += $value['votes_total'];
			}

			if (isset($results[$key]['votes_total']))
			{
				if (isset($commentators_stats[$key]['votes']))
				{
					$commentators_stats[$key]['votes'] += $results[$key]['votes_total'];
				} else {
					$commentators_stats[$key]['votes'] = $results[$key]['votes_total'];
				}
			} else {
				$commentators_stats[$key]['votes'] = 0;
			}
		}

		foreach($commentators_stats as $key => $value)
		{
			if ($commentators_stats[$key]['votes'] > $most_votes[0]['total'])
			{
				$most_votes = array();
				$most_votes[0]['nickname'] = $key;
				$most_votes[0]['total'] = $commentators_stats[$key]['votes'];
			}

			if ($commentators_stats[$key]['votes'] == $most_votes[0]['total'])
			{
				$commentator_is_counted = false;
				for ($j = 0; $j < count($most_votes); ++$j)
				{
					if ($key === $most_votes[$j]['nickname'])
					{
						$commentator_is_counted = true;
					}
				}
				if (!$commentator_is_counted)
				{
					$next = count($most_votes);
					$most_votes[$next]['nickname'] = $key;
					$most_votes[$next]['total'] = $commentators_stats[$key]['votes'];
				}
			}
		}
	}
}

?>

&nbsp;<br>

<div class='gbox'>
	<div class='header'>
		Статистика сезона
	</div>
	<center>
	<p>Участников конкурса комментариев: <?=count($commentators);?><br>
	Номинировалось комментариев: <?=$comments_total;?><br>
	Отдано голосов: <?=$votes_total;?><br>
	Самый номинируемый комментатор: <?php
	for ($i = 0; $i < count($most_comments); ++$i)
	{
		echo '<b>'.$most_comments[$i]['nickname'].'</b> (комментариев: ' . $most_comments[$i]['total']. ')' . (($i == (count($most_comments) - 1)) ? '' : ', ');
	}
	?><br>
	Самый любимый комментатор: <b><?=$most_votes[0]['nickname'];?></b> (всего голосов: <?=$most_votes[0]['total'];?>)
	<p>
	</center>

</div>