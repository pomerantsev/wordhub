<div class="navbar">
	<div class="navbar-inner">		
		<?php
			if (loggedIn()) {
				include(getFullPath(MAIN_MENU_LOGGED_IN_PAGE_ELEMENT));
			} else {
				include(getFullPath(MAIN_MENU_GUEST_PAGE_ELEMENT));
			}
		?>
		
		
	</div>
</div>