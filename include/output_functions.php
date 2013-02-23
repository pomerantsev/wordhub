<?php

/* Здесь собраны вперемешку функции, что-то выводящие пользователю. */


// Вспомогательная функция, возвращающая нужное падежное окончание для существительного или прилагательного, идущего за числительным.
function caseEnding($count, $ending1, $ending2, $ending5) {
	if ($count % 10 == 1 && $count % 100 != 11) {
		return $ending1;
	} elseif (($count % 10 == 2 && $count % 100 != 12) || ($count % 10 == 3 && $count % 100 != 13) || ($count % 10 == 4 && $count % 100 != 14)) {
		return $ending2;
	} else {
		return $ending5;
	}
}



// Вспомогательная функция для редиректа.
function redirectTo($address) {
	header('Location: ' . $address);
	exit;
}



// Вспомогательная функция, выполняющая проверку, совпадает ли адрес, заданный в адресной строке, с адресом страницы.
/*function pageEquals($browserAddress, $pageAddress) {
	$matches = array();
	if (preg_match_all('/(^' . str_replace('/', '\\/', $pageAddress) . ')(\/|$)/', $browserAddress, $matches)) {
		setMessage($matches[1][0]);
		return true;
	} else {
		return false;
	}
}*/


// Функция, выдающая (или не выдающая) класс в HTML для пометки пунктов меню как значимых (осталось сделать сегодня).
function getStateClass($flashcardSet) {
	if ($flashcardSet == NEW_FLASHCARDS) {
		if (sqlGetFlashcardsCreatedTodayCount(getUserId()) < getDailyLimit(getUserId())) {
			return "toDo";
		} else {
			return "";
		}
	} elseif ($flashcardSet == REPEAT_FLASHCARDS) {
		if (getFlashcardsToRepeatTodayCount() > 0) {
			return "toDo";
		} else {
			return "inactive";
		}
	} else {
		throw "Неизвестный параметр";
	}
}



// Функция выдаёт для пунктов меню "Создать" и "Повторить" количество карточек в виде строки.
function getFlashcardCountAsString($flashcardSet) {
	if ($flashcardSet == NEW_FLASHCARDS) {
		return sqlGetFlashcardsCreatedTodayCount(getUserId()) . " из " . getDailyLimit(getUserId()); // "Сколько нужно создать карточек сегодня" - задаётся в виде константы. Лучшего решения пока не нашёл.
	} elseif ($flashcardSet == REPEAT_FLASHCARDS) {
		$repeatTodayParams = getRepeatTodayParams(); // Не очень оптимально: довольно много вычислений внутри функции ради всего двух параметров.
		return $repeatTodayParams['repeated'] . " из " . ($repeatTodayParams['repeated'] + $repeatTodayParams['toRepeat']);
	} else {
		throw "Неизвестный параметр";
	}
}



// Функция выдаёт список всех карточек в виде HTML. Хочется в будущем от неё в таком виде избавиться, не мешать HTML с PHP.
function getFlashcardList() {
	$result = "";
	$flashcardsArray = sqlSelectAllFlashcards(getUserId(), "DESC");
	$flashcardCount = mysql_num_rows($flashcardsArray);
	// Выводим список всех нужных карточек (если он не пуст), отображаются передняя и задняя сторона каждой.
	if ($flashcardCount > 0) {
		$result = "<div class = \"\"><h2>Все карточки</h2></div>";
		$today = date("Y-m-d");
		$lastFlashcardCreatedDate = $today . "1"; // Чтобы первая же дата была меньше и чтобы текст "Сегодня" был выведен.
		while ($flashcard = mysql_fetch_array($flashcardsArray)) {
			if ($flashcard['created_date'] < $lastFlashcardCreatedDate) {
				if ($flashcard['created_date'] == $today) {
					$result .= "<p class = \"lead\">Сегодня:</p>";
				} else {
					$result .= "<p class = \"lead\">{$flashcard['created_date']}:</p>";
				}
				$lastFlashcardCreatedDate = $flashcard['created_date'];
			}
			$result .= "<a id = \"" . $flashcard['id'] . "\"/><a class = \"hero-unit flashcard\" href = \"" . getLink(EDIT_PAGE) . $flashcard['id'] . "/\">" . 
				// Раньше здесь ещё был nl2br, сейчас вместо него в CSS word-wrap: pre;
				htmlspecialchars($flashcard['front_text']) . 
				"</a>";
		}
	}
	
	return $result;
}



// Одна и та же функция используется для вывода текста при создании и редактировании карточки. При создании не выводится ничего. $fieldName - лицевая или обратная сторона.
function getFlashcardText($action, $flashcard, $fieldName) {
	global $newText;
	if ($action == CREATE_FLASHCARD) {
		return $newText[$fieldName];
	} elseif ($action == EDIT_FLASHCARD) {
		return htmlspecialchars($flashcard[$fieldName]);
	} else {
		throw "Неизвестный параметр";
	}
}



// Функция используется для установки значения скрытого поля в форме редактирования карточки. Значение скрытого поля, в свою очередь, нужно, чтобы передать в базу данных id карточки, которыую нужно обновлять.
function getFlashcardId($action, $flashcard) {
	if ($action == CREATE_FLASHCARD) {
		return "";
	} elseif ($action == EDIT_FLASHCARD) {
		return $flashcard['id'];
	} else {
		throw "Неизвестный параметр";
	}
}



// Функция выдаёт ссылку, на которую ведёт карточка. Если мы смотрим на лицевую сторону, ссылка ведёт на обратную, и наоборот. Эти ссылки работают, если отключён Яваскрипт (иначе содержимое обратной стороны карточки получается Аяксом).
function getFlashcardRepeatLink($view) {
	if ($view == FRONT_TEXT) {
		return getLink(REPEAT_PAGE) . "back/";
	} elseif($view == BACK_TEXT) {
		return getLink(REPEAT_PAGE) . "front/";
	} else {
		throw "Неизвестный параметр";
	}
}



// Возвращает title страницы (файл с header'ом во все страницы подставляется общий).

function getPageTitle($activePage) {
	switch ($activePage) {
	case CREATE_PAGE:
		return "Новая карточка &mdash; Вордхаб";
	case REPEAT_PAGE:
		return "Повтор слов &mdash; Вордхаб";
	case STATS_PAGE:
		return "Статистика и все карточки &mdash; Вордхаб";
	case EDIT_PAGE:
		return "Редактирование карточки &mdash; Вордхаб";
	case HOME_PAGE:
		return "Изучение иностранных слов &mdash; Вордхаб";
	case ADMIN_PAGE:
		return "Администраторская статистика &mdash; Вордхаб";
	case REGISTER_PAGE:
		return "Регистрация &mdash; Вордхаб";
	case INTRO_PAGE:
		return "О сайте &mdash; Вордхаб";
	case ABOUT_PAGE:
		return "Подробно о сайте &mdash; Вордхаб";
	case SETTINGS_PAGE:
		return "Настройки &mdash; Вордхаб";
	case DUPLICATE_USER_PAGE:
		return "Копирование пользователя &mdash; Вордхаб";
	}
}



// Для активной страницы возвращает класс active, чтобы пункт меню был выделен.
function getMenuItemClass($activePage, $page) {
	if ($page == $activePage) {
		return "active";
	} else {
		return "";
	}
}



// В статистике возвращает класс active для пункта меню с текущим периодом (день, неделя, месяц).
function getStatsPeriodClass($activePeriod, $period) {
	if ($activePeriod == $period) {
		return "active";
	} else {
		return "";
	}
}



// Возвращает текущую сторону карточки (для случая, когда текст на карточке не меняется Аяксом). Если параметр в адресной строке не задан, текущая карточка обновляется (чтобы при обновлении страницы каждый раз подгружалась случайная карточка).
function getFlashcardView() {
	if (isset($_GET['params']) && $_GET['params'] == 'front') {
		$view = FRONT_TEXT;
	} elseif (isset($_GET['params']) && $_GET['params'] == 'back') {
		$view = BACK_TEXT;
	} else {
		$view = FRONT_TEXT;
		refreshCurrentFlashcard();
	}
	return $view;
}



// Функция, выдающая id карточки, использующееся потом только для Аякса.
function getFlashcardRepeatId($view) {
	if ($view == FRONT_TEXT) {
		return "flashcard_front";
	} elseif ($view == BACK_TEXT) {
		return "flashcard_back";
	} else {
		return "";
	}
}



// Функция, выдающая в select всех пользователей. Используется для копирования всех карточек пользователя (пока это нужно только мне, для внешнего тестирования).
function getAllUsersForOutput() {
	$allUsers = sqlSelectAllUsers();
	$output = "";
	while ($user = mysql_fetch_array($allUsers)) {
		$output .= "<option value = \"" . $user['id'] . "\">" . $user['id'] . " " . $user['email'] . "</option>";
	}
	return $output;
}
?>