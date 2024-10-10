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


// if there are no results saved:
// steps:
// 0 - upload forum poll results
// 1 - additional votes
// 2 - verify
// 3 - save
// 4 - view
// 5 - edit
$step = 0;
if (isset($_POST['step']))
{
	$step = intval($_POST['step']);
}

// results scheme:
// nickname (key) { votes | additional votes (step2) | votes total (step3) | score }
$results = [];

if (is_file("./seasons/".$workspace['current_season']."/".$week_number."-results.txt"))
{
	$results = unserialize(file_get_contents("./seasons/".$workspace['current_season']."/".$week_number."-results.txt"));
	if (!isset($_POST['step']))
	{
		$step = 4;
	}
} else {
	for ($i = 0; $i < count($commentators_names); ++$i)
	{
		if (!$commentators[$commentators_names[$i]]['removed'] && isset($links[$commentators_names[$i]]))
		{
			if (count($links[$commentators_names[$i]]) > 0)
			{
				$results[$commentators_names[$i]]['votes'] = 0;
				$results[$commentators_names[$i]]['additional_votes'] = 0;
				$results[$commentators_names[$i]]['votes_total'] = 0;
				$results[$commentators_names[$i]]['score'] = 0;
			}
		}
	}
}
?>

<form action='?mode=week_results' method='POST' id='theForm'>
	<div class='gbox'>
		<div class='header'>
			Итоги недели
		</div>
		<center>
			<input type='hidden' id='step' name='step' value='<?=($step+1);?>'>

<?php
if ($step != 4)
{
	// upload forum poll results
	if ($step == 0)
	{
?>

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

<?php
	}

	// add additional votes
	if ($step == 1)
	{
		$poll_results = explode("\n", trim($_POST['poll_results']));
		for ($i = 0; $i < count($poll_results); ++$i)
		{
			$commentator_results = explode("\t", $poll_results[$i]);
			$nickname = trim($commentator_results[0]);
			$results[$nickname]['votes'] = $commentator_votes = intval(mb_substr(trim($commentator_results[1]), 1));
		}
		file_put_contents("./seasons/".$workspace['current_season']."/".$week_number."-results.txt", serialize($results));

?>
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

<?php
		for ($i = 0; $i < count($commentators_names); ++$i)
		{
			if (!$commentators[$commentators_names[$i]]['removed'] && isset($links[$commentators_names[$i]]))
			{
				if (count($links[$commentators_names[$i]]) > 0)
				{
?>
		<tr>
			<td><?=$commentators_names[$i];?></td>
			<td><?=$results[$commentators_names[$i]]['votes'];?></td>
			<td><input style='width: 3em;' type="text" name='<?=$i;?>' value='0'></td>
			<td><?=$results[$commentators_names[$i]]['votes_total'];?></td>
			<td><?=$results[$commentators_names[$i]]['score'];?></td>
		</tr>

<?php
				}
			}
		}
?>
	</table>
	<input type="submit" class="input-button green" value="Сохранить дополнительные голоса">
<?php
	}

	// verify
	if ($step == 2)
	{
?>
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
<?php
		for ($i = 0; $i < count($commentators_names); ++$i)
		{
			if (isset($_POST[$i]))
			{
				$add_votes = intval($_POST[$i]);
				$results[$commentators_names[$i]]['additional_votes'] = $add_votes;
				$results[$commentators_names[$i]]['votes_total'] = $results[$commentators_names[$i]]['votes'] + $results[$commentators_names[$i]]['additional_votes'];
?>

		<tr>
			<td><?=$commentators_names[$i];?></td>
			<td><?=$results[$commentators_names[$i]]['votes'];?></td>
			<td><?=$results[$commentators_names[$i]]['additional_votes'];?></td>
			<td><?=$results[$commentators_names[$i]]['votes_total'];?></td>
			<td><?=$results[$commentators_names[$i]]['score'];?></td>
		</tr>

<?php
			}
		}
		file_put_contents("./seasons/".$workspace['current_season']."/".$week_number."-results.txt", serialize($results));
?>
	</table>
	<input type="submit" class="input-button green" value="Утвердить результаты">
<?php
	}

	// save
	if ($step == 3)
	{
		$commentators_votes_total = [];
		for ($i = 0; $i < count($commentators_names); ++$i)
		{
			if (isset($results[$commentators_names[$i]]) && !$commentators[$commentators_names[$i]]['removed'] && isset($results[$commentators_names[$i]]['votes_total']))
			{
				$commentators_votes_total[$i] = $results[$commentators_names[$i]]['votes_total'];
			}
		}
		rsort($commentators_votes_total, SORT_NUMERIC);
		
		$scores = [3, 2, 1 , 1, 1];
		$places = [];

		$excluded = []; // to make sure this is not an infinite cycle
		while (count($places) < 5)
		{
			$max = 0;
			$contestants = [];

			foreach($results as $nickname => $array)
			{
				if (!isset($excluded[$nickname]) && !$commentators[$nickname]['removed'] && isset($array['votes_total']))
				{
					if ($array['votes_total'] > $max)
					{
						// update max
						$max = $array['votes_total'];
						// reset $contestants and save the current one
						$contestants = [];
						$contestants[$nickname] = $max;
					}

					if ($array['votes_total'] == $max)
					{
						// add a contestant for the place
						$contestants[$nickname] = $max;
					}
				}
			}

			// calculate how much score each contestant gets
			$score = 0;
			for ($i = 0, $j = count($places); ($i < count($contestants)) && ($j < 5); ++$i, ++$j)
			{
				$score += $scores[$j];
			}
			$score = round(100*$score / count($contestants))/100;

			// grant scores to contestants
			foreach($contestants as $nickname => $value)
			{
				$places[$nickname] = $score;
				$results[$nickname]['score'] = $score;
				$commentators[$nickname]['score_weeks'][$week_number] = $score;

				// and make sure they are no longer in the next cycles
				$excluded[$nickname] = $max;
			}
		}

		// save the results
		file_put_contents("./seasons/".$workspace['current_season']."/".$week_number."-results.txt", serialize($results));
		file_put_contents("./seasons/".$workspace['current_season']."/commentators.txt", serialize($commentators));

		// now, let's display the results
?>
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
<?php
		foreach($results as $nickname => $array)
		{
			if (!$commentators[$nickname]['removed'] && isset($array['score']))
			{
				if ($array['score'] > 0)
				{
?>
		<tr>
			<td><?=$nickname;?></td>
			<td><?=$results[$nickname]['votes'];?></td>
			<td><?=$results[$nickname]['additional_votes'];?></td>
			<td><?=$results[$nickname]['votes_total'];?></td>
			<td><?=round(100*$results[$nickname]['score'])/100;?></td>
		</tr>
<?php
				}
			}
		}
?>
	</table>
	<input type="submit" class="input-button green" value="Посчитать заново" onClick="javascript: document.getElementById('step').setAttribute('value', '0');">


<?php
	}

	// edit
	if ($step == 5)
	{
?>



<?php
	}
} else {
	// step 4 - view
?>
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
<?php
		// array of commentators who won any score of the week
		$winners = [];

		foreach($results as $nickname => $array)
		{
			if (!$commentators[$nickname]['removed'] && isset($array['votes_total']) && isset($array['score']))
			{
				if (($array['votes_total'] > 0) && ($array['score'] > 0))
				{
					$winners[] = [$nickname, $array['votes_total']];
				}
			}
		}

		// sort winners
		sort_winners($winners);
		$winners_total = count($winners);
		for ($i = 0; $i < $winners_total; ++$i)
		{
			$nickname = $winners[$i][0];
?>
		<tr>
			<td><?=$nickname;?></td>
			<td><?=$results[$nickname]['votes'];?></td>
			<td><?=$results[$nickname]['additional_votes'];?></td>
			<td><?=$results[$nickname]['votes_total'];?></td>
			<td><?=round(100*$results[$nickname]['score'])/100;?></td>
		</tr>
<?php
		}
?>

	</table>
	<input type="submit" class="input-button green" value="Посчитать заново" onClick="javascript: document.getElementById('step').setAttribute('value', '0');">

<?php
}
?>
	
		</center>
	</div>
</form>