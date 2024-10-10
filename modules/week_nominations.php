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

// process forms
if(isset($_POST['todo']))
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
								echo '<div class="red">Этот комментарий зарегистрирован на <b>'.$nick.'</b> и уже номинирован.</div>';
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
				file_put_contents("./seasons/".$workspace['current_season']."/commentators.txt", serialize($commentators));
				file_put_contents("./seasons/".$workspace['current_season']."/".$week_number."-links.txt", serialize($links));
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
		file_put_contents("./seasons/".$workspace['current_season']."/".$week_number."-links.txt", serialize($links));
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
			file_put_contents("./seasons/".$workspace['current_season']."/commentators.txt", serialize($commentators));
			file_put_contents("./seasons/".$workspace['current_season']."/".$week_number."-links.txt", serialize($links));
		} else {
			echo '<div class="red">Комментатор с ником '.$nickname.' почему-то не зарегистрирован в базе.</div>';
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
			file_put_contents("./seasons/".$workspace['current_season']."/commentators.txt", serialize($commentators));
			file_put_contents("./seasons/".$workspace['current_season']."/".$week_number."-links.txt", serialize($links));
		} else {
			echo '<div class="red">Комментатор с ником '.$nickname.' почему-то не зарегистрирован в базе.</div>';
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
		$links[$nickname][$link] = $new;

		// save data
		file_put_contents("./seasons/".$workspace['current_season']."/".$week_number."-links.txt", serialize($links));
	}
}

?>

<form id='theForm' action='?mode=week_nominations' method="POST">
	<div class='gbox'>
		<div class='header'>
			Номинировать комментатора
		</div>
		<center>
			<table>
				<tr>
					<td style="vertical-align: middle;">
						Ник: &nbsp;
					</td>
					<td style='width: 200px'>
						<input class="input-edit" type="text" id="nickname" name="nickname" value='' maxlength="32" style='width: 100%'>
					</td>
					<td style="vertical-align: middle;">
						Ссылка: &nbsp;
					</td>
					<td style='width: 400px'>
						<input class="input-edit" type="text" id="url" name="url" value='' maxlength="512" style='width: 100%'>
					</td>
					<td>
						<input class="input-button" type="submit" onClick="javascript: document.getElementById('todo').setAttribute('value', 'add');" value="Добавить">
					</td>
				</tr>
			</table>
		</center>
	</div>

	&nbsp;<br>

	<div class='gbox'>
		<div class='header'>
			Номинированные комментаторы
		</div>
		<table style='width: 100%;'>

			<?php
				// write down all commentators
				for ($commentator=0; $commentator < $commentators_count; ++$commentator)
				{ 
			?>
			<tr onmouseover="javascript: this.childNodes[1].style.backgroundColor = '#446688';"
				onmouseout="javascript: this.childNodes[1].style.backgroundColor = '#000000';">
				<td style='width: 200px; border-right: 1px solid #446688;'>
					<div style="margin-top: 0.25em; margin-left: 0.25em"> <?=$commentators_names[$commentator];?> </div>

					<br>
					<div style="margin-top: 0.25em; margin-left: 0.25em">
					<?php
						if ($commentators[$commentators_names[$commentator]]['removed'])
						{
							// echo 'Не номинируется с ' . date('d.m.Y', $commentators[$commentators_names[$commentator]]['removed_date']);
						} else {
					?>
						<input class="input-button red" type="submit" name="remove" value="Снять" onClick="javascript: if(confirm('Этот комментатор слёзно просит его не номинировать?')) { document.getElementById('todo').setAttribute('value', 'remove'); document.getElementById('nickname').setAttribute('value', '<?=$commentators_names[$commentator];?>'); } else { return false; }; ">
					</div>
					<?php
						}
					?>
				</td>
				<td>

					<?php
						// write down all links
						if ($links_count > 0)
						{
							if (isset($links[$commentators_names[$commentator]]) && !$commentators[$commentators_names[$commentator]]['removed'])
							{
								for ($link=0; $link < count($links[$commentators_names[$commentator]]); ++$link) 
								{ 
					?>
					<div class="nominated-link"
						onmouseover="javascript: this.style.backgroundColor = '#224466';"
						onmouseout="javascript: this.style.backgroundColor = '#000000';">
						<a href="<?=$links[$commentators_names[$commentator]][$link];?>" target="_blank"> <?=$links[$commentators_names[$commentator]][$link];?> </a>
						<div style="float: right;">
							<input class="input-button" type="submit" name="edit" value="Править" onClick="javascript: document.getElementById('todo').setAttribute('value', 'edit'); document.getElementById('var1').setAttribute('value', '<?=$commentators_names[$commentator];?>'); document.getElementById('var2').setAttribute('value', <?=$link;?>); var url=prompt('Введите адрес ссылки:', ''); if (!url) { return false; }; document.getElementById('var3').setAttribute('value', url);">
							<input class="input-button red" type="submit" name="delete" value="Удалить" onClick="javascript: if(confirm('Удалить эту ссылку?')) { document.getElementById('todo').setAttribute('value', 'delete'); document.getElementById('var1').setAttribute('value', '<?=$commentators_names[$commentator];?>'); document.getElementById('var2').setAttribute('value', '<?=$link;?>'); } else { return false; };">
						</div>						
					</div>
					<?php
								}
							}
						}
					?>


					<div style="text-align: center;">
						<?php
							if ($commentators[$commentators_names[$commentator]]['removed'])
							{
								echo 'Снят с Конкурса Комментариев ' . date('d.m.Y', $commentators[$commentators_names[$commentator]]['removed_date']);
						?>
						<input class="input-button green" type="submit" name="bring_back" value="Вернуть" onClick="javascript: if(confirm('Но ведь комментатор слёзно просит не номинировать!')) { document.getElementById('todo').setAttribute('value', 'bring_back'); document.getElementById('nickname').setAttribute('value', '<?=$commentators_names[$commentator];?>'); } else { return false; }; ">
						<?php
							} else {
						?>
						<br>
						<input class="input-button" type="submit" name="new" value="Добавить ссылку" onClick="javascript: document.getElementById('todo').setAttribute('value', 'add'); document.getElementById('nickname').setAttribute('value', '<?=$commentators_names[$commentator];?>'); var url=prompt('Введите адрес ссылки:', ''); if (!url) { return false; }; document.getElementById('url').setAttribute('value', url);">
						<?php
							}
						?>
					</div>
				</td>
			</tr>

			<tr>
				<td colspan="2">
					<hr>
				</td>
			</tr>
			<?php
				}
			?>

		</table>
	</div>


	<input type="hidden" name="todo" id="todo" value="">
	<input type="hidden" name="var1" id="var1" value="">
	<input type="hidden" name="var2" id="var2" value="">
	<input type="hidden" name="var3" id="var3" value="">
</form>