<?php if (!defined('CCH')) die('Этот скрипт не может работать самостоятельно.'); ?>
<div class='headbar'>
	<ul class='mainmenu'>
		<?if(authorized()):?>
		<li class='nav-item'>Добро пожаловать, <b><?=$user['login'];?>!</b></li>
		<li class='nav-item'><a href='./'>Главная</a>
			<ul class='submenu'>
				<li class='nav-item'><a href="?mode=settings&section=seasons">Настройки</a></li>
				<li class='nav-item'><a href="?mode=settings&section=edit_user&userid=<?=$user['userid'];?>">Профиль</a></li>
				<li><hr></li>
				<li class='nav-item'><a href='?mode=logout'>Выход</a></li>
			</ul>
		</li>
		<?else:?>
		<li class='nav-item'><a href='#'>Войти</a>
			<form id='theForm' action='?mode=login' method='POST' style='padding: 0px; margin: 0px'>
				<div class='submenu'>
					<div class='gbox'>
						<div class='header'>
							Форма входа
						</div>
						<div style='text-align: center'>
							Введите логин: <br>
							<input type='text' class='input-edit' name='login' value=''><br>
							Введите пароль: <br>
							<input type='text' class='input-edit' name='password' value=''>
							<?if($config['open_registration']):?>
							<br>
							<input type='checkbox' value=1 name='register' id='register_checkbox'><label for='register_checkbox'> зарегистрироваться</label>
							<?endif;?>
							<br> &nbsp; <br>
							<input type='submit' class='input-button green' name='submit' value='Войти'>
						</div>
					</div>
				</div>
			</form>
		</li>
		<?endif;?>

		<li class='nav-item'><a href='#'>Конкурс комментариев</a>
			<ul class='submenu'>
				<li class='nav-item'><a href='?mode=seasons'>Выбрать сезон</a></li>
				<?if($season_master):?>
				<li class='nav-item'><a href="?mode=settings&season=<?=$workspace['current_season'];?>">Настройки сезона</a></li>
				<?endif;?>
				<li class='nav-item'><a href='?mode=week_nominations&season=<?=$workspace['current_season'];?>&week=<?=$workspace['current_week'];?>'>Номинации недели</a></li>
				<?if($season_master):?>
				<li class='nav-item'><a href='?mode=week_post&season=<?=$workspace['current_season'];?>&week=<?=$workspace['current_week'];?>'>Пост недели</a></li>
				<?endif;?>
				<li class='nav-item'><a href='?mode=week_results&season=<?=$workspace['current_season'];?>'>Итоги недели</a></li>
				<li class='nav-item'><a href='?mode=season_results&season=<?=$workspace['current_season'];?>'>Результаты конкурса</a></li>
			</ul>
		</li>
	</ul>
</div>
<br>