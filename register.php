<?php
	$activePage = REGISTER_PAGE; // Пока нужно, чтобы ставить на странице правильный заголовок.
?>
<?php
	if (loggedIn()) {
		redirectTo(getLink(CREATE_PAGE));
	}

	// Эти значения потом устанавливаются в processRegisterInput(), чтобы при ошибках ввода пользователю не приходилось вводить данные дважды.	
	$emailText = "";
	$passwordText = "";
	$nameText = "";
	
	processRegisterInput();
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
					<h2>Регистрация</h2>
					<form action = "<?php /*echo REGISTER_PAGE;*/ ?>" method = "post">
						<label for = "email">Email: <span class = "text-error">*</span></label>
						<input type="email" id = "email" name = "email" value = "<?php echo $emailText; ?>" placeholder = "@" required />
						<span class = "help-block">Мы ни с кем не будем делиться вашим адресом.</span>
						<label for = "password">Пароль: <span class = "text-error">*</span></label>
						<input type="password" id = "password" name = "password" value = "<?php echo $passwordText; ?>" required />
						<span class = "help-block">Любая комбинация цифр (0-9) и латинских букв (a-z, A-Z) без пробелов, не короче 6 символов. РеГисТР имеет значение.</span>
						<label for = "name">Имя:</label>
						<input type="text" id = "name" name = "name" value = "<?php echo $nameText; ?>" />
						<span class = "help-block">Можете указать, как к вам обращаться (не длиннее 25 символов).</span>
						<span class="help-block"></span>
						<button class = "btn btn-primary" type = "submit" name = "submit" value = "Зарегистрироваться">Зарегистрироваться</button>
					</form>
				</div>
			</div>
			<div class = "push"></div>
		</div>
		<?php include(getFullPath(FOOTER_PAGE_ELEMENT)); ?>
	</body>
</html>