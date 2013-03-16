<?php
	$activePage = INTRO_PAGE; // Пока нужно, чтобы ставить на странице правильный заголовок.
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
					<h2>О сайте</h2>
					<?php include(getFullPath(ABOUT_PAGE_ELEMENT)); ?>
					<ol>
						<li><a href = "<?php echo getLink(ABOUT_PAGE); ?>#whatToRead">Читайте интересные тексты</a> и&nbsp;подчёркивайте слова, в переводе которых не уверены.</li>
						<li>Вооружитесь <a href = "<?php echo getLink(ABOUT_PAGE); ?>#dictionary">словарём</a>&nbsp;&mdash; и&nbsp;создавайте карточки. Регулярно. <a href = "<?php echo getLink(ABOUT_PAGE); ?>#everyDay">Каждый день</a>.</li>
						<li>И&nbsp;повторяйте. Тоже <a href = "<?php echo getLink(ABOUT_PAGE); ?>#everyDay">каждый день</a>.</li>
						<li>Никаких тестов. Видите слово&nbsp;&mdash; просто отвечайте &laquo;Помню&raquo; или &laquo;Не&nbsp;помню&raquo;. <a href = "<?php echo getLink(ABOUT_PAGE); ?>#selfControl">Вы&nbsp;сами контролируете свои знания</a>.</li>
						<li>Можно подсматривать. На&nbsp;результате это не&nbsp;скажется. Всё равно выучите.</li>
						<li>Чтобы слово запомнилось, достаточно повторить его несколько раз через <a href = "<?php echo getLink(ABOUT_PAGE); ?>#leitner">увеличивающиеся промежутки времени</a>.</li>
						<li>Три раза подряд отвечаете, что помните слово&nbsp;&mdash; и&nbsp;оно считается выученным. Это не&nbsp;сразу, а&nbsp;<a href = "<?php echo getLink(ABOUT_PAGE); ?>#leitner">на&nbsp;протяжении месяца</a> :).</li>
						<li>Чтобы лучше запоминалось, записывайте слово вместе с&nbsp;контекстом.</li>
						<li>Один раз в&nbsp;день повторили старые слова&nbsp;&mdash; и&nbsp;до&nbsp;следующего дня об&nbsp;этом можно забыть. <a href = "<?php echo getLink(ABOUT_PAGE); ?>#leitner">Всё равно всё запомните</a>.</li>
						<li>Через месяц увидите, как вырос ваш словарный запас. Достаточно каждый день вписывать на&nbsp;карточки <a href = "<?php echo getLink(ABOUT_PAGE); ?>#howMany"></a>несколько новых слов</a>. Остальное&nbsp;&mdash; дело техники.</li>
					</ol>
					<h4>И&nbsp;всё это&nbsp;&mdash; совершенно бесплатно.</h4>
				</div>
			</div>
			<div class = "push"></div>
		</div>
		<?php include(getFullPath(FOOTER_PAGE_ELEMENT)); ?>
	</body>
</html>