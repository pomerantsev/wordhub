var dropdownMenu = $("#dropdownMenu");
	try {
		// Этот код находится в try-catch, потому что в IE8 insertAfter вызывает ошибку (почему - пока не выяснил). Если ошибку не отлавливать, будет выполняться append, таким образом, меню будет исчезать. А в collapse в IE8 меню всё равно не нужно засовывать, потому что в этом браузере сайт фиксированной ширины (браузер не поддерживат media qeuries).
		var menuDiv = $("<div class = 'nav-collapse collapse'>").insertAfter($("#elementBeforeDropdown"));
		menuDiv.append(dropdownMenu);
	} catch (error) {}