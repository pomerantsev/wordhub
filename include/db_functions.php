<?php

/* Здесь все функции для работы с базой данных. Не во все функции передаётся userId (хотя это было бы надёжнее), потому что случайное изменение данных других пользователей в этих функциях экранировано на функциях более высокого уровня. */




function dbConnect() {
	$connection = mysql_connect(SERVER, USERNAME, PASSWORD);
	if (!$connection) {
		setMessage("Не удалось соединиться с базой данных: " . mysql_error(), MSG_ERROR);
		exit;
	}
	
	$db_select = mysql_select_db(DATABASE, $connection);
	if (!$db_select) {
		setMessage("Не удалось найти таблицу в базе данных: " . mysql_error(), MSG_ERROR);
		exit;
	}
	
	//Без этой строчки вместо русских букв в базу записываются кракозябры.
	mysql_set_charset('utf8');
	return $connection;
}


// Пока соединение с базой устанавливается при каждом новом запросе.
function query($query) {
	$connection = dbConnect();
	return mysql_query($query, $connection);
}




// Функция взята из курса по PHP, она что-то делает с текстами после ввода их в форму.
function mysqlPrep($value) {
	$magic_quotes_active = get_magic_quotes_gpc();
	$new_enough_php = function_exists("mysql_real_escape_string");
	if ($new_enough_php) {
		if ($magic_quotes_active) {
			$value = stripslashes($value);
		}
		$value = mysql_real_escape_string($value, dbConnect()); // dbConnect() здесь передаётся в качестве параметра, потому что иначе на MacOS программа по умолчанию пытается подключиться к базе данных не от root'а.
	} else {
		if (!$magic_quotes_active) {
			$value = addslashes($value);
		}
	}
	return $value;
}




function sqlInsertFlashcard($frontText, $backText, $date, $userId) {
	$frontText = trim(mysqlPrep($frontText));
	$backText = trim(mysqlPrep($backText));
	$query = "INSERT INTO flashcards (
			front_text, back_text, created_date, user_id
			) VALUES (
				'{$frontText}', '{$backText}', '{$date}', {$userId}
			)";
	query($query);
	
	return mysql_insert_id();
}



// Редактирует тексты карточки, не меняя никакие другие параметры.
function sqlUpdateFlashcard($id, $frontText, $backText, $userId) {
	$frontText = trim(mysqlPrep($frontText));
	$backText = trim(mysqlPrep($backText));
	$query = "UPDATE flashcards
				SET front_text = '{$frontText}', back_text = '{$backText}'
				WHERE id = {$id} AND user_id = {$userId}";
	query($query);
	return $id; // Возвращаем значение просто для целостности, оно нигде не используется.
}


// Функция возвращает количество карточек, созданных сегодня.
function sqlGetFlashcardsCreatedTodayCount($userId) {
	$currentDate = date("Y-m-d");
	$query = "SELECT COUNT(*) FROM flashcards WHERE created_date = '{$currentDate}' AND user_id = {$userId}";
	$result = query($query);
	$row = mysql_fetch_row($result);
	return $row[0];
}


// Функция возвращает все карточки, созданные пользователем.
function sqlSelectAllFlashcards($userId, $order) {
	$query = "SELECT *
				FROM flashcards
				WHERE user_id = {$userId}
				ORDER BY id {$order}";
	return query($query);
}



function sqlSelectFlashcardsToRepeatToday($userId) {
	// Выбираем только такие карточки, у которых дата повтора хотя бы одной (а она и может быть только одна) невыполненной итерации (где actual_date == "0000-00-00") меньше или равна текущей дате.
	$currentDate = date("Y-m-d");
	$query = "SELECT flashcards.id, flashcards.created_date, flashcards.front_text, flashcards.back_text
				FROM flashcards
				JOIN repetitions
				ON repetitions.flashcard_id = flashcards.id
				WHERE repetitions.planned_date <= '{$currentDate}' AND repetitions.actual_date = '0000-00-00' AND flashcards.user_id = {$userId}";
	return query($query);
}




function sqlSelectCurrentRepetitionForFlashcard($flashcardId) {
	$currentDate = date("Y-m-d");
	$query = "SELECT * FROM repetitions
				WHERE flashcard_id = {$flashcardId} AND actual_date = '0000-00-00' AND planned_date <= '{$currentDate}' 
				LIMIT 1";
	$result = query($query);
	return mysql_fetch_array($result);
}



function sqlUpdateRepetition($repetitionId, $success) {
	$currentDate = date("Y-m-d");
	$query = "UPDATE repetitions
				SET actual_date = '{$currentDate}', success = {$success}
				WHERE id = {$repetitionId}";
	query($query);
	return $repetitionId;
}



function sqlSelectRepetitionsToPostpone($userId) {
	$currentDate = date("Y-m-d");
	$query = "SELECT repetitions.id AS id,
				repetitions.actual_date AS actual_date,
				repetitions.planned_date AS planned_date,
				flashcards.user_id AS user_id
				FROM repetitions
				JOIN flashcards
				ON repetitions.flashcard_id = flashcards.id
				WHERE actual_date = '0000-00-00'
				AND user_id = {$userId}
				ORDER BY planned_date ASC";
	return query($query);
}



function sqlUpdateRepetitionDate($repetitionId, $newDate) {
	$query = "UPDATE repetitions
				SET planned_date = '{$newDate}'
				WHERE id = {$repetitionId}";
	query($query);
}



function sqlSelectFlashcardsRepeatedOnDate($userId, $date) {
	$query = "SELECT repetitions.flashcard_id AS flashcard_id,
			repetitions.actual_date AS actual_date,
			flashcards.user_id AS user_id
			FROM repetitions
			JOIN flashcards
			ON repetitions.flashcard_id = flashcards.id
			WHERE actual_date = '{$date}'
			AND user_id = {$userId}";
	return query($query);
}



function sqlDeleteNextRepetition($flashcardId) {
	$query = "DELETE FROM repetitions
				WHERE flashcard_id = {$flashcardId}
				AND actual_date = '0000-00-00'";
	query($query);
}



function sqlNullifyAllRepetitionsOnDate($userId, $date) {
	$query = "UPDATE repetitions, flashcards
				SET repetitions.actual_date = '0000-00-00', repetitions.success = 0
        		WHERE repetitions.actual_date = '{$date}'
				AND flashcards.user_id = {$userId}
        		AND repetitions.flashcard_id = flashcards.id";
	query($query);
}


function sqlSelectFlashcardById($id, $userId) {
	$query = "SELECT *
				FROM flashcards
				WHERE id = {$id} 
				AND flashcards.user_id = {$userId}
				LIMIT 1";
	return query($query);
}



function getFlashcardsRepeatedTodayCount($userId, $success) {
	$currentDate = date("Y-m-d");
	$query = "SELECT COUNT(*) as count
				FROM repetitions
				JOIN flashcards
				ON repetitions.flashcard_id = flashcards.id
				WHERE repetitions.actual_date = '{$currentDate}'
				AND repetitions.success = {$success}
				AND flashcards.user_id = {$userId}";
	$result = query($query);
	$count = mysql_result($result, 0);
	return $count;
}



function sqlSelectAllRepetitionsForFlashcard($flashcardId) {
	$query = "SELECT repetitions.id AS id,
					 repetitions.flashcard_id AS flashcard_id,
					 repetitions.repetition AS repetition,
					 repetitions.planned_date AS planned_date,
					 repetitions.actual_date AS actual_date,
					 repetitions.success AS success,
					 flashcards.created_date AS created_date
			  FROM repetitions 
			  JOIN flashcards
			  ON repetitions.flashcard_id = flashcards.id
			  WHERE flashcard_id = {$flashcardId}
			  ORDER BY id ASC";
	return query($query);
}



// Возвращаются все выученные карточки. Считается, как успешно завершённые повторы третьей итерации.
function sqlGetStudiedFlashcardCount($userId) {
	$query = "SELECT COUNT(*) as count
				FROM repetitions
				JOIN flashcards
				ON repetitions.flashcard_id = flashcards.id
				WHERE repetitions.actual_date != '0000-00-00'
				AND repetitions.repetition = " . MAX_REPETITIONS .
				" AND repetitions.success = 1
				AND flashcards.user_id = {$userId}";
	$result = query($query);
	$count = mysql_result($result, 0);
	return $count;
}



// Добавляет к заданной карточке повтор под заданным номером в заданную дату.
function sqlInsertRepetition($flashcardId, $repetition, $plannedDate, $actualDate = '0000-00-00', $success = 0) {
	$query = "INSERT INTO repetitions (
			flashcard_id, repetition, planned_date, actual_date, success
			) VALUES (
				{$flashcardId}, {$repetition}, '{$plannedDate}', '{$actualDate}', {$success}
			)";
	query($query);
}


// Функция, которая возвращает все повторы. Для администраторской статистики.
function sqlSelectAllRepetitions($userId) {
	$query = "SELECT repetitions.id AS id, repetitions.flashcard_id AS flashcard_id, repetitions.repetition AS repetition, repetitions.planned_date AS planned_date, repetitions.actual_date AS actual_date, repetitions.success AS success, flashcards.created_date AS created_date
			FROM repetitions
			JOIN flashcards
			ON repetitions.flashcard_id = flashcards.id
			WHERE flashcards.user_id = {$userId}";
	return query($query);
}



function sqlSelectUser($email, $password) {
	$email = trim(mysqlPrep($email));
	$hashedPassword = sha1(trim(mysqlPrep($password)));
	$query = "SELECT * FROM users
				WHERE email = '{$email}'
				AND hashed_password = '{$hashedPassword}'
				LIMIT 1;";
	return query($query);
}



function sqlSelectUserById($userId) {
	$query = "SELECT * FROM users
				WHERE id = {$userId}";
	return mysql_fetch_array(query($query));
}



function sqlGetUserByEmailCount($email) {
	$email = trim(mysqlPrep($email));
	$query = "SELECT * FROM users
				WHERE email = '{$email}'
				LIMIT 1;";
	return mysql_num_rows(query($query));
}



function sqlSelectAllUsers() {
	$query = "SELECT id, email FROM users";
	return query($query);
}



function sqlInsertUser($email, $password, $name, $emailVerificationCode) {
	$email = trim(mysqlPrep($email));
	$hashedPassword = sha1(trim(mysqlPrep($password)));
	$name = trim(mysqlPrep($name));
	$emailVerificationCode = trim(mysqlPrep($emailVerificationCode)); // Это вызывается на всякий случай, значение уже без лишних символов.
	$query = "INSERT INTO users (
			email, hashed_password, name, email_verification_code
			) VALUES (
				'{$email}', '{$hashedPassword}', '{$name}', '{$emailVerificationCode}'
			)";
	query($query);
	return mysql_insert_id();
}



function sqlUpdateDailyLimit($dailyLimit, $userId) {
	$query = "UPDATE users
				SET daily_limit = {$dailyLimit}
				WHERE id = {$userId}";
	query($query);
}


function sqlUpdateSubscribedToEmails($subscribedToEmails, $userId) {
	$query = "UPDATE users
				SET subscribed_to_emails = {$subscribedToEmails}
				WHERE id = {$userId}";
	query($query);
}


function sqlUpdatePassword($newPassword, $userId) {
	$hashedNewPassword = sha1(trim(mysqlPrep($newPassword)));
	$query = "UPDATE users
				SET hashed_password = '{$hashedNewPassword}'
				WHERE id = {$userId}";
	query($query);
}




function sqlInsertEvent($eventId, $userId) {
	$query = "INSERT INTO events (
			event_id, user_id
			) VALUES (
				{$eventId}, {$userId}
			)";
	query($query);
}


function sqlGetEventCount($eventId, $userId) {
	$query = "SELECT *
			FROM events
			WHERE event_id = {$eventId}
			AND user_id = {$userId}";
	$result = query($query);
	return mysql_num_rows($result);
}

?>