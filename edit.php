<?php
	confirmLoggedIn();
	$activePage = EDIT_PAGE; // Пока нужно, чтобы ставить на странице правильный заголовок.
?>
<?php
	// Проверяем, было ли что-то введено, и сохраняем новую версию карточки.
	$action = EDIT_FLASHCARD;
	$newText = array();
	$newText[FRONT_TEXT] = $newText[BACK_TEXT] = "";
	processFlashcardInput($action);
	
	// Проверяем адрес страницы: существует ли такая карточка?
	$flashcard = verifyFlashcardForEditing();
	triggerEvent(EDIT_PAGE_OPENED_EVENT, getUserId());
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
						if ($flashcard != NULL) {
							include(getFullPath(INPUT_PAGE_ELEMENT));
						}
					?>
				</div>
			</div>
			<div class = "push"></div>
		</div>
		<?php include(getFullPath(FOOTER_PAGE_ELEMENT)); ?>
		<script type="text/javascript" src="http://<?php echo $_SERVER['SERVER_NAME'] . ROOT_FOLDER; ?>script/focus_script.js"></script>
	</body>
</html>