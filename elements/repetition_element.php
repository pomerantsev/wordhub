<!-- Класс flashcard - для аяксового скрипта. -->


<form class = "form-actions" action = "<?php /*echo REPEAT_PAGE;*/ ?>" method = "post">
	<button class = "btn btn-link" id = "submitDontKnow" type = "submit" name = "submit" value = "<?php echo DONT_KNOW; ?>">Не помню</button>
	<button class = "btn btn-link pull-right" id = "submitKnow" type = "submit" name = "submit" value = "<?php echo KNOW; ?>">Помню</button>
</form>

<!-- hero-unit - это класс из twitter bootstrap, определяет внешний вид элемента. flashcard - нужен для подгрузки текста Аяксом. -->
<a id = "<?php echo getFlashcardRepeatId($view); ?>" class = "hero-unit flashcard" href = "<?php echo getFlashcardRepeatLink($view); ?>"><?php echo htmlspecialchars(getCurrentFlashcardText($view)); ?></a>