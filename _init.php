<?php

/* В этом файле происходит вся инициализация: подгружается конфиг, задаются константы, подгружаются файлы с функциями, запускается сессия. */
	
// Константы корневой директории и базы данных

if (file_exists("_config.dev.php")) {
	include("_config.dev.php");
} else {
	include("_config.production.php");
}



// Функция для получения полного пути к любому из включаемых файлов (для работы серверных функций).

function getFullPath($fileName) {
	return $_SERVER['DOCUMENT_ROOT'] . ROOT_FOLDER . $fileName;
}



//Функция для получения правильной полной ссылки.

function getLink($fileName) {
	return "http://" . $_SERVER['SERVER_NAME'] . ROOT_FOLDER . $fileName . ($fileName ? "/" : "");
}



// Адреса страниц

define("HOME_PAGE", "");
define("HOME_PAGE_FILE", "home.php");

define("CREATE_PAGE", "create");
define("CREATE_PAGE_FILE", "create.php");

define("EDIT_PAGE", "edit");
define("EDIT_PAGE_FILE", "edit.php");

define("REPEAT_PAGE", "repeat");
define("REPEAT_PAGE_FILE", "repeat.php");

define("STATS_PAGE", "stats");
define("STATS_PAGE_FILE", "stats.php");

define("ADMIN_PAGE", "admin");
define("ADMIN_PAGE_FILE", "admin.php");

define("LOGOUT_PAGE", "logout");
define("LOGOUT_PAGE_FILE", "logout.php");

define("REGISTER_PAGE", "register");
define("REGISTER_PAGE_FILE", "register.php");

define("ABOUT_PAGE", "about");
define("ABOUT_PAGE_FILE", "about.php");

define("INTRO_PAGE", "intro");
define("INTRO_PAGE_FILE", "intro.php");

define("SETTINGS_PAGE", "settings");
define("SETTINGS_PAGE_FILE", "settings.php");

define("DUPLICATE_USER_PAGE", "duplicate_user");
define("DUPLICATE_USER_PAGE_FILE", "__duplicate_user.php");

define("ERROR404_PAGE", "error404");
define("ERROR404_PAGE_FILE", "error404.php");


define("FLASHCARD_TEXT_SCRIPT", "flashcard_text_script");
define("FLASHCARD_TEXT_SCRIPT_FILE", "__flashcard_text_script.php");



define("HEADER_PAGE_ELEMENT", "elements/header_element.php");
define("NAVBAR_PAGE_ELEMENT", "elements/navbar_element.php");
define("MAIN_MENU_LOGGED_IN_PAGE_ELEMENT", "elements/main_menu_logged_in_element.php");
define("MAIN_MENU_GUEST_PAGE_ELEMENT", "elements/main_menu_guest_element.php");
define("INPUT_PAGE_ELEMENT", "elements/input_element.php");
define("REPETITION_PAGE_ELEMENT", "elements/repetition_element.php");
define("ADMIN_MENU_PAGE_ELEMENT", "elements/admin_menu_element.php");
define("STATS_PAGE_ELEMENT", "elements/stats_element.php");
define("MESSAGE_PAGE_ELEMENT", "elements/message_element.php");
define("ABOUT_PAGE_ELEMENT", "elements/about_element.php");
define("FOOTER_PAGE_ELEMENT", "elements/footer_element.php");



// Файлы с функциями

define("DB_FUNCTIONS_FILE", "include/db_functions.php");
define("OUTPUT_FUNCTIONS_FILE", "include/output_functions.php");
define("USER_FUNCTIONS_FILE", "include/user_functions.php");
define("INPUT_FUNCTIONS_FILE", "include/input_functions.php");
define("FLASHCARD_FUNCTIONS_FILE", "include/flashcard_functions.php");
define("STATS_FUNCTIONS_FILE", "include/stats_functions.php");
define("MESSAGE_FUNCTIONS_FILE", "include/message_functions.php");
define("EVENT_FUNCTIONS_FILE", "include/event_functions.php");

define("PHP_ERROR_FILE", "include/php_error.php");


// Другие константы

define("MAX_REPETITIONS", 3);

define("NEW_FLASHCARDS", "newFlashcards");
define("REPEAT_FLASHCARDS", "repeatFlashcards");
define("FLASHCARDS_CREATED_TODAY", "today");
define("ALL_FLASHCARDS", "all");

define("EDIT_FLASHCARD", "edit");
define("CREATE_FLASHCARD", "create");


define("FRONT_TEXT", 'front_text');
define("BACK_TEXT", 'back_text');

define("VIEW_FRONT", 'viewFront');
define("VIEW_BACK", 'viewBack');
define("KNOW", 'know');
define("DONT_KNOW", 'dontKnow');

define("CLEAR", 'clear');

define("PROGRESS", 'progress');


define("DAY", 1);
define("WEEK", 7);
define("MONTH", 30);


define("MAX_NAME_LENGTH", 25);
define("MIN_PASSWORD_LENGTH", 6);


define("MSG_ERROR", "alert-error");
define("MSG_SUCCESS", "alert-success");
define("MSG_WARNING", "");
define("MSG_INFO", "alert-info");


define("MIN_DAILY_LIMIT", 1);
define("MAX_DAILY_LIMIT", 100);


define("RELEASE_YEAR", "2013");
define("ADMIN_EMAIL", "pomerantsevp@gmail.com");



// Все функции

require_once(getFullPath(DB_FUNCTIONS_FILE));
require_once(getFullPath(OUTPUT_FUNCTIONS_FILE));
require_once(getFullPath(USER_FUNCTIONS_FILE));
require_once(getFullPath(INPUT_FUNCTIONS_FILE));
require_once(getFullPath(FLASHCARD_FUNCTIONS_FILE));
require_once(getFullPath(STATS_FUNCTIONS_FILE));
require_once(getFullPath(MESSAGE_FUNCTIONS_FILE));
require_once(getFullPath(EVENT_FUNCTIONS_FILE));



// php_error пока не пользуюсь, он здесь стоит на всякий случай.

if (file_exists(getFullPath(PHP_ERROR_FILE))) {
	require_once(getFullPath(PHP_ERROR_FILE));
}





session_start();

mb_internal_encoding("UTF-8"); // А это, по-моему, чтобы в базе данных русский текст сохранялся не в кракозябрах.
header("Content-Type: text/html; charset=UTF-8"); // Без этой строчки на сервере все тексты отображаются в кракозябрах.
?>