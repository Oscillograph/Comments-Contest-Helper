<?php if (!defined('CCH')) die('Этот скрипт не может работать самостоятельно.'); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<head>
<?php echo $html['meta'];?>
	<title><?=$html['title'];?></title>
</head>


<body>
<?php echo $html['header'];?>
<?php echo $html['error'];?>
<?php echo $html['content'];?>
<?php echo $html['footer'];?>
</body>

<?php echo $html['scripts'];?>

</html>