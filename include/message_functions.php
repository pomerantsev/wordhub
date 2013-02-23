<?php

/* Функции - интерфейс работы с сообщениями. Пока алгоритм работы сообщений такой: в каждом файле есть место, где сообщение выводится. Если в ходе выполнения любой функции сообщение было установлено, то оно выводится один раз и затем удаляется. */

function messageExists() {
	return isset($_SESSION['message']);
}

// eventMessage имеет преимущество перед обычным сообщением.
function setMessage($text, $type = MSG_WARNING, $eventMessage = false) {
	if (isset($_SESSION['message']['eventMessage']) && $_SESSION['message']['eventMessage'] == false || !isset($_SESSION['message']['eventMessage']) || $eventMessage == true) {
		$_SESSION['message']['text'] = $text;
		$_SESSION['message']['type'] = $type;
		$_SESSION['message']['eventMessage'] = $eventMessage;
	}
}

function setEventMessage($text) {
	setMessage($text, MSG_INFO, true);
}

function getMessageText() {
	return $_SESSION['message']['text'];
}

function getMessageType() {
	return $_SESSION['message']['type'];
}

function deleteMessage() {
	if (isset($_SESSION['message'])) {
		unset($_SESSION['message']);
	}
}

function showMessage() {
	if (messageExists()) {
		include(MESSAGE_PAGE_ELEMENT);
		deleteMessage();
	}
}

?>