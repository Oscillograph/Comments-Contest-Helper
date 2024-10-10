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


// strings to paste
$months = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];

$week_start_day = intval(date('d', $week_start));
$week_end_day = intval(date('d', $week_end));
$week_start_month = $months[intval(date('m', $week_start))-1];
$week_end_month = $months[intval(date('m', $week_end))-1];
$week_start_year = intval(date('Y', $week_start));
$week_end_year = intval(date('Y', $week_end));
?>

<div class='gbox'>
	<div class='header'>
		Разметка сообщения для голосования
	</div>

	<center>

	<table id='poll_post' style='width: 100%;' cellpadding="0" cellspacing="0">
		<tr style='display: none;'>
			<td style='border-right: 1px solid #ffff00; background-color: #002244; width: 25%; text-align: right; font-weight: 700'>
				Поле &nbsp;
			</td>
			<td style='width: 75%; text-align: justify; font-weight: 700'>
				Содержание
			</td>
		</tr>
		<tr style=''>
			<td style='border-right: 1px solid #ffff00; background-color: #002244; text-align: right; font-weight: 700'>Название темы: &nbsp;</td>
			<td><input style='width: 95%;' type='text' value='Голосование №<?=$week_number;?> на Конкурсе Комментариев на <?=$seasons[$workspace['current_season']]['name'];?>' onClick='this.select();'></td>
		</tr>
		<tr style=''>
			<td style='border-right: 1px solid #ffff00; background-color: #002244; text-align: right; font-weight: 700'>Описание темы: &nbsp;</td>
			<td><input style='width: 95%;' type='text' value='комментарии с <?=$week_start_day;?> <?=$week_start_month;?> по <?=$week_end_day;?> <?=$week_end_month;?>' onClick='this.select();'></td>
		</tr>
		<tr style=''>
			<td style='border-right: 1px solid #ffff00; background-color: #002244; text-align: right; font-weight: 700'>Вопрос голосования: &nbsp;</td>
			<td><input style='width: 95%;' type='text' value='Чьи комментарии вам понравились больше?' onClick='this.select();'></td>
		</tr>
		<tr style=''>
			<td style='border-right: 1px solid #ffff00; background-color: #002244; text-align: right; font-weight: 700'>Ники авторов: &nbsp;</td>
			<td>
<textarea style='width: 95%; height: 200px' onClick='this.select();'>
<?php

$out = '';
for ($i = 0; $i < $commentators_count; ++$i)
{
	if (!$commentators[$commentators_names[$i]]['removed'] && isset($links[$commentators_names[$i]]))
	{
		if (count($links[$commentators_names[$i]]))
			$out .= $commentators_names[$i] . "\n";
	}
}
echo trim($out);
?>
</textarea>
			</td>
		</tr>
		<tr style=''>
			<td style='border-right: 1px solid #ffff00; background-color: #002244; text-align: right; font-weight: 700'>Выбирать от 1 до 3 вариантов. &nbsp;</td>
			<td></td>
		</tr>
		<tr style=''>
			<td style='border-right: 1px solid #ffff00; background-color: #002244; text-align: right; font-weight: 700'>Текст сообщения: &nbsp;</td>
			<td>
				<textarea id='post_text' style='width: 95%; height: 400px' onClick='this.select();'>Список номинантов и ссылки на их комментарии, которые были опубликованы и номинированы пользователями форума на прошедшей неделе: с <?=$week_start_day;?> <?=$week_start_month;?> по <?=$week_end_day;?> <?=$week_end_month;?> <?=$week_end_year;?>.
Выбираем лучших, по вашему мнению, троих комментаторов.
Дополнительно можно проголосовать ещё раз, указав ник комментатора непосредственно в теме для голосования. Это может быть как один из уже отмеченных вами в опроснике, так и дополнительный — четвертый, понравившийся вам, комментатор.
За себя голосовать нельзя.

<?php
$out = '';
for ($i = 0; $i < $commentators_count; ++$i)
{
	if (!$commentators[$commentators_names[$i]]['removed'] && isset($links[$commentators_names[$i]]))
	{
		$links_count = count($links[$commentators_names[$i]]);
		if ($links_count > 0)
		{
			$out .= '[b]'.$commentators_names[$i]."[b]\n";
			for ($j = 0; $j < $links_count; ++$j)
			{
				$out .= $links[$commentators_names[$i]][$j]."\n";
			}
			$out .= "\n";
		}
	}
}
echo trim($out);
?>
</textarea>
			</td>
		</tr>
		<tr style=''>
			<td style='border-right: 1px solid #ffff00; background-color: #002244; text-align: right; font-weight: 700'>
			&nbsp;
			</td>
			<td>
			</td>
		</tr>			
		<tr style=''>
			<td style='border-right: 1px solid #ffff00; background-color: #002244; text-align: right; font-weight: 700'>Фишечки: &nbsp;</td>
			<td>
				<input type='button' class="input-button" value="Поменять ссылки с rpg-zone.ru на fancon.org/forum" onClick="javascript: fanconizeURLs();">
				<input type='button' class="input-button green" value="Освободить Биоскептика" onClick="alert('Голос учтён.');">
				<input type='button' class="input-button red" value="Найти тушёнку (ОПАСНО!)" onClick="alert('Какая беспечность! Есть мнение, что Бункер уже идёт за тобой.');">
			</td>
		</tr>		
	</table>



	</center>
</div>

<script>
	function fanconizeURLs()
	{
		obj = document.getElementById('post_text');
		obj.value = obj.value.replace(/https:\/\/rpg-zone.ru\//gi, 'https://fancon.org/forum/');
	}
</script>