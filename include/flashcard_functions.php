<?php

/* В файле собраны функции, работающие с карточками: создание, задание интервалов для повтора, текущая карточка. Работы непосредственно с базой данных в этом файле нет. */


// Три функции, являющиеся интерфейсом к счётчику оставшихся для повтора на сегодня карточек и к списку этих карточек.

function getFlashcardsToRepeatToday() {
	$flashcardSet = sqlSelectFlashcardsToRepeatToday(getUserId());
	updateFlashcardsToRepeatTodayCount(mysql_num_rows($flashcardSet));
	return $flashcardSet;
}



function getFlashcardsToRepeatTodayCount() {
	if (!isset($_SESSION['flashcardsForTodayCount'])) {
		$flashcardSet = sqlSelectFlashcardsToRepeatToday(getUserId());
		$_SESSION['flashcardsForTodayCount'] = mysql_num_rows($flashcardSet);
	}
	return $_SESSION['flashcardsForTodayCount'];
}



// Параметр при вызове функции может быть неизвестен, тогда он вычисляется внутри функции. Если передаётся -1 (когда карточка помечается как повторенная, то текущее значение декрементируется на 1).
function updateFlashcardsToRepeatTodayCount($count = -2) {
	if ($count >= 0) {
		$_SESSION['flashcardsForTodayCount'] = $count;
	} elseif ($count == -1) {
		$_SESSION['flashcardsForTodayCount']--;
	} else {
		$flashcardSet = sqlSelectFlashcardsToRepeatToday(getUserId());
		$_SESSION['flashcardsForTodayCount'] = mysql_num_rows($flashcardSet);
	}
	
	return $_SESSION['flashcardsForTodayCount'];
}









// Три функции, являющиеся интерфейсом к текущей карточке (актуально для режима повтора, чтобы при нажатии на карточку показывалась оборотная сторона той же карточки, на которой нажали; при обновлении страницы можно менять карточку - для этого используется refreshCurrentFlashcard). 

function getCurrentFlashcardText($view) {
	if (!isset($_SESSION['currentFlashcard'])) {
		$_SESSION['currentFlashcard'] = selectCurrentFlashcard();
	}
	
	/* Раньше здесь применялся nl2br, чтобы все новые абзацы из базы данных заменялись в html на <br/>.
	 * Сейчас вместо него в CSS используется word-wrap: pre;
	 * htmlspecialchars - чтобы из базы в html не просачивались теги, чтобы они просто отображались в текстовом виде.*/
	return html_entity_decode($_SESSION['currentFlashcard'][$view]);
}



function markCurrentFlashcardAsRepeated($success) {
	if (!isset($_SESSION['currentFlashcard'])) {
		$_SESSION['currentFlashcard'] = selectCurrentFlashcard();
		setMessage("Произошла ошибка. Попробуем починить. Не найдена текущая карточка.", MSG_ERROR);
	} else {
		markFlashcardAsRepeated($_SESSION['currentFlashcard']['id'], $success);
		unset($_SESSION['currentFlashcard']);
	}
}



function refreshCurrentFlashcard() {
	$_SESSION['currentFlashcard'] = selectCurrentFlashcard();
}



// Функция случайным образом выбирает карточку, которую нужно повторить сейчас (из тех, что нужно повторять сегодня).
function selectCurrentFlashcard() {
	//Получаем количество карточек на сегодня. Потом уже PHP выбирает из массива случайный элемент.
	$flashcardSet = getFlashcardsToRepeatToday();
	
	// Выбираем случайную запись из выборки. Просто берём случайное число строк из выборки, и нужную запись возвращаем. Это явно неоптимально, просто я не умею обращаться к произвольной записи в выборке.
	$randomRow = rand(1, mysql_num_rows($flashcardSet));
	for ($i = 1; $i < $randomRow; $i++) {
		mysql_fetch_array($flashcardSet);
	}
	
	return mysql_fetch_array($flashcardSet);
}








// Добавляет в базу данных карточку с сегодняшней датой и заданными в параметрах текстами на лицевой и обратной сторонах. Здесь же устанавливаем для неё дату ближайшего повтора.
function addFlashcard($frontText, $backText) {
	$currentTime = time(); //Текущее время в секундах, которое потом используется для получения текущей даты и следующих нужных дат.
	$currentDate = date("Y-m-d", $currentTime);
	$flashcardId = sqlInsertFlashcard($frontText, $backText, $currentDate, getUserId());
	// Устанавливаем дату следующего повтора. Передаём в функцию: 1 - первый повтор, идентификатор только что созданной карточки, текущий момент.
	setRepetition(1, $flashcardId, $currentTime);
}






// Функции для установки даты следующего повтора.

// Фунция, помечающая данную карточку как выученную на сегодня. Здесь же устанавливаем дату следующего повтора.
function markFlashcardAsRepeated($flashcardId, $success) {
	$currentDate = date("Y-m-d");
	$currentRepetition = sqlSelectCurrentRepetitionForFlashcard($flashcardId);
	
	/* Потенциальная проблема - попытка пометить одну и ту же карточку как прочитанную из двух разных браузеров (с двух компьютеров).
	* Решается тем, что мы можем выбрать только "правильный" повтор, у которого запланированная дата меньше текущей (иначе предыдущая функция возвращает пустой массив).*/
	if (!empty($currentRepetition)) {
		$repetitionId = $currentRepetition['id'];
		$repetition = $currentRepetition['repetition'];
		sqlUpdateRepetition($repetitionId, $success);
		
		// Выставляем дату следующего повтора, если нужно (если карточка после этого повтора не считается выученной).
		if (($success == 1) && ($repetition < MAX_REPETITIONS)) {
			setRepetition($repetition + 1, $flashcardId, time());
		} elseif ($success == 0) {
			setRepetition(1, $flashcardId, time());
		}
		// Обновляем количество карточек, который осталось учить сегодня. Для того, чтобы правильно вычислялись параметры.
		// И сразу, если осталось 0, запускаем событие "все карточки на сегодня выучены".
		if (updateFlashcardsToRepeatTodayCount(-1) == 0) {
			// Это событие не готово, раскомментируем потом.
			//triggerEvent(ALL_FLASHCARDS_REPEATED_EVENT, getUserId());
		}
	} else {
		setMessage("Вы уже повторяли эту карточку.", MSG_WARNING);
	}
}



// Функция по дате, заданной в виде строки, и интервалу в днях (может быть и отрицательным), возвращает новую дату в виде строки.
function incrementDate($date, $intervalInDays) {
	$startTime = strtotime($date);
	$endTime = $startTime + $intervalInDays * 60 * 60 * 24;
	return date("Y-m-d", $endTime);
}



// Функция выставляет дату следующего повтора. $repetition - номер повтора (пока от 1 до MAX_REPETITIONS), $startTime - момент, к которому прибавляются дни (сейчас - текущий момент).
function setRepetition($repetition, $flashcardId, $startTime) {
	$intervalInDays = getIntervalInDays($flashcardId);
	$nextRepetitionTime = $startTime + $intervalInDays * 60 * 60 * 24;
	$nextRepetitionDate = date("Y-m-d", $nextRepetitionTime);
	sqlInsertRepetition($flashcardId, $repetition, $nextRepetitionDate);
}



// Вспомогательная функция, вычисляющая интервал между двумя датами, заданными строками.
function dayInterval($date1, $date2) {
	return floor((strtotime($date1) - strtotime($date2)) / (60 * 60 * 24));
}



// Вычисляет дату следующего повтора для заданной карточки по моему алгоритму. Из базы для этого выбираются все предыдущие повторы, и текущий интервал вычисляется, исходя из предыдущего интервала (если последний повтор был успешным), или просто устанавливается случайным образом от 1 до 3 дней (для новой карточки или после неуспешного повтора).
function getIntervalInDays($flashcardId) {
	$allRepetitions = sqlSelectAllRepetitionsForFlashcard($flashcardId);
	if (mysql_num_rows($allRepetitions) == 0) {
		return rand(1, 3);
	} else {
		// Пока функция проходит все предыдущие повторы карточки, хотя можно было бы ограничиться последними двумя.
		// Считаем все интервалы, исходя из прошлой actual_date и текущей planned_date.
		$repetition = mysql_fetch_array($allRepetitions);
		$previousInterval = dayInterval($repetition['planned_date'], $repetition['created_date']);
		$previousActualDate = $repetition['actual_date'];
		$previousSuccess = $repetition['success'];
		$previousRepetition = $repetition['repetition'];
		while ($repetition = mysql_fetch_array($allRepetitions)) {
			$previousInterval = dayInterval($repetition['planned_date'], $previousActualDate);
			$previousActualDate = $repetition['actual_date'];
			$previousSuccess = $repetition['success'];
			$previousRepetition = $repetition['repetition'];
		}
		if ($previousSuccess == 0) {
			return rand(1,3);
		} else if ($previousSuccess == 1) {
			// Если предыдущий повтор был успешным, до интервал до следущего = предыдущему, умноженному на коэффициент от 2 до 3.
			if ($previousRepetition < MAX_REPETITIONS) {
				return rand(2 * $previousInterval, 3 * $previousInterval);
			} else {
				throw "Ошибка в базе данных, уже чиним.";
			}
		} else {
			throw "Ошибка в базе данных, уже чиним.";
		}
	}	
}



// Функция для администраторского режима, копирующая все карточки из одного аккаунта в другой. Применять с осторожностью.
function copyAllFlashcards($oldUserId, $newUserId) {
	$allFlashcards = sqlSelectAllFlashcards($oldUserId, "ASC");
	$newFlashcardIds = array();
	while ($flashcard = mysql_fetch_array($allFlashcards)) {
		sqlInsertFlashcard($flashcard['front_text'], $flashcard['back_text'], $flashcard['created_date'], $newUserId);
		$newFlashcardIds[$flashcard['id']] = mysql_insert_id();
	}
	$allRepetitions = sqlSelectAllRepetitions($oldUserId);
	while ($repetition = mysql_fetch_array($allRepetitions)) {
		sqlInsertRepetition($newFlashcardIds[$repetition['flashcard_id']], $repetition['repetition'], $repetition['planned_date'], $repetition['actual_date'], $repetition['success']);
	}
}



// Функция для переноса не выученных в предыдущие даты карточек. Сколько дней прошло от того дня, на который остались невыученные карточки, на столько дней всё переносится вперёд.
function postponeUnrepeatedFlashcards() {
	// Выбираются все повторы, отсортированные по planned_date.
	$repetitionsToPostpone = sqlSelectRepetitionsToPostpone(getUserId());
	$postponeInterval = null;
	// Если повторов запланированных повторов не найдено, ничего не произойдёт (цикл не выполнится ни разу).
	while ($repetition = mysql_fetch_array($repetitionsToPostpone)) {
		// Код внутри этой проверки вызывается в первую итерацию цикла (потому что во вторую, если она будет, $postponeInterval будет уже не null).
		if ($postponeInterval == null) {
			// Если первый же planned_date не меньше текущей даты, то делать ничего больше не нужно, поэтому из цикла выходим.
			$currentDate = date("Y-m-d");
			if ($repetition['planned_date'] >= $currentDate) {
				break;
			} else {
				// Устанавливаем $postponeInterval, который будет дальше использоваться для переноса всех остальных повторов.
				$postponeInterval = dayInterval($currentDate, $repetition['planned_date']);
				// Здесь можно вставить вызов события "Вход на сайт после перерыва в повторах".
			}
		}
		
		// Теперь каждое повторение сдвигаем на $postponeInterval.
		
		$newDate = incrementDate($repetition['planned_date'], $postponeInterval);
		sqlUpdateRepetitionDate($repetition['id'], $newDate);
	}
}

?>