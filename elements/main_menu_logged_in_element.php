<a class="btn btn-navbar fat-menu-center no-js-hide" data-toggle="collapse" data-target=".nav-collapse">
	<span class="icon-bar"></span>
	<span class="icon-bar"></span>
	<span class="icon-bar"></span>
</a>

<ul id = "elementBeforeDropdown" class = "nav pull-left">
	<li class = "<?php echo getMenuItemClass($activePage, CREATE_PAGE); ?>">
		<a href="<?php echo getLink(CREATE_PAGE); ?>" class = "<?php echo getStateClass(NEW_FLASHCARDS); ?>">
			<span>Создать</span>
			<span><small><?php echo getFlashcardCountAsString(NEW_FLASHCARDS); ?></small></span>
		</a>
	</li>
	<?php
		updateFlashcardsToRepeatTodayCount(); // Чтобы при любом обновлении страницы (а эта функция вызывается при построении меню) показывалось правильное количество карточек, которые нужно выучить сегодня. Без этого вызова при наличии карточек может всегда показываться 0 (ничего повторить нельзя).
		//Вообще очень плохо, что view непосредственно обновляет model. Нужно будет всё переструктурировать.
	?>
	<li class = "<?php echo getMenuItemClass($activePage, REPEAT_PAGE); ?>">
		<a href="<?php echo getLink(REPEAT_PAGE); ?>" class = "<?php echo getStateClass(REPEAT_FLASHCARDS); ?>">
			<span>Повторить</span>
			<span><small><?php echo getFlashcardCountAsString(REPEAT_FLASHCARDS); ?></small>
		</a>
	</li>
</ul>



<!-- Этот ul Яваскриптом обрамляется в div, чтобы в случае, если Яваскрипт не работает, меню работало, хоть и выглядело бы некрасиво. -->
	<ul id = "dropdownMenu" class="fat-menu-center nav pull-right"> <!-- Класс fat-menu-center - чтобы пункты меню в "толстом" меню не были приклеены к верху, а появлялись посередине. -->
		<li class = "<?php echo getMenuItemClass($activePage, STATS_PAGE); ?>">
			<a href="<?php echo getLink(STATS_PAGE); ?>">Статистика и все карточки</a>
		</li>
		<li class = "<?php echo getMenuItemClass($activePage, ABOUT_PAGE) . " " . getMenuItemClass($activePage, ABOUT_FULL_PAGE); ?>">
			<a href="<?php echo getLink(ABOUT_PAGE); ?>">О сайте</a>
		</li>
		
		<!-- Дропдаун подсвечивается как активный, когда выбран один из пунктов меню, входящих в него. -->
		<li id = "userDropdownMenu" class="no-js-hide dropdown <?php echo getMenuItemClass($activePage, SETTINGS_PAGE) . " " . getMenuItemClass($activePage, ADMIN_PAGE); ?>">
			<a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php echo getUserNameForOutput(getUserId()); ?> <b class="caret"></b></a>
			<ul class="dropdown-menu">

				<?php
					if (isAdmin()) {
						include(getFullPath(ADMIN_MENU_PAGE_ELEMENT));
					}
				?>
				<li class = "<?php echo getMenuItemClass($activePage, SETTINGS_PAGE); ?>">
					<a href="<?php echo getLink(SETTINGS_PAGE); ?>">Настройки</a>
				</li>
				<li class="divider"></li>
				<li>
					<a href="<?php echo getLink(LOGOUT_PAGE); ?>">Выйти</a>
				</li>
			</ul>
		</li>
		<li id = "userMenuNoDropdown">
			<ul class = "nav">
				<?php
					if (isAdmin()) {
						include(getFullPath(ADMIN_MENU_PAGE_ELEMENT));
					}
				?>
				<li class = "<?php echo getMenuItemClass($activePage, SETTINGS_PAGE); ?>">
					<a href="<?php echo getLink(SETTINGS_PAGE); ?>">Настройки</a>
				</li>
				<li class = "divider"></li>
				<li>
					<a href="<?php echo getLink(LOGOUT_PAGE); ?>">Выйти</a>
				</li>
			</ul>
		</li>
	</ul>