<?php
	confirmLoggedIn();
	$activePage = REPEAT_PAGE; // Пока нужно, чтобы ставить на странице правильный заголовок.
?>
<?php
	processRepeatInput();
	
	// Получаем текущую статистику по обучению (сколько повторено, осталось, процент успеха), и если карточек не осталось, переадресуем на статистику.
	$repeatTodayParams = getRepeatTodayParams();
	if ($repeatTodayParams['toRepeat'] == 0) {
		if ($repeatTodayParams['repeated'] != 0) { // Повторы были
			setMessage("На сегодня повторы закончились.", MSG_SUCCESS);
			triggerEvent(REDIRECT_TO_STATS_EVENT, getUserId());
			redirectTo(getLink(STATS_PAGE));
		} else { // Повторов не было, возможно, только начинает пользоваться системой.
			setMessage("Повторять пока нечего. Первые повторы — на второй-четвёртый день после создания первых карточек.", MSG_SUCCESS);
			redirectTo(getLink(CREATE_PAGE));
		}
	}
	
	$view = getFlashcardView(); // В этой же функции выбирается новая текущая карточка, если на страницу попали извне (не перевернув текущую карточку).
	
	triggerEvent(REPEAT_PAGE_OPENED_EVENT, getUserId());
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
						include(getFullPath(REPETITION_PAGE_ELEMENT));
					?>
					<div class = "progress">
						<div class = "bar" style = "width: <?php echo $repeatTodayParams['successRate'] + $repeatTodayParams['failRate']; ?>%"></div>
					</div>
				</div>
			</div>
			<div class = "push"></div>
		</div>
		<?php include(getFullPath(FOOTER_PAGE_ELEMENT)); ?>
	</body>
	<script type="text/javascript" src="http://<?php echo $_SERVER['SERVER_NAME'] . ROOT_FOLDER; ?>script/repeat_script.js"></script>
</html>