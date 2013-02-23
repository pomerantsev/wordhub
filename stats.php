<?php
	confirmLoggedIn();
	$activePage = STATS_PAGE; // Пока нужно, чтобы ставить на странице правильный заголовок.
	
	triggerEvent(STATS_PAGE_OPENED_EVENT, getUserId());
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
					<?php include(getFullPath(STATS_PAGE_ELEMENT)); ?>
				</div>
			</div>
			<div class = "row">
				<div class = "span12">
					<?php echo getFlashcardList(); ?>
				</div>
			</div>
			<div class = "push"></div>
		</div>
		<?php include(getFullPath(FOOTER_PAGE_ELEMENT)); ?>
	</body>
</html>