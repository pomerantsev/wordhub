<?php
	confirmLoggedIn();
	$activePage = SETTINGS_PAGE; // Пока нужно, чтобы ставить на странице правильный заголовок.
?>
<?php

	// Эти значения потом устанавливаются в processSettingsInput(), чтобы при ошибках ввода пользователю не приходилось вводить данные дважды.	
	$dailyLimit;
	$oldPasswordText = "";
	$newPasswordText = "";
	
	processSettingsInput();

?>
<!DOCTYPE HTML>
<html class = "no-js">
	<?php include(getFullPath(HEADER_PAGE_ELEMENT)); ?>
	<body>
		<div class = "container">
			<?php include(getFullPath(NAVBAR_PAGE_ELEMENT)); ?>
			<div class = "row">
				<div class = "span12">
					<?php showMessage(); ?>
					<h2>Настройки</h2>
					<form action = "<?php /*echo SETTINGS_PAGE;*/ ?>" method = "post">
						<h4>Дневной лимит на создание карточек</h4>
						<p>Его необязательно соблюдать, но это <a href = "<?php echo getLink(ABOUT_FULL_PAGE); ?>#everyDay">очень подстёгивает</a>.</p>
						<input type="number" min = "<?php echo MIN_DAILY_LIMIT; ?>" max = "<?php echo MAX_DAILY_LIMIT; ?>" step = "1" name = "dailyLimit" value = "<?php if (!isset($dailyLimit)) {echo getDailyLimit(getUserId());} else {echo $dailyLimit;} ?>" />
						<!--<h4>Отправка писем <small class = "muted">Отправляются только тем, кто подтвердил почту.</small></h4>
						<label class = "checkbox">
							<input type="checkbox" name = "subscribedToEmails" <?php echo getSubscribedToEmailsCheckedStatus(getUserId()); ?> /> Отправлять напоминания на email
						</label>
						<span class = "help-block">Они будут приходить нечасто, и только чтобы напомнить о регулярности занятий.</span>-->
						<h4>Смена пароля</h4>
						<label for = "oldPassword">Старый пароль:</label>
						<input type="password" id = "oldPassword" name = "oldPassword" value = "<?php echo $oldPasswordText; ?>" />
						<label for = "newPassword">Новый пароль:</label>
						<input type="password" id = "newPassword" name = "newPassword" value = "<?php echo $newPasswordText; ?>" />
						<span class="help-block"></span>
						<button class = "btn btn-primary btnSaveSettings" type = "submit" name = "submit" value = "Сохранить">Сохранить</button>
					</form>
				</div>
			</div>
			<div class = "push"></div>
		</div>
		<?php include(getFullPath(FOOTER_PAGE_ELEMENT)); ?>
	</body>
</html>