<a class = "brand" href = "<?php echo getLink(HOME_PAGE); ?>">WordHub</a>

<ul class="nav pull-right">
	<li class = "<?php echo getMenuItemClass($activePage, INTRO_PAGE) . " " . getMenuItemClass($activePage, ABOUT_PAGE); ?>">
		<a href="<?php echo getLink(INTRO_PAGE); ?>">О сайте</a>
	</li>
	<li>
		<a href="<?php echo getLink(HOME_PAGE); ?>#login">Вход</a>
	</li>
	
</ul>