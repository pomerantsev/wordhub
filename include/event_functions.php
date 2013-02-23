<?php

// В нынешней архитектуре событий показывается сообщение, относящееся только к одному из событий, если два события происходят одновременно.

define("EVENT0", 0);
define("EVENT1", 1);
define("LOGIN_EVENT", 2);
define("ALL_FLASHCARDS_REPEATED_EVENT", 3);
define("REDIRECT_TO_STATS_EVENT", 4);
define("EDIT_PAGE_OPENED_EVENT", 5);
define("CREATE_PAGE_OPENED_EVENT", 6);
define("REPEAT_PAGE_OPENED_EVENT", 7);
define("STATS_PAGE_OPENED_EVENT", 8);

function triggerEvent($eventId, $userId) {
	insertEvent($eventId, $userId);
	$eventCount = getEventCount($eventId, $userId);
	$message = getEventMessage($eventId, $eventCount);
	if ($message) {
		setEventMessage($message);
	}
}


function insertEvent($eventId, $userId) {
		
	// Здесь выполняются проверки, отдельно по каждому Id события.
	if ($eventId == LOGIN_EVENT) {
		sqlInsertEvent($eventId, $userId);
	} elseif ($eventId == REDIRECT_TO_STATS_EVENT) {
		sqlInsertEvent($eventId, $userId);
	} elseif ($eventId == EDIT_PAGE_OPENED_EVENT) {
		sqlInsertEvent($eventId, $userId);
	} elseif ($eventId == CREATE_PAGE_OPENED_EVENT) {
		sqlInsertEvent($eventId, $userId);
	} elseif ($eventId == REPEAT_PAGE_OPENED_EVENT) {
		sqlInsertEvent($eventId, $userId);
	} elseif ($eventId == STATS_PAGE_OPENED_EVENT) {
		sqlInsertEvent($eventId, $userId);
	}
	// Никакого else быть не должно, оставлен тут на всякий случай.
	else {
		sqlInsertEvent($eventId, $userId);
	}
}



function getEventCount($eventId, $userId) {
	$count;
	
	// Здесь выполняются проверки, отдельно по каждому Id события.
	if ($eventId == LOGIN_EVENT) {
		$count = sqlGetEventCount($eventId, $userId);
	} elseif ($eventId == REDIRECT_TO_STATS_EVENT) {
		$count = sqlGetEventCount($eventId, $userId);
	} elseif ($eventId == EDIT_PAGE_OPENED_EVENT) {
		$count = sqlGetEventCount($eventId, $userId);
	} elseif ($eventId == CREATE_PAGE_OPENED_EVENT) {
		$count = sqlGetEventCount($eventId, $userId);
	} elseif ($eventId == REPEAT_PAGE_OPENED_EVENT) {
		$count = sqlGetEventCount($eventId, $userId);
	} elseif ($eventId == STATS_PAGE_OPENED_EVENT) {
		$count = sqlGetEventCount($eventId, $userId);
	} 
	// Никакого else быть не должно, оставлен тут на всякий случай.
	else {
		$count = sqlGetEventCount($eventId, $userId);
	}
	
	return $count;
}


function getEventMessage($eventId, $eventCount) {
	$message = NULL;
	if ($eventId == LOGIN_EVENT) {
		// Пока вывод сообщения убрали, потому что сообщение здесь выводиться не должно, оно будет только в подчинённых событиях.
		/*if ($eventCount == 1) {
			$message = "_Это тестовое сообщение._ Спасибо, что зарегистрировались. Посмотрите, <a href = \"" . getLink(INTRO_PAGE) . "\">о чём этот сайт</a>.";
		} */
	} elseif ($eventId == REDIRECT_TO_STATS_EVENT) {
		if ($eventCount == 2) {
			/*$message = "_Это тестовое сообщение._ Вы молодец. Всё повторили на сегодня. Теперь можно смотреть статистику. Только зачем это сообщение выводится?";*/
		} elseif ($eventcount == 10) {
			/*$message = "Вы правда хорошо сегодня поработали. Зачем в ссылку столько раз тыкать? Ей тоже может быть больно.";*/
		}
	} elseif ($eventId == EDIT_PAGE_OPENED_EVENT) {
		if ($eventCount == 1) {
			/*$message = "Любую карточку можно редактировать. Если не нажать на &laquo;Сохранить&raquo; &mdash; то ничего и не сохранится.";*/
		}
	} elseif ($eventId == CREATE_PAGE_OPENED_EVENT) {
		if ($eventCount == 1) {
			$message = "Это главная страница, на которой создаются карточки. Как их создавать &mdash; можно прочесть в разделе <a href = \"" . getLink(ABOUT_PAGE) . "#everyDay\">&laquo;О сайте&raquo;</a>.";
		}
	} elseif ($eventId == REPEAT_PAGE_OPENED_EVENT) {
		if ($eventCount == 1) {
			$message = "Здесь появляется набор карточек на день. Почему заниматься лучше каждый день &mdash; смотрите в разделе <a href = \"" . getLink(ABOUT_PAGE) . "#everyDay\">&laquo;О сайте&raquo;</a>.";
		}
	} elseif ($eventId == STATS_PAGE_OPENED_EVENT) {
		if ($eventCount == 1) {
			/*$message = "Посмотрите статистику. Особенно интересно будет, когда хотя бы 100 карточек создадите.";*/
		}
	}
	// Это событие пока не готово, поэтому закомментировано.
	/* elseif ($eventId == ALL_FLASHCARDS_REPEATED_EVENT) {
		if ($eventCount == 1) {
			$message = "_Это тестовое сообщение._ Все карточки, которые нужно было повторить сегодня, вы повторили. Если будете регулярно их создавать, то повторы будут планироваться на каждый день. Так что не делайте перерывов — и будет результат.";
		} elseif ($eventCount == 3) {
			$message = "_Это тестовое сообщение._ Вы уже в третий раз все задания, запланированные на день, повторяете. Так держать.";
		}
	} */
	return $message;
}

?>