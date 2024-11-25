<?php if (!defined('CCH')) die('Этот скрипт не может работать самостоятельно.'); 
$html['title'] = 'Настройки'; ?>

<?if(authorized()):?>
<table id='content-table'>
	<tr>
		<td style='width: 300px'>
			<div class='gbox'>
				<div class='header'>
					Меню
				</div>
				<ul>
					<li><a href='?mode=settings&section=seasons'>Мои сезоны</a>
					<li><a href="?mode=settings&section=edit_user&userid=<?=$user['userid'];?>">Мой профиль</a>
				</ul>

				<?if($user['group']=='admin'):?>
				<hr>
				<ul>
					<li><a href='?mode=settings&section=users'>Пользователи</a>
					<li><a href='?mode=settings&section=system'>Система</a>
				</ul>
				<?endif;?>
			</div>
		</td>
		<td>
			<div class='gbox'>
				<?if($section == 'seasons'):?>
					<?if(!$season):?>
					<center>
						<div class='header'>
						Сезоны, в которых я ведущий:
						</div>
						<hr>
						<?foreach($seasons as $key => $value):?>
							<?if(($user['group'] == 'admin') || isset($user['seasons'][$key])):?>
								<a href="?mode=settings&season=<?=$key;?>" class="input-button<?=(($key==$config['current_season'])?' red':'');?>" style='padding: 0.25em; text-decoration: none'><?=$value['name'];?></a>
							<?endif;?>
						<?endforeach;?>

						<?if($user['group'] == 'admin'):?>
						<hr>
							<div style='display: inline-block;'>
								<form action='?mode=settings&section=seasons' method="POST">
									<input type="hidden" name= "season_select" value="new_season">
									<input type="button" class="input-button green" value="Начать новый сезон" onClick="javascript: document.getElementById('season_type').parentNode.style.display = 'inline-block';">
									<div style='display: none;'>
										<select id='season_type' name='season_type'>
											<option value='vpf'>Весенний ПФ</option>
											<option value='opf'>Осенний ПФ</option>
											<option value='sf'>Открытый Конкурс НФ</option>
										</select>
										<select id='season_year' name='season_year'>
										<?for($i = intval(date('Y'))-50; $i < intval(date('Y'))+50; ++$i):?>
											<option value=<?=$i;?><?=(($i == intval(date('Y'))) ? ' selected':'');?>><?=$i;?></option>
										<?endfor;?>
										</select>
										<input type="submit" class="input-button green" name="submit" value="Создать">
									</div>
								</form>
							</div>
						<?endif;?>
					</center>
					<?endif;?>
				<?endif;?>

				<?if($season):?>
					<?if($season_master):?>
						<center>
							<div class='header'>Настройки сезона</div>
							<hr>
							<form action='?mode=settings&section=seasons&season=<?=$season;?>' method='POST'>
								<table style='width: 100%;'>
									<tr>
										<td width='50%'></td>
										<td width='50%'></td>
									</tr>
									<tr>
										<td style='text-align: right'>
										Название:
										</td>
										<td>
											<input type='text' name='season_name' value="<?=$seasons[$season]['name'];?>">
										</td>
									</tr>
									<tr>
										<td style='text-align: right'>
										Дата начала:
										</td>
										<td>
											<select id='new_starting_day' name='new_starting_day'>
												<?for ($i=31; $i>0; $i--):?>
												<option value='<?=$i;?>' <?=($i == intval(date('d', $seasons[$season]['starting_date']))) ? 'selected' : '';?>><?=$i;?></option>
												<?endfor;?>
											</select>
											<select id='new_starting_month' name='new_starting_month'>
												<?for ($i=12; $i>0; $i--):?>
												<option value='<?=$i;?>' <?=($i == intval(date('m', $seasons[$season]['starting_date']))) ? 'selected' : '';?>><?=$i;?></option>
												<?endfor;?>
											</select>
											<select id='new_starting_year' name='new_starting_year'>
												<?for ($i=intval(date('Y'))+50; $i>(intval(date('Y')) - 50); $i--):?>
												<option value='<?=$i;?>' <?=($i == intval(date('Y', $seasons[$season]['starting_date']))) ? 'selected' : '';?>><?=$i;?></option>
												<?endfor;?>
											</select>
										</td>
									</tr>
									<tr>
										<td style='text-align: right'>
										Дата окончания:
										</td>
										<td>
											<select id='new_ending_day' name='new_ending_day'>
												<?for ($i=31; $i>0; $i--):?>
												<option value='<?=$i;?>' <?=($i == intval(date('d', $seasons[$season]['ending_date']))) ? 'selected' : '';?>><?=$i;?></option>
												<?endfor;?>
											</select>
											<select id='new_ending_month' name='new_ending_month'>
												<?for ($i=12; $i>0; $i--):?>
												<option value='<?=$i;?>' <?=($i == intval(date('m', $seasons[$season]['ending_date']))) ? 'selected' : '';?>><?=$i;?></option>
												<?endfor;?>
											</select>
											<select id='new_ending_year' name='new_ending_year'>
												<?for ($i=intval(date('Y'))+50; $i>(intval(date('Y')) - 50); $i--):?>
												<option value='<?=$i;?>' <?=($i == intval(date('Y', $seasons[$season]['ending_date']))) ? 'selected' : '';?>><?=$i;?></option>
												<?endfor;?>
											</select>
										</td>
									</tr>
									<tr>
										<td style='text-align: right'>
										Статус:
										</td>
										<td>
										<select id='season_closed' name='season_closed'>
											<option value='0'<?=(($seasons[$season]['closed'] != true)? ' selected':'')?>>Открыт</option>
											<option value='1'<?=(($seasons[$season]['closed'] == true)? ' selected':'')?>>Закрыт</option>
										</select>
										</td>
									</tr>
									<tr>
										<td style='text-align: right'>
										Ведущий:
										</td>
										<td>
											<select id='season_user' name='season_user'>
												<option value='-1'> -без ведущего- </option>
												<?for($i=0; $i<count($users); ++$i):?>
													<option value='<?=$i;?>' <?=(($i == $seasons[$season]['user'])?'selected':'');?>><?=$users[$i]['login'];?></option>
												<?endfor;?>
											</select>
										</td>
									</tr>
									<tr>
										<td colspan=2 style='text-align: center'>
											<hr>
										</td>
									</tr>
									<tr>
										<td colspan=2 style='text-align: center'>
											<input type='submit' class='input-button green' value='Сохранить'>
										</td>
									</tr>
								</table>
							</form>
							
						</center>
					<?endif;?>
				<?endif;?>

				<?if($section == 'edit_user'):?>
					<?if($userid && ($userid != 0)):?>
						<?if(isset($users[$userid])):?>
						<div class='header'>
							Настройки профиля - <?=$user['login'];?>
						</div>
						<hr>
						<form action="?mode=settings&section=edit_user&userid=<?=$userid;?>" id='edit_user_form' method='POST'>
							<table style='width: 100%;'>
								<tr>
									<td width='50%'></td>
									<td width='50%'></td>
								</tr>
								<tr>
									<td style='text-align: right'>Логин:</td>
									<td><input type='text' value="<?=$users[$userid]['login'];?>" name='login'></td>
								</tr>
								<tr>
									<td style='text-align: right'>Пароль:</td>
									<td><input type='text' value="" name='password'> &nbsp; <input type='checkbox' value=1 name='change_password'>изменить пароль</td>
								</tr>
								<tr>
									<td colspan=2 style='text-align: center'>
										<hr>
										<input type='submit' class='input-button green' value='Сохранить'> &nbsp;
										<input type='submit' class='input-button red' value='Удалить' onclick='javascript: return checkDecision("Действительно хотите удалить пользователя?", "delete", "1");'>
										<input type='hidden' id='delete' name='delete' value='0'>
									</td>
								</tr>
							</table>
						</form>
						<?else:?>
						<center></center>
						<?endif;?>
					<?else:?>
					<center>Профиль администратора нужно редактировать в разделе настроек системы.</center>
					<?endif;?>
				<?endif;?>

				<?if($section == 'users'):?>
					<?if($user['group'] == 'admin'):?>
					<div class='header'>
						Зарегистрированные пользователи
					</div>
					<hr>
					<center>| 
						<?for($i = 0; $i < count($users); ++$i):?>
						<a href="?mode=settings&section=edit_user&userid=<?=$i;?>"><?=$users[$i]['login'];?></a> | 
						<?endfor;?>
					</center>
					<?else:?>
					<center>Только администратор может просматривать список пользователей.</center>
					<?endif;?>
				<?endif;?>

				<?if($section == 'system'):?>
				<div class='header'>
					Настройки системы
				</div>
					<hr>
					<form action='?mode=settings&section=system' method='POST'>
						<table style='width: 100%;'>
							<tr>
								<td width='50%'></td>
								<td width='50%'></td>
							</tr>
							<tr>
								<td style='text-align: right'>Логин админа:</td>
								<td><input type='text' value="<?=$config['admin_login'];?>" name='admin_login'></td>
							</tr>
							<tr>
								<td style='text-align: right'>Пароль админа:</td>
								<td><input type='text' value="<?=$config['admin_password'];?>" name='admin_password'></td>
							</tr>
							<tr>
								<td style='text-align: right'>Регистрация:</td>
								<td>
									<select name='open_registration'>
										<option value=0 <?=((!$config['open_registration'])?'selected':'');?>>Закрыта</option>
										<option value=1 <?=(($config['open_registration'])?'selected':'');?>>Открыта</option>
									</select>
								</td>
							</tr>
							<tr>
								<td style='text-align: right'>Стиль оформления:</td>
								<td>
									<select name='skin'>
									<?foreach($values['skins'] as $skin_name):?>
										<option value="<?=$skin_name;?>" <?=(($skin_name == $config['skin'])?'selected':'')?>><?=$skin_name;?></option>
									<?endforeach;?>
									</select>
								</td>
							</tr>
							<tr>
								<td style='text-align: right'>Сезон по умолчанию:</td>
								<td>
									<select name='current_season'>
										<?foreach($seasons as $key => $value):?>
										<option value="<?=$key;?>" <?=(($key == $config['current_season'])?'selected':'');?>><?=$seasons[$key]['name'];?></option>
										<?endforeach;?>
									</select>
								</td>
							</tr>
							<tr>
								<td colspan=2 style='text-align: center'>
									<hr>
									<input type='submit' class='input-button green' value='Сохранить'>
								</td>
							</tr>
						</table>
						<input type='hidden' name='admin_password_reset' value="<?=$config['admin_password_reset'];?>">
					</form>
				<?endif;?>
			</div>
		</td>
	</tr>
</table>
<?else:?>
<? error_message('Доступ запрещён.'); ?>
<?endif;?>

<script>
function checkDecision(message, findElement, storeValue)
{
	if (confirm(message))
	{
		document.getElementById(findElement).setAttribute('value', storeValue);
		return true;
	} else {
		return false;
	}
}
</script>