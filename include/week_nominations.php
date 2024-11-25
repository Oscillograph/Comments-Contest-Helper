<?php
if ($workspace['current_season'] !== 'none')
{
	// load commentators and links to comments
	load_commentators_and_links();

	// process forms
	if(isset($_POST['todo']) && $season_master)
	{
		// nominate a new comment
		if($_POST['todo'] == 'add')
		{
			if (isset($_POST['nickname']))
			{
				$nickname = trim($_POST['nickname']);
				$url = trim($_POST['url']);

				if (strlen($nickname) > 0)
				{
					// if the commentator is new - book'im
					if (!isset($commentators[$nickname]))
					{
						$commentators[$nickname]['score_weeks'] = [];
						$commentators[$nickname]['score_total'] = 0;
						$commentators[$nickname]['removed'] = false;
						$commentators[$nickname]['removed_date'] = $seasons[$workspace['current_season']]['starting_date'];
						$commentators_names[] = $nickname;
						mb_sort($commentators_names); // to display in alphabetic order
						$commentators_count++;
					}

					// add a new link
					if (strlen($url) > 0)
					{
						$url_exists = false;
						
						foreach ($links as $nick => $urls)
						{
							if ($url_exists)
								break;

							$urls_total = count($urls);
							for ($i = 0; $i < $urls_total; ++$i)
							{
								if ($urls[$i] == $url)
								{
									$url_exists = true;
									error_message('Этот комментарий зарегистрирован на <b>'.$nick.'</b> и уже номинирован.');
									break;
								}
							}
						}

						if (!$url_exists)
						{
							$links[$nickname][] = $url;
							$links_count++;
						}
					}

					// save data
					save_array("./data/seasons/".$workspace['current_season']."/commentators.php", $commentators);
					save_array("./data/seasons/".$workspace['current_season']."/".$week_number."-links.php", $links);
				}
			}

		}

		// delete a nominated comment
		if ($_POST['todo'] == 'delete')
		{
			// find the element to delete
			$nickname = $_POST['var1'];
			$links_index = intval($_POST['var2']);

			// delete
			if (isset($links[$nickname]))
			{
				$links_total = count($links[$nickname]);
				$links_new = [];
				$j = 0;
				for ($i = 0; $i < $links_total; ++$i)
				{
					if ($i == ($links_index))
					{
						$j++;
						continue;
					} else {
						$links_new[$i - $j] = $links[$nickname][$i];
					}
				}
				$links[$nickname] = $links_new;
			}

			// save data
			save_array("./data/seasons/".$workspace['current_season']."/".$week_number."-links.txt", $links);
		}

		// remove a commentator from being nominated
		if ($_POST['todo'] == 'remove')
		{
			// collect data
			$nickname = $_POST['nickname'];
			if (isset($commentators[$nickname]))
			{
				// remove the commentator
				$commentators[$nickname]['removed'] = true;
				$commentators[$nickname]['removed_date'] = mktime(0, 0, 0, intval(date('m')), intval(date('d')), intval(date('Y')));
				
				// remove links corresponding to the commentator
				// if (isset($links[$nickname]))
				// {
				// 	unset($links[$nickname]);
				// }

				// save data
				save_array("./data/seasons/".$workspace['current_season']."/commentators.php", $commentators);
				save_array("./data/seasons/".$workspace['current_season']."/".$week_number."-links.php", $links);
			} else {
				error_message('<div class="red">Комментатор с ником '.$nickname.' почему-то не зарегистрирован в базе.');
			}
		}

		if ($_POST['todo'] == 'bring_back')
		{
			// collect data
			$nickname = $_POST['nickname'];
			if (isset($commentators[$nickname]))
			{
				// remove the commentator
				$commentators[$nickname]['removed'] = false;
				$commentators[$nickname]['removed_date'] = mktime(0, 0, 0, intval(date('m')), intval(date('d')), intval(date('Y')));
				
				// remove links corresponding to the commentator
				// if (isset($links[$nickname]))
				// {
				// 	unset($links[$nickname]);
				// }

				// save data
				save_array("./data/seasons/".$workspace['current_season']."/commentators.php", $commentators);
				save_array("./data/seasons/".$workspace['current_season']."/".$week_number."-links.php", $links);
			} else {
				error_message('<div class="red">Комментатор с ником '.$nickname.' почему-то не зарегистрирован в базе.');
			}
		}

		// edit a link to nominated comment
		if($_POST['todo'] == 'edit')
		{
			// collect data
			$nickname = $_POST['var1'];
			$link = intval($_POST['var2']);
			$new = $_POST['var3'];

			// update
			if (isset($links[$nickname]))
			{
				if (isset($links[$nickname][$link]))
				{
					$links[$nickname][$link] = $new;
				} else {
					error_message('Комментарий под номером ('.$link.') не зарегистрирован для комментатора "'.$nickname.'".');
				}
			} else {
				error_message('Комментатор "'.$nickname.'" не обнаружен среди номинированных комментариев.');
			}

			// save data
			save_array("./data/seasons/".$workspace['current_season']."/".$week_number."-links.php", $links);
		}
	}
}
?>