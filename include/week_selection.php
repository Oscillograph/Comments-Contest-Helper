<?php
if ($workspace['current_season'] !== 'none')
{
	if (isset($_POST['week_selector_todo']))
	{
		$selected_week = $_POST['select_week'];
		if ($selected_week)
		{
			$_SESSION['current_week'] = intval($selected_week);
			$workspace['current_week'] = $_SESSION['current_week'];
			header('location: ?mode=' . $mode . '&season='.$season . '&week='.$selected_week);
		}
	}

	$week_selector_form_action = '?mode=' . $mode . (($season)?'&season='.$season : '&season='.$workspace['current_season']) . (($week)?'&week='.$week : '&week='.$workspace['current_week']);
}
?>