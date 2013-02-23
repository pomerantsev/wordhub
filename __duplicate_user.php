<?php
	confirmAdminLoggedIn();
	$activePage = DUPLICATE_USER_PAGE; // Пока нужно, чтобы ставить на странице правильный заголовок.
?>
<?php
	processDuplicateUserInput();
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
					
					<h2>Копирование пользователя</h2>
					<p>(права администратора не копируются).</p>
					
					<form action = "<?php /*echo DUPLICATE_USER_PAGE;*/ ?>" method = "post">
						<label for = "userId">Какого пользователя копировать:</label>
						<select id = "userId" name = "userId"><?php echo getAllUsersForOutput(); ?></select>
						<label for = "email">Новый email:</label>
						<input type = "email" id = "email" name = "email" required />
						<label for = "password">Новый пароль:</label>
						<input type = "password" id = "password" name = "password" required />
						<span class="help-block"></span>
						<button class = "btn btn-primary" type = "submit" name = "submit">Копировать</button>
					</form>
				</div>
			</div>
			<div class = "push"></div>
		</div>
		<?php include(getFullPath(FOOTER_PAGE_ELEMENT)); ?>
	</body>
</html>