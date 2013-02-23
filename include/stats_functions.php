<?php

/* Функции, связанные с получением из базы таблиц для статистики (запросы находятся в отдельном файле). Здесь же - статистика по сегодняшним повторам. Здесь же - функция администраторского режима для обнуления сегодняшних результатов. */


function getFullStats() {
	
	/* Это статистика по повторам и успехам для администраторского режима. Очень объёмная и очень сырая функция.
	 * Размер таблицы - исходя из максимальной попытки и максимального дня. Поэтому размер её, если будет много попыток или много дней пропущу, может быть слишком большим.
	 * Суть расчётов - создаётся две таблицы.
	 * $flashcards[id]['currentAttempt'] - текущая попытка текущей карточки (одна попытка - до очередного неуспеха) (сохраняется, чтобы понять момент, когда начинается следующая попытка).
	 * $flashcards[id]['previousDate'] - дата, от которой отсчитывается очередной интервал.
	 * $flashcards[id]['previousRepetition'] - номер предыдущего повтора (вспомогательная переменная для определения момента начала следующей попытки).
	 * $statsByAttempt[iteration][dayInterval][attempt][success] - в ячейках хранится количество успешных и неуспешных угадываний для определённой итерации, дня (интервала) и попытки. */
	
	$repetitions = sqlSelectAllRepetitions(getUserId());
	$flashcards = array();
	$statsByAttempt = array();
	$maxDay = 1;
	$maxAttempt = 1;
	
	$statsByDate = array();
	$startDate = "0000-00-00";
	$endDate = "0000-00-00";
	$flashcardIds = array(); // Этот массив - чтобы знать, какие карточки уже попадались в массиве repetitions.
	$flashcardCount = 0; // Сколько всего карточек на изучении или выучено.
	$studiedFlashcardCount = 0; // Сколько всего карточек выучено.
	
	// Получаем все повторы по одному и заполняем массивы, которые потом будут нужны при выводе таблиц.
	while ($repetition = mysql_fetch_array($repetitions)) {
		
		// Считаем статистику по повторам и успехам только для уже повторенных карточек.
		
		if ($repetition['actual_date'] != '0000-00-00') {
			if (isset($flashcards[$repetition['flashcard_id']])) {
				$dayInterval = dayInterval($repetition['actual_date'], $flashcards[$repetition['flashcard_id']]['previousDate']);
				
				if ($repetition['repetition'] <= $flashcards[$repetition['flashcard_id']]['previousRepetition']) {
					$flashcards[$repetition['flashcard_id']]['currentAttempt'] ++;

				}
				
			} else {
				$dayInterval = dayInterval($repetition['actual_date'], $repetition['created_date']); 
				$flashcards[$repetition['flashcard_id']]['currentAttempt'] = 1;
			}
			
			if (isset($statsByAttempt [$repetition['repetition']] [$dayInterval] [$flashcards[$repetition['flashcard_id']]['currentAttempt']] [$repetition['success']])) {
				$statsByAttempt [$repetition['repetition']] [$dayInterval] [$flashcards[$repetition['flashcard_id']]['currentAttempt']] [$repetition['success']] ++;
			} else {
				$statsByAttempt [$repetition['repetition']] [$dayInterval] [$flashcards[$repetition['flashcard_id']]['currentAttempt']] [$repetition['success']] = 1;
			}
			
			$flashcards[$repetition['flashcard_id']]['previousDate'] = $repetition['actual_date'];
			$flashcards[$repetition['flashcard_id']]['previousRepetition'] = $repetition['repetition'];
			
			if ($flashcards[$repetition['flashcard_id']]['currentAttempt'] > $maxAttempt) {
				$maxAttempt++;
			}
			if ($dayInterval > $maxDay) {
				$maxDay = $dayInterval;
			}
		}
		


		// Этот же запрос используется для получения статистики по датам.
		
		if (($startDate == "0000-00-00") || ($repetition['created_date'] < $startDate)) {
			$startDate = $repetition['created_date'];
		}
		
		$maxDateInRepetition = max($repetition['planned_date'], $repetition['actual_date']);
		
		if ($maxDateInRepetition > $endDate) {
			$endDate = $maxDateInRepetition;
		}
		
		
		// Эта проверка и следующее присвоение - чтобы created прибавлялось только если эта карточка ещё не попадалась.
		
		if (!isset($flashcardIds[$repetition['flashcard_id']])) {
			if (isset($statsByDate[$repetition['created_date']]['created'])) {
				$statsByDate[$repetition['created_date']]['created'] ++;
			} else {
				$statsByDate[$repetition['created_date']]['created'] = 1;
			}
			
			$flashcardCount++;
		}
		
		$flashcardIds[$repetition['flashcard_id']] = 1;
		
		
		
		if (isset($statsByDate[$repetition['planned_date']]['planned'])) {
			$statsByDate[$repetition['planned_date']]['planned'] ++;
		} else {
			$statsByDate[$repetition['planned_date']]['planned'] = 1;
		}
		
		
		// actual и success в массиве увеличивается, только если это повторение уже случилось.
		
		if ($repetition['actual_date'] != '0000-00-00') {
			if (isset($statsByDate[$repetition['actual_date']]['actual'])) {
				$statsByDate[$repetition['actual_date']]['actual'] ++;
			} else {
				$statsByDate[$repetition['actual_date']]['actual'] = 1;
			}
			
			if ($repetition['success'] == 1) {
				if (isset($statsByDate[$repetition['actual_date']]['success'])) {
					$statsByDate[$repetition['actual_date']]['success'] ++;
				} else {
					$statsByDate[$repetition['actual_date']]['success'] = 1;
				}
				
				if ($repetition['repetition'] == MAX_REPETITIONS) {
					if (isset($statsByDate[$repetition['created_date']]['studied'])) {
						$statsByDate[$repetition['created_date']]['studied'] ++;
					} else {
						$statsByDate[$repetition['created_date']]['studied'] = 1;
					}
					
					if (isset($statsByDate[$repetition['actual_date']]['last_successful_repetition'])) {
						$statsByDate[$repetition['actual_date']]['last_successful_repetition'] ++;
					} else {
						$statsByDate[$repetition['actual_date']]['last_successful_repetition'] = 1;
					}
				}
			}
		}
		
	}
	
	
	$fullStats['flashcardCount'] = $flashcardCount;
	$fullStats['maxAttempt'] = $maxAttempt;
	$fullStats['maxDay'] = $maxDay;
	$fullStats['startDate'] = $startDate;
	$fullStats['endDate'] = $endDate;
	$fullStats['statsByAttempt'] = $statsByAttempt;
	$fullStats['statsByDate'] = $statsByDate;
	
	return $fullStats;
}



// Функция используется на странице статистике, доступной всем пользователям.
function calculateStatsForPeriod($statsByDate, $period) {
		
	$statsForPeriod['createdCount'] = 0;
	$statsForPeriod['repetitionCount'] = 0;
	$statsForPeriod['successCount'] = 0;
	$statsForPeriod['lastSuccessfulRepetitionCount'] = 0;	
	
	//Идём от сегодняшнего дня назад.
	$date = date("Y-m-d");
	
	for ($i = 0; $i < $period; $i++) {
		if (isset($statsByDate[$date]['created'])) {
			$statsForPeriod['createdCount'] += $statsByDate[$date]['created'];
		}
		if (isset($statsByDate[$date]['actual'])) {
			$statsForPeriod['repetitionCount'] += $statsByDate[$date]['actual'];
		}
		if (isset($statsByDate[$date]['success'])) {
			$statsForPeriod['successCount'] += $statsByDate[$date]['success'];
		}
		if (isset($statsByDate[$date]['last_successful_repetition'])) {
			$statsForPeriod['lastSuccessfulRepetitionCount'] += $statsByDate[$date]['last_successful_repetition'];
		}
			
		// Берём следующую (предыдущую) дату.
		$date = date("Y-m-d", strtotime($date) - 60 * 60 * 24);
	}

	return $statsForPeriod;
}




// Функция для вычисления статистики по сегодняшним повторам.
function getRepeatTodayParams() {
	postponeUnrepeatedFlashcards(); // Здесь эта функция вызывается на случай того, что сессия перейдёт на следующую дату. Таким образом, если не все повторы были завершены в текущую дату, то при открытии любой страницы на следующий день все не повторенные карточки перенесутся на один день вперёд.
	updateFlashcardsToRepeatTodayCount(); // Здесь эта функция для того, чтобы при открытии страницы repeat срабатывала переадресация.
	$repeatTodayParams = array();
	$repeatTodayParams['successful'] = getFlashcardsRepeatedTodayCount(getUserId(), 1);
	$repeatTodayParams['failed'] = getFlashcardsRepeatedTodayCount(getUserId(), 0);
	$repeatTodayParams['repeated'] = $repeatTodayParams['successful'] + $repeatTodayParams['failed'];
	$repeatTodayParams['toRepeat'] = getFlashcardsToRepeatTodayCount();
	// Вычисляем проценты для отгаданных и не отгаданных карточек. Проверяем заранее, чтобы не было деления на ноль.
	if ($repeatTodayParams['successful'] + $repeatTodayParams['failed'] + $repeatTodayParams['toRepeat'] != 0) {
		$repeatTodayParams['successRate'] = $repeatTodayParams['successful'] / ($repeatTodayParams['successful'] + $repeatTodayParams['failed'] + $repeatTodayParams['toRepeat']) * 100;
		$repeatTodayParams['failRate'] = $repeatTodayParams['failed'] / ($repeatTodayParams['successful'] + $repeatTodayParams['failed'] + $repeatTodayParams['toRepeat']) * 100;
	} else {
		$repeatTodayParams['successRate'] = 0;
		$repeatTodayParams['failRate'] = 0;
	}
	return $repeatTodayParams;
}





// Функция для администраторского режима, помечающая то, что было выучено сегодня, не выученным.
function clearTodaysResults() {
	$currentDate = date("Y-m-d");
	// Удаляем все следующие повторы, каждой карточки последовательно (много запросов запускается).
	$flashcardsRepeatedToday = sqlSelectFlashcardsRepeatedOnDate(getUserId(), $currentDate);
	while ($row = mysql_fetch_array($flashcardsRepeatedToday)) {
		sqlDeleteNextRepetition($row['flashcard_id']);
	}
	
	sqlNullifyAllRepetitionsOnDate(getUserId(), $currentDate);
}

?>