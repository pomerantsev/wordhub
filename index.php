<?php
	require_once("_init.php");
	$request = $_SERVER['REQUEST_URI'];
	if (!preg_match('/^' . str_replace('/', '\\/', preg_quote(ROOT_FOLDER)) . '.*/', $request)) {
		setMessage("Неправильно задан корневой каталог для переадресации в Apache. Постараемся разобраться как можно быстрее.", MSG_ERROR);
		include(getFullPath(ERROR404_PAGE_FILE));
	} else {
		$request = substr($request, strlen(ROOT_FOLDER));
		$pageName = "";
		$_GET['params'] = "";
		$firstSlashPos = strpos($request, '/');
		if ($firstSlashPos === FALSE) {
			$pageName = $request;
		} else {
			$pageName = substr($request, 0, $firstSlashPos);
			$_GET['params'] = substr($request, $firstSlashPos + 1);
			$paramsLength = strlen($_GET['params']);
			if ($_GET['params'][$paramsLength - 1] == '/') {
				$_GET['params'] = substr($_GET['params'], 0, $paramsLength - 1);
			}
		}
		if ($pageName == HOME_PAGE) {
			include(getFullPath(HOME_PAGE_FILE));
		} else if ($pageName == CREATE_PAGE) {
			include(getFullPath(CREATE_PAGE_FILE));
		} else if ($pageName == EDIT_PAGE) {
			include(getFullPath(EDIT_PAGE_FILE));
		} else if ($pageName == REPEAT_PAGE) {
			include(getFullPath(REPEAT_PAGE_FILE));
		} else if ($pageName == STATS_PAGE) {
			include(getFullPath(STATS_PAGE_FILE));
		} else if ($pageName == ADMIN_PAGE) {
			include(getFullPath(ADMIN_PAGE_FILE));
		} else if ($pageName == LOGOUT_PAGE) {
			include(getFullPath(LOGOUT_PAGE_FILE));
		} else if ($pageName == REGISTER_PAGE) {
			include(getFullPath(REGISTER_PAGE_FILE));
		} else if ($pageName == ABOUT_PAGE) {
			include(getFullPath(ABOUT_PAGE_FILE));
		} else if ($pageName == ABOUT_FULL_PAGE) {
			include(getFullPath(ABOUT_FULL_PAGE_FILE));
		} else if ($pageName == SETTINGS_PAGE) {
			include(getFullPath(SETTINGS_PAGE_FILE));
		} else if ($pageName == DUPLICATE_USER_PAGE) {
			include(getFullPath(DUPLICATE_USER_PAGE_FILE));
		} else if ($pageName == FLASHCARD_TEXT_SCRIPT) {
			include(getFullPath(FLASHCARD_TEXT_SCRIPT_FILE));
		} else {
			include(getFullPath(ERROR404_PAGE_FILE));
		}
		
	}
	
?>