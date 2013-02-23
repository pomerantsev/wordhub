<?php
	confirmLoggedIn();
	$activePage = CREATE_PAGE; // Пока нужно, чтобы ставить на странице правильный заголовок.
?>
<?php
	// Сейчас только производится действие, а нужно ещё сообщение об успехе или ошибке выводить.
	$action = CREATE_FLASHCARD;
	$newText = array();
	$newText[FRONT_TEXT] = $newText[BACK_TEXT] = "";
	$newFrontText = $newBackText = "";
	processFlashcardInput($action);
	triggerEvent(CREATE_PAGE_OPENED_EVENT, getUserId());
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
					<?php 
						$flashcard = NULL; // Это значение дальше передаётся в функцию, возвращающую текст текущей карточки или, как здесь, ничего (для создания новой карточки).
						include(getFullPath(INPUT_PAGE_ELEMENT));
					?>
				</div>
			</div>
			<div class = "push"></div>
		</div>
		<?php include(getFullPath(FOOTER_PAGE_ELEMENT)); ?>
		<script type="text/javascript" src="http://<?php echo $_SERVER['SERVER_NAME'] . ROOT_FOLDER; ?>script/focus_script.js"></script>
	</body>
	
</html>