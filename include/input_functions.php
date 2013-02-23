<?php

/* Здесь собраны функции для обработки ввода на всех страницах. */



// Проверка на стороне сервера, все ли обязательные поля заполнены. Возвращается массив с названиями полей, которые не были заполнены.
function inputValid($required_fields) {	
	$errors = array();
	foreach($required_fields as $fieldname) {
		if (!isset($_POST[$fieldname]) || (empty($_POST[$fieldname]))) {
			$errors[] = $fieldname;
		}
	}
	return empty($errors);
}



// Обработка при вводе новой карточки или при редактировании. Оба действия собраны в одной функции, хотя, возможно, их можно было бы разнести.
function processFlashcardInput($action) {
	global $newText;
	if (isset($_POST['submit'])) {
		//  Проверяем: обязательно должны присутствовать введённые поля, а flashcard_id (скрытое поле) - только в случае редактирования карточки.
		if (inputValid(array('front_text', 'back_text')) && ($action == CREATE_FLASHCARD || inputValid(array('flashcard_id')) && is_numeric($_POST['flashcard_id']))) {
			$frontText = $_POST['front_text'];
			$backText = $_POST['back_text'];
			if ($action == CREATE_FLASHCARD) {
				addFlashcard($frontText, $backText);
			} else {
				// Если даже в форму мы передали id карточки чужого пользователя, она не будет отредактирована (ничего не произойдёт).
				sqlUpdateFlashcard($_POST['flashcard_id'], $frontText, $backText, getUserId());
			}
			
			// Выводим сообщение (при создании карточки). При редактировании - пока сообщение не выводится.
			if ($action == CREATE_FLASHCARD) {
				$flashcardsToAdd = getDailyLimit(getUserId()) - sqlGetFlashcardsCreatedTodayCount(getUserId());
				if ($flashcardsToAdd > 0) {
					setMessage("Сегодня осталось добавить " . $flashcardsToAdd . " карточ" . caseEnding($flashcardsToAdd, "ку", "ки", "ек") . ".", MSG_SUCCESS);
				} elseif ($flashcardsToAdd == 0) {
					setMessage("Вы добавили нужное количество карточек. Но если хотите &mdash; можете добавлять ещё.", MSG_SUCCESS);
				}
			}
			
			// После обработки формы перенаправляем - либо сюда же (после создания), либо обратно на статистику (после редактирования). 
			if ($action == EDIT_FLASHCARD) {
				redirectTo(getLink(STATS_PAGE) . "#" . $_POST['flashcard_id']);
			} elseif ($action == CREATE_FLASHCARD) {
				redirectTo(getLink(CREATE_PAGE));
			}
		} else {
			// Если какие-то поля не заполнены.
			$newText[FRONT_TEXT] = isset($_POST['front_text']) ? $_POST['front_text'] : "";
			$newText[BACK_TEXT] = isset($_POST['back_text']) ? $_POST['back_text'] : "";
			setMessage("Пожалуйста, заполните оба поля.", MSG_ERROR);
		}
	}
}



// Обработка ввода регистрационных данных.
function processLoginInput() {
	// Если перешли на страницу по нажатию на "Войти".
	if (isset($_POST['submit'])) {
		if (inputValid(array('email', 'password'))) {
			$user = getUserFromDb($_POST['email'], $_POST['password']);
			if ($user != NULL) {
				login($user);
			} else {
				setMessage("Неверный email / пароль.", MSG_ERROR);
			}
		} else {
			setMessage("Пожалуйста, заполните оба поля.", MSG_ERROR);
		}
	}
}



// Проверка email.
function emailValid($email) {
	if (preg_match('/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\\.[A-Z]{2,4}$/i', $email)) {
		return true;
	} else {
		return false;
	}
}



// Проверка, свободен ли email (нет ли в базе такого же email'а).
function emailFree($email) {
	if (sqlGetUserByEmailCount($email) == 1) {
		return false;
	} else {
		return true;
	}
}



// Проверка пароля.
function passwordValid($password) {
	if (preg_match('/^[A-Z0-9]{6,}$/i', $password)) {
		return true;
	} else {
		return false;
	}
}




// Обработка ввода регистрационных данных.
function processRegisterInput() {
	if (isset($_POST['submit'])) {
		if (inputValid(array('email', 'password'))) {
			/*$_POST['email'] = trim($_POST['email']);
			$_POST['password'] = trim($_POST['password']);
			$_POST['name'] = trim($_POST['name']);*/
			$message = "";
			if (!emailValid($_POST['email'])) {
				$message .= "Проверьте формат email. ";
			} elseif (!emailFree($_POST['email'])) {
				$message .= "Пользователь с таким email уже зарегистрирован. ";
			}
			if (strlen($_POST['password']) < MIN_PASSWORD_LENGTH) {
				$message .= "Пароль должен быть не короче 6 символов. ";
			}
			if (!passwordValid($_POST['password'])) {
				$message .= "Проверьте символы, которые вы ввели в пароль (может содержать только 0-9, a-z, A-Z). ";
			}
			if (!empty($message)) {
				setMessage($message, MSG_ERROR);
			} else {
				$user = addUser(trim($_POST['email']), trim($_POST['password']), trim($_POST['name']));
				login($user);
			}
		} else {
			setMessage("Пожалуйста, заполните обязательные поля.", MSG_ERROR);
		}
		global $emailText, $passwordText, $nameText;
		$emailText = $_POST['email'];
		$passwordText = $_POST['password'];
		$nameText = $_POST['name'];
	}
}



function dailyLimitValid($dailyLimit) {
	if (is_numeric($dailyLimit) && strlen($dailyLimit) <= 4 && intval($dailyLimit) >= MIN_DAILY_LIMIT && intval($dailyLimit) <= MAX_DAILY_LIMIT) {
		return true;
	} else {
		return false;
	}
}



// Обработка ввода на странице настроек.
function processSettingsInput() {
	if (isset($_POST['submit'])) {
		$_POST['oldPassword'] = trim($_POST['oldPassword']);
		$_POST['newPassword'] = trim($_POST['newPassword']);
		
		global $dailyLimit, $oldPasswordText, $newPasswordText;
		$dailyLimit = $_POST['dailyLimit'];
		$oldPasswordText = $_POST['oldPassword'];
		$newPasswordText = $_POST['newPassword'];
		
		$message = "";
		
		$user = sqlSelectUserById(getUserId());
		
		// Устанавливаем в любом случае дневной лимит. Предварительно проверяем, что это число от 1 до 100. Сохраняется, если введено дробное число, только целая часть.
		if (!inputValid(array('dailyLimit'))) {
			$message .= "Пожалуйста, введите в качестве дневного лимита число от " . MIN_DAILY_LIMIT . " до " . MAX_DAILY_LIMIT . ".";
		} elseif (!dailyLimitValid($_POST['dailyLimit'])) {
			$message .= "Дневной лимит может быть целым числом от " . MIN_DAILY_LIMIT . " до " . MAX_DAILY_LIMIT . " (вряд ли можно учить в день больше " . MAX_DAILY_LIMIT . " слов или меньше нуля). ";
		} else {
			sqlUpdateDailyLimit(intval($_POST['dailyLimit']), getUserId());
			unset($dailyLimit);
		}
		
		// Устанавливаем без проверок, если нужно, значение "Отправлять напоминания на email". Пока это значение ни на что не влияет.
		if (isset($_POST['subscribedToEmails']) && !$user['subscribed_to_emails']) {
			sqlUpdateSubscribedToEmails(1, getUserId());
		}
		if (!isset($_POST['subscribedToEmails']) && $user['subscribed_to_emails']) {
			sqlUpdateSubscribedToEmails(0, getUserId());
		}
		
		
		// Проверяем, заполнено ли хотя бы одно поле с паролями. Если да, то проверяем, что заполнены оба, что новый пароль правильного формата и что старый введён правильно. Если всё верно, сохраняем новый пароль.
		if (inputValid(array('oldPassword')) || inputValid(array('newPassword'))) {
			$oldMessage = $message;
			if (!inputValid(array('oldPassword', 'newPassword'))) {
				$message .= "Чтобы сменить пароль, заполните оба поля: старый и новый. ";
			}
			if (inputValid(array('oldPassword')) && sha1($_POST['oldPassword']) != $user['hashed_password']) {
				$message .= "Старый пароль введён неверно. ";
			}
			if (inputValid(array('newPassword')) && !passwordValid($_POST['newPassword'])) {
				$message .= "Проверьте символы, которые вы ввели в новый пароль (может содержать только 0-9, a-z, A-Z). ";
			}
			if (inputValid(array('newPassword')) && strlen($_POST['newPassword']) < MIN_PASSWORD_LENGTH) {
				$message .= "Новый пароль должен быть не короче 6 символов. ";
			}
			// Проверяем: если сообщение не удлинилось (никаких ошибок не выдано), то пароль нужно поменять на новый.
			if ($oldMessage == $message) {
				sqlUpdatePassword($_POST['newPassword'], getUserId());
				$oldPasswordText = "";
				$newPasswordText = "";
			}
		}
		
		
		//Выводим по окончании процесса сохранения настроек сообщение: либо всё сохранено, либо что-то пошло не так (но остальное при этом будет, тем не менее, сохранено).
		if (!empty($message)) {
			setMessage($message, MSG_ERROR);
		} else {
			setMessage("Настройки сохранены.", MSG_SUCCESS);
		}
	}
}


// Функция проверяет, можно ли открыть карточку для редактирования. Если да, возвращает карточку в виде массива.
function verifyFlashcardForEditing() {
	if (!isset($_GET['params']) || (isset($_GET['params']) && !is_numeric($_GET['params']))) {
		setMessage("Не введён номер карточки.", MSG_ERROR);
		return NULL;
	} else {
		$flashcards = sqlSelectFlashcardById($_GET['params'], getUserId());
		if (mysql_num_rows($flashcards) == 0) {
			setMessage("Такой карточки нет. Возможно, вы набрали неверный адрес.", MSG_ERROR);
			return NULL;
		} else {
			return mysql_fetch_array($flashcards);
		}
	}
}



// Функция помечает текущую карточку как повторенную.
function processRepeatInput() {
	if (isset($_POST['submit'])) {
		if ($_POST['submit'] == KNOW) {
			// Если currentFlashcard не установлена, то непонятно, с какой карточки вызвали это действие. Если пользователь не будет ничего хакать, такого быть не должно.
			markCurrentFlashcardAsRepeated(1);
			redirectTo(getLink(REPEAT_PAGE));
		} elseif ($_POST['submit'] == DONT_KNOW) {
			markCurrentFlashcardAsRepeated(0);
			redirectTo(getLink(REPEAT_PAGE));
		}
	}
}



// Вспомогательная функция: нет ли уже в базе другого пользователя с таким же email?
function emailUnique($email) {
	$allUsers = sqlSelectAllUsers();
	while ($user = mysql_fetch_array($allUsers)) {
		if (trim(mysqlPrep($email)) == $user['email']) {
			return false;
		}
	}
	return true;
}



// Функция копирует заданного пользователя (доступно только в администраторском режиме).
function processDuplicateUserInput() {
	if (isset($_POST['submit'])) {
		// Пока здесь проверяется только то, что эти поля непустые. Совсем никак не валидируется email.
		if (inputValid(array('email', 'password'))) {
			if (emailUnique($_POST['email'])) {
				$newUserId = addUser($_POST['email'], $_POST['password']);
				copyAllFlashcards($_POST['userId'], $newUserId);
				setMessage("Пользователь добавлен.", MSG_SUCCESS);
			} else {
				setMessage("Пользователь с таким email у нас уже есть.", MSG_ERROR);
			}
			
		} else {
			setMessage("Пожалуйста, заполните все поля.", MSG_ERROR);
		}
	}
}



// Функция выводит годы, когда функционирует сайт (чтобы вручную не менять).
function getCopyrightYears() {
	$currentYear = date("Y");
	$years = RELEASE_YEAR;
	if ($currentYear != RELEASE_YEAR) {
		$years .= "&ndash;" . $currentYear;
	}
	return $years;
}
?>