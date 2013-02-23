// Нужно учитывать, что jQuery автоматом вызывает функцию htmlspecialchars - если она уже была применена, то на карточке может быть выведен html.
// Сейчас и Аякс-скрипт, и страница получают текст, обрамлённый в html_entity_decode. И на странице вызывается htmlentities.

$('.flashcard:first').click(function(event) {
	var flashcard = $(this),
		viewParam,
		newFlashcardId,
		isReturned = false;
	if (flashcard.attr('id') === 'flashcard_front') {
		viewParam = 'back_text';
		newFlashcardId = 'flashcard_back';
	} else if (flashcard.attr('id') === 'flashcard_back') {
		viewParam = 'front_text';
		newFlashcardId = 'flashcard_front';
	}
	
	// Если пользователь случайно ввёл неправильный адрес, например, /repeat/something/, JS считает, что ../flashcard_text_script/ - это /repeat/flashcard_text_script/. В этом случае лучше сработать обычной ссылке. Для этого функция-обработчик click переадресует нас по ссылке (Аякс не срабатывает).
	
	$.get("../flashcard_text_script/" + viewParam, null, function(responseText, status) {
		if (responseText.substring(0, 3) === "~~~") {
			flashcard.text(responseText.substring(3));
			flashcard.attr("id", newFlashcardId);
			isReturned = true;
		} else {
			location = $('.flashcard:first').attr("href");
		}
	});
	
	// Пока не получилось, чтобы нормально заработала демонстрация gif во время отправки и получения Аякс-запроса. На iOS и Android gif вообще не показывается, тогда как на настольных браузерах - вполне.
	/*setTimeout(function() {
		if (!isReturned) {
			flashcard.html("<img src = \"../img/ajax-loader.gif\" />");
		}
	}, 100);*/

	return false;
});