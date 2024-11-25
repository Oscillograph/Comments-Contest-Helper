<?php

// load commentators and links to comments
load_commentators_and_links();

$season_start = $seasons[$workspace['current_season']]['starting_date'];
$season_end = $seasons[$workspace['current_season']]['ending_date'];
$values['weeks_count'] = ceil(($season_end - $season_start) / $week_length);

$values['winners'] = [];

if ($commentators)
{
	foreach ($commentators as $nickname => $array)
	{
		$commentators[$nickname]['score_total'] = 0;
		if (!isset($array['score_weeks']))
		{
			$commentators[$nickname]['score_weeks'] = [];
		}

		$commentators[$nickname]['score_total'] = 0;
		for ($i = 1; $i <= $values['weeks_count']; ++$i)
		{
			if (isset($commentators[$nickname]['score_weeks'][$i]))
			{
				$commentators[$nickname]['score_total'] += $commentators[$nickname]['score_weeks'][$i];
			}
		}
		$values['winners'][] = [$nickname, $commentators[$nickname]['score_total']];
	}
}

$values['winners_count'] = count($values['winners']);
if ($values['winners_count'] > 0)
{
	sort_winners($values['winners']);
}

// collect stats
$values['comments_total'] = 0;
$values['votes_total'] = 0;
$values['commentators_stats'] = []; // key - nickname; value = array('comments'=>, 'votes'=>)
$values['most_comments'] = [];
$values['most_comments'][0] = array(
	'nickname'	=>	'',
	'total'		=>	0
);
$values['most_votes'] = [];
$values['most_votes'][0] = array(
	'nickname'	=>	'',
	'total'		=>	0
);

for ($i = 0; $i < 14; ++$i)
{
	$links = load_array("./data/seasons/".$workspace['current_season']."/".$i."-links.php");
	if ($links)
	{
		foreach($links as $key => $value)
		{
			$values['comments_total'] += count($value);
			if (isset($values['commentators_stats'][$key]['comments']))
			{
				$values['commentators_stats'][$key]['comments'] += count($value);
			} else {
				$values['commentators_stats'][$key]['comments'] = count($value);
			}
		}

		foreach($values['commentators_stats'] as $key => $value)
		{
			if (isset($values['commentators_stats'][$key]['comments']))
			{
				if ($values['commentators_stats'][$key]['comments'] > $values['most_comments'][0]['total'])
				{
					$values['most_comments'] = array();
					$values['most_comments'][0]['nickname'] = $key;
					$values['most_comments'][0]['total'] = $values['commentators_stats'][$key]['comments'];
				}

				if ($values['commentators_stats'][$key]['comments'] == $values['most_comments'][0]['total'])
				{
					$commentator_is_counted = false;
					for ($j = 0; $j < count($values['most_comments']); ++$j)
					{
						if ($key === $values['most_comments'][$j]['nickname'])
						{
							$commentator_is_counted = true;
						}
					}
					if (!$commentator_is_counted)
					{
						$next = count($values['most_comments']);
						$values['most_comments'][$next]['nickname'] = $key;
						$values['most_comments'][$next]['total'] = $values['commentators_stats'][$key]['comments'];
					}
				}
			}
		}
	}

	$results = load_array("./data/seasons/".$workspace['current_season']."/".$i."-results.php");
	if ($results)
	{
		foreach($results as $key => $value)
		{
			if (isset($value['votes_total']))
			{
				$values['votes_total'] += $value['votes_total'];
			}

			if (isset($results[$key]['votes_total']))
			{
				if (isset($values['commentators_stats'][$key]['votes']))
				{
					$values['commentators_stats'][$key]['votes'] += $results[$key]['votes_total'];
				} else {
					$values['commentators_stats'][$key]['votes'] = $results[$key]['votes_total'];
				}
			} else {
				$values['commentators_stats'][$key]['votes'] = 0;
			}
		}

		foreach($values['commentators_stats'] as $key => $value)
		{
			if ($values['commentators_stats'][$key]['votes'] > $values['most_votes'][0]['total'])
			{
				$values['most_votes'] = array();
				$values['most_votes'][0]['nickname'] = $key;
				$values['most_votes'][0]['total'] = $values['commentators_stats'][$key]['votes'];
			}

			if ($values['commentators_stats'][$key]['votes'] == $values['most_votes'][0]['total'])
			{
				$commentator_is_counted = false;
				for ($j = 0; $j < count($values['most_votes']); ++$j)
				{
					if ($key === $values['most_votes'][$j]['nickname'])
					{
						$commentator_is_counted = true;
					}
				}
				if (!$commentator_is_counted)
				{
					$next = count($values['most_votes']);
					$values['most_votes'][$next]['nickname'] = $key;
					$values['most_votes'][$next]['total'] = $values['commentators_stats'][$key]['votes'];
				}
			}
		}
	}
}

?>