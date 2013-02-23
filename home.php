<?php
	$activePage = HOME_PAGE; // Пока нужно, чтобы ставить на странице правильный заголовок.
?>

<?php
	if (loggedIn()) {
		redirectTo(getLink(CREATE_PAGE));
	}
	
	processLoginInput();
	
	
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
				</div>
			</div>
			<div class = "row">
				<div class = "span9">
					<div class = "hero-unit">
						<h2>Простой, бесплатный способ учить иностранные слова</h2>
						<p>Это как бумажные карточки, только удобнее.</p>
					</div>
				</div>
				<div class = "span3">
					<a id = "login"></a>
					<h4 class = "no-margin-top">Вход на сайт</h4>
					<form id = "loginForm" action = "<?php /*echo HOME_PAGE;*/ ?>" method = "post">
						<label for = "email">Email:</label>
						<input type="email" id = "email" name = "email" value = "" style = "max-width: 100%;" required />
						<label for = "password">Пароль:</label>
						<input type="password" id = "password" name = "password" value = "" required />
						<span class="help-block"></span>
						<button class = "btn" type = "submit" name = "submit" value = "Войти">Войти</button>
						<a class = "btn btn-link registerLink" href = "<?php echo getLink(REGISTER_PAGE); ?>">Зарегистрироваться</a>
					</form>
				</div>
			</div>
			<div class = "row">
				<div class = "span4">
					<h4>Кому нужен этот сайт</h4>
					<p>Если вы:</p>
					<ul>
						<li>читаете на&nbsp;английском (или любом другом иностранном) языке,</li>
						<li>знаете язык неидеально и понимаете не&nbsp;100% текста,</li>
						<li>но&nbsp;не&nbsp;знаете, как расширить свой словарный запас,</li>
					</ul>
					<p>то&nbsp;выучивайте по&nbsp;несколько новых слов в&nbsp;день. Сайт в&nbsp;этом поможет.</p>
				</div>
				<div class = "span4">
					<h4>Что здесь происходит</h4>
					<p>Вы&nbsp;просто создаёте карточки со&nbsp;словами и&nbsp;их&nbsp;определениями.</p>
					<p>И&nbsp;потом повторяете&nbsp;их. Сайт выдаёт нужные карточки для повторения, делая упор на&nbsp;словах, которые вы хуже запоминаете.</p>
					<p>На&nbsp;сайте нет встроенных словарей, зато его легко использовать так, как вам удобно: создавать карточки на&nbsp;любых языках или учить не&nbsp;слова, а важные для вас факты (например, по&nbsp;учёбе или работе).</p>
					<p>Лучший эффект достигается, если заниматься регулярно: каждый день создавать новые карточки и&nbsp;каждый день повторять. Например, если создавать по&nbsp;30&nbsp;новых карточек в&nbsp;день (не&nbsp;так много), за&nbsp;год можно запомнить около 10&nbsp;тысяч новых слов (а&nbsp;30&nbsp;тысяч&nbsp;&mdash; это уже близко к уровню носителей языка).</p>
				</div>
				<div class = "span4">
					<h4>Почему удобно заниматься в&nbsp;интернете</h4>
					<p>То же самое можно делать с&nbsp;бумажными карточками. Но&nbsp;у&nbsp;занятий на&nbsp;сайте много преимуществ:</p>
					<ul>
						<li>Быстрее. Можно переносить слова и&nbsp;выражения с&nbsp;других веб-страниц. А&nbsp;тем, кто владеет десятипальцевым методом&nbsp;&mdash; совсем легко.</li>
						<li>Надёжнее и доступнее. Ваши карточки с&nbsp;вами везде, где есть интернет. И&nbsp;никуда не&nbsp;потеряются.</li>
						<li>Понятнее. Не&nbsp;нужно придумывать систему повторений: вы&nbsp;чаще видите те&nbsp;слова, которые вам труднее даются.</li>
						<!-- Пока сайт, к сожалению, напоминать по почте не научился.
							<li>Стабильнее. Сайт напоминает о важности регулярных занятий. Меньше шанс, что вы забудете сделать новые карточки или повторить старые.</li>-->
						<li>Дешевле. Использование сайта абсолютно бесплатно. Вам даже на&nbsp;бумагу не&nbsp;придётся тратиться :).</li>
					</ul>
				</div>
			</div>
			<div class = "row">
				<div class = "span12">
					<h4>Вы&nbsp;можете <a href = "<?php echo getLink(INTRO_PAGE); ?>">узнать о&nbsp;сайте больше</a> или <a href = "<?php echo getLink(REGISTER_PAGE); ?>">зарегистрироваться</a>.</h4>
				</div>
			</div>
					
					
			<div class = "push"></div>
		</div>
		<?php include(getFullPath(FOOTER_PAGE_ELEMENT)); ?>
	</body>
</html>
