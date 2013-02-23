<?php

/* Все функции (кроме обращений к базе данных) для работы с текущим пользователем. */

function login($user) {
	// Если пользователь найден, устанавливаем для сессии id пользователя и наличие у него админских прав.
	setUser($user['id'], $user['admin']);
	// Если с предыдущих дней остались не повторенные карточки, переносим на сегодня.
	postponeUnrepeatedFlashcards();
	triggerEvent(LOGIN_EVENT, getUserId());
	redirectTo(getLink(CREATE_PAGE));
}


function loggedIn() {
	return isset($_SESSION['user']);
}

function isAdmin() {
	return $_SESSION['adminRights'];
}

function getUserId() {
	if (loggedIn()) {
		return $_SESSION['user'];
	} else {
		return NULL;
	}
}

function setUser($userId, $adminRights = 0) {
	$_SESSION['user'] = $userId;
	$_SESSION['adminRights'] = $adminRights;
}

function confirmLoggedIn() {
	if (!loggedIn()) {
		redirectTo(getLink(HOME_PAGE));
	}
}

function confirmAdminLoggedIn() {
	confirmLoggedIn();
	if (!isAdmin()) {
		redirectTo(getLink(CREATE_PAGE));
	}
}


function getUserFromDb($email, $password) {
	$result = sqlSelectUser($email, $password);
	if ($user = mysql_fetch_array($result)) {
		return $user;
	} else {
		return NULL;
	}
}


function getUserNameForOutput($userId) {
	if ($user = sqlSelectUserById($userId)) {
		if ($user['name']) {
			return $user['name'];
		} else {
			if (strlen($user['email']) <= MAX_NAME_LENGTH) {
				return $user['email'];
			} else {
				return substr($user['email'], 0, 25) . "&hellip;";
			}
		}
	} else {
		return NULL;
	}
}


function addUser($email, $password, $name = "") {
	$emailVerificationCode = sha1($email . time()); // Пока код генерируется без всяких паролей. Если нужно будет потом сделать более безопасное решение, то просто изменится эта функция, никаких неудобств для пользователей при этом не будет.
	$userId = sqlInsertUser($email, $password, $name, $emailVerificationCode);			
	return sqlSelectUserById($userId);
}



function getDailyLimit($userId) {
	$user = sqlSelectUserById($userId);
	return $user['daily_limit'];
}



function getSubscribedToEmailsCheckedStatus($userId) {
	$user = sqlSelectUserById($userId);
	if ($user['subscribed_to_emails'] == 1) {
		return "checked";
	} else {
		return "";
	}
}

?>