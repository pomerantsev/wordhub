<a class = "brand" href = "<?php echo getLink(HOME_PAGE); ?>">Вордхаб</a>

<ul class="nav pull-right">
	<li class = "<?php echo getMenuItemClass($activePage, ABOUT_PAGE) . " " . getMenuItemClass($activePage, ABOUT_FULL_PAGE); ?>">
		<a href="<?php echo getLink(ABOUT_PAGE); ?>">О сайте</a>
	</li>
	<li>
		<a href="<?php echo getLink(HOME_PAGE); ?>#login">Вход</a>
	</li>
	
</ul>