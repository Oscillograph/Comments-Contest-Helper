<?php

// load commentators and links to comments
load_commentators_and_links();

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
$results = load_array("./data/seasons/".$workspace['current_season']."/".$week_number."-results.php");

if ($results)
{
	if (!isset($_POST['step']))
	{
		$step = 4;
	}
} else {
	$results = [];
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

if ($user['group'] != 'guest')
{
	// upload forum poll results
	if ($step == 0)
	{
	}

	// add additional votes
	if ($step == 1)
	{
		if ($step == 1)
		{
			$results = [];
			$poll_results = explode("\n", trim($_POST['poll_results']));
			for ($i = 0; $i < count($poll_results); ++$i)
			{
				$commentator_results = explode("\t", $poll_results[$i]);
				$nickname = trim($commentator_results[0]);
				$results[$nickname]['votes'] = $commentator_votes = intval(mb_substr(trim($commentator_results[1]), 1));
				$results[$nickname]['additional_votes'] = 0;
				$results[$nickname]['votes_total'] = 0;
				$results[$nickname]['score'] = 0;
			}
			save_array("./data/seasons/".$workspace['current_season']."/".$week_number."-results.php", $results);
		}
	}

	// verify
	if ($step == 2)
	{
		for ($i = 0; $i < count($commentators_names); ++$i)
		{
			if (isset($_POST[$i]))
			{
				$add_votes = intval($_POST[$i]);
				$results[$commentators_names[$i]]['additional_votes'] = $add_votes;
				$results[$commentators_names[$i]]['votes_total'] = $results[$commentators_names[$i]]['votes'] + $results[$commentators_names[$i]]['additional_votes'];
			}
		}
		save_array("./data/seasons/".$workspace['current_season']."/".$week_number."-results.php", $results);
	}

	// save
	if ($step == 3)
	{
		// make sure we overwrite results previously calculated for this week
		foreach ($commentators as $nickname => $array)
		{
			unset($commentators[$nickname]['score_weeks'][$week_number]);
			$results[$nickname]['score'] = 0;
		}

		// proceed with saving actual results of this week
		// get a sorted array of commentators from highest votes to lowest
		$commentators_votes_total = [];
		for ($i = 0; $i < count($commentators_names); ++$i)
		{
			if (isset($results[$commentators_names[$i]]) && !$commentators[$commentators_names[$i]]['removed'] && isset($results[$commentators_names[$i]]['votes_total']))
			{
				$commentators_votes_total[$i] = $results[$commentators_names[$i]]['votes_total'];
			}
		}
		rsort($commentators_votes_total, SORT_NUMERIC);
		
		$scores = [];
		switch (count($results))
		{
			case 4:
			{
				$scores = [3, 2, 1, 1];
			} break;
			case 3:
			{
				$scores = [3, 2, 1];
			} break;
			case 2:
			{
				$scores = [3, 2];
			} break;
			case 1:
			{
				$scores = [3];
			} break;

			default:
			{
				$scores = [3, 2, 1, 1, 1];
			}
		}
		$places = [];

		$excluded = []; // to make sure this is not an infinite cycle
		while (count($places) < count($scores))
		{
			$max = 0; // most votes giving right to take the place
			$contestants = []; // contestants per place

			foreach($results as $nickname => $array)
			{
				if (!isset($excluded[$nickname]) && isset($array['votes_total']))
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

			if (count($contestants) > 0)
			{
				$score = 0;
				if (count($results) >= count($places))
				{
					for ($i = 0, $j = count($places); ($i < count($contestants)) && ($j < count($scores)); ++$i, ++$j)
					{
						$score += $scores[$j];
					}
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
			} else {
				// no contestants for the place - no need to continue the cycle
				break;
			}
		}

		// save the results
		save_array("./data/seasons/".$workspace['current_season']."/".$week_number."-results.php", $results);
		save_array("./data/seasons/".$workspace['current_season']."/commentators.php", $commentators);

		// proceed with view
		$step = 4;
	}
}

// view
if ($step == 4)
{
	// array of commentators who won any score of the week
	$values['winners'] = [];

	foreach($results as $nickname => $array)
	{
		if (isset($array['votes_total']) && isset($array['score']))
		{
			// if (($array['votes_total'] > 0) && ($array['score'] > 0))
			{
				$values['winners'][] = [$nickname, $array['votes_total']];
			}
		}
	}

	// sort winners
	sort_winners($values['winners']);
	$values['winners_total'] = count($values['winners']);

	// collect sums
	$values['votes'] = 0;
	foreach ($results as $nickname => $array) {
		if (isset($array['votes']))
		{
			$values['votes'] += intval($array['votes']);
		}
	}
	$values['additional_votes'] = 0;
	foreach ($results as $nickname => $array) {
		if (isset($array['additional_votes']))
		$values['additional_votes'] += intval($array['additional_votes']);
	}
	$values['votes_total'] = 0;
	foreach ($results as $nickname => $array) {
		if (isset($array['votes_total']))
			$values['votes_total'] += intval($array['votes_total']);
	}
	$values['score'] = 0;
	foreach ($results as $nickname => $array) {
		if (isset($array['score']))
			$values['score'] += $array['score'];
	}
}

// edit
if ($user['group'] != 'guest')
{
	if ($step == 5)
	{
	}
}
?>