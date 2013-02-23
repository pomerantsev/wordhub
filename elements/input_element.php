	<form id = "flashcardInputForm" action = "<?php /*echo $activePage;*/?>" method = "post">
		<div class = "row">
			<div class = "span12">
				<!-- id = "frontTextInput" используется js'ом для установки туда курсора после загрузки страницы. -->
				<label for = "frontTextInput">Слово:</label>
				<textarea class = "input-block-level" rows = "6" id = "frontTextInput" name = "front_text"><?php echo getFlashcardText($action, $flashcard, FRONT_TEXT);?></textarea>
			</div>
			<div class = "span12">
				<label for = "backTextInput">Значение:</label>
				<textarea class = "input-block-level" rows = "6" id = "backTextInput" name = "back_text"><?php echo getFlashcardText($action, $flashcard, BACK_TEXT); ?></textarea>
			</div>
		</div>
		<input type = "hidden" name = "flashcard_id" value = "<?php echo getFlashcardId($action, $flashcard); ?>"/>
		<button class = "btn btn-block" id = "submitNew" type = "submit" name = "submit">Сохранить</button>
	</form>