<?php if (!defined('CCH')) die('Этот скрипт не может работать самостоятельно.'); ?>

<?if($workspace['current_season']!=='none'):?>
<form id='theForm' action='?mode=week_nominations' method="POST">
	<?if($season_master):?>
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
	<?endif;?>

	<div class='gbox'>
		<div class='header'>
			Номинированные комментаторы
		</div>
		<table style='width: 100%;'>

			<?for ($commentator=0; $commentator < $commentators_count; ++$commentator):?>
			<tr onmouseover="javascript: this.childNodes[1].style.backgroundColor = '#446688';"
				onmouseout="javascript: this.childNodes[1].style.backgroundColor = '#000000';">
				<td style='width: 200px; border-right: 1px solid #446688;'>
					<div id="<?=$commentators_names[$commentator];?>" style="margin-top: 0.25em; margin-left: 0.25em"><a href='#<?=$commentators_names[$commentator];?>'><?=$commentators_names[$commentator];?></a></div>

					<br>
					<div style="margin-top: 0.25em; margin-left: 0.25em">
					<?php
						if ($commentators[$commentators_names[$commentator]]['removed'])
						{
							// echo 'Не номинируется с ' . date('d.m.Y', $commentators[$commentators_names[$commentator]]['removed_date']);
						} else {
					?>
					<?if($user['group'] != 'guest'):?>
						<input class="input-button red" type="button" name="remove" value="Снять" onClick="form_submitter('remove', '<?=$commentators_names[$commentator];?>');">
					<?endif;?>
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
					<?if($user['group'] != 'guest'):?>
						<div style="float: right;">
							<input class="input-button" type="button" name="edit" value="Править" onClick="form_submitter('edit', '<?=$commentators_names[$commentator];?>', <?=$link;?>);">
							<input class="input-button red" type="button" name="delete" value="Удалить" onClick="form_submitter('delete', '<?=$commentators_names[$commentator];?>', <?=$link;?>);">
						</div>						
					<?endif;?>
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
						<?if($user['group'] != 'guest'):?>
						<input class="input-button green" type="button" name="bring_back" value="Вернуть" onClick="form_submitter('bring_back', '<?=$commentators_names[$commentator];?>');">
						<?endif;?>
						<?php
							} else {
						?>
						<br>
						<?if($user['group'] != 'guest'):?>
						<input class="input-button" type="button" name="new" value="Добавить ссылку" onClick="form_submitter('add', '<?=$commentators_names[$commentator];?>');">
						<?endif;?>
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
			<?endfor;?>

		</table>
	</div>


	<input type="hidden" name="todo" id="todo" value="">
	<input type="hidden" name="var1" id="var1" value="">
	<input type="hidden" name="var2" id="var2" value="">
	<input type="hidden" name="var3" id="var3" value="">
</form>

<script>
function form_submitter(todo, var1, var2, var3)
{
	theForm = document.getElementById('theForm');
	theForm.setAttribute('action', theForm.getAttribute('action') + '#' + var1);
	switch (todo)
	{
		// links
		case 'add': // var1 = nickname
		{
			document.getElementById('todo').setAttribute('value', 'add'); 
			document.getElementById('nickname').setAttribute('value', var1); 
			var url=prompt(var1 + ': введите адрес ссылки:', ''); 
			if (!url) return false;
			document.getElementById('url').setAttribute('value', url);
			theForm.submit();
			break;
		}
		case 'edit': // var1 = nickname; var2 = link id
		{
			document.getElementById('todo').setAttribute('value', 'edit'); 
			document.getElementById('var1').setAttribute('value', var1);
			document.getElementById('var2').setAttribute('value', var2); 
			var url=prompt(var1 + ': ведите адрес ссылки:', ''); 
			if (!url) return false; 
			document.getElementById('var3').setAttribute('value', url);
			theForm.submit();
			break;
		}
		case 'delete': // var1 = nickname, var2 = link id
		{
			if(confirm(var1 + ': удалить эту ссылку?'))
			{
				document.getElementById('todo').setAttribute('value', 'delete'); 
				document.getElementById('var1').setAttribute('value', var1);
				document.getElementById('var2').setAttribute('value', var2);
			} else { 
				return false; 
			}
			theForm.submit();
			break;
		}

		// commentators
		case 'remove': // var1 = nickname
		{
			if (confirm(var1 + ' слёзно просит не номинировать?'))
			{
				document.getElementById('todo').setAttribute('value', 'remove'); 
				document.getElementById('nickname').setAttribute('value', var1); 
			} else { 
				return false; 
			}
			theForm.submit();
			break;
		}
		case 'bring_back': // var1 = nickname
		{
			if(confirm('Но ведь ' + var1 + ' слёзно просит не номинировать!'))
			{ 
				document.getElementById('todo').setAttribute('value', 'bring_back');
				document.getElementById('nickname').setAttribute('value', var1);
			} else {
				return false;
			}
			theForm.submit();
			return true;

			break;
		}
	}
	return false;
}
</script>
<?endif;?>