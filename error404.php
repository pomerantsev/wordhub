<?php
	$activePage = ERROR404_PAGE; // Пока нужно, чтобы ставить на странице правильный заголовок.
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
					<h2>Ошибка 404</h2>
					<p>Вы не туда попали. Такой страницы на сайте нет. Попробуйте нажать на один из пунктов меню.</p>
					<p>Если вы уверены, что на эту страницу вы перешли по ссылке, напишите об этой ошибке на <a href = "mailto:<?php echo ADMIN_EMAIL; ?>"><?php echo ADMIN_EMAIL; ?></a></p>
				</div>
			</div>
			<div class = "push"></div>
		</div>
		<?php include(getFullPath(FOOTER_PAGE_ELEMENT)); ?>
	</body>
</html>