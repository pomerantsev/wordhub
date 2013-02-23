<?php
	if (isset($_GET['params']) && is_numeric($_GET['params']) && $_GET['params'] > 0) {
		$period = $_GET['params'];
	} else {
		$period = 1;
	}
	
	$fullStats = getFullStats();
	$statsForPeriod = calculateStatsForPeriod($fullStats['statsByDate'], $period);
?>
<div class = "hero-unit clearfix">
	<div class = "span6">
		<p>Всего карточек: <span class = "text-success"><strong><?php echo $fullStats['flashcardCount']; ?></strong></span></p>
		<p>Из них выучено: <span class = "text-success"><strong><?php echo sqlGetStudiedFlashcardCount(getUserId()); ?></strong></span></p>
		<br/><br/>
	</div>
	<div class = "span 6">
		<ul class = "nav nav-pills">
			<li class = "<?php echo getStatsPeriodClass($period, DAY); ?>"><a href = "<?php echo getLink(STATS_PAGE) . DAY . "/"; ?>">Сегодня</a></li>
			<li class = "<?php echo getStatsPeriodClass($period, WEEK); ?>"><a href = "<?php echo getLink(STATS_PAGE) . WEEK . "/"; ?>">Неделя</a></li>
			<li class = "<?php echo getStatsPeriodClass($period, MONTH); ?>"><a href = "<?php echo getLink(STATS_PAGE) . MONTH . "/"; ?>">Месяц</a></li>
		</ul>
		<p>Создано: <span class = "text-success"><strong><?php echo $statsForPeriod['createdCount']; ?></strong></span></p>
		<p>Выучено: <span class = "text-success"><strong><?php echo $statsForPeriod['lastSuccessfulRepetitionCount']; ?></strong></span></p>
		<p>Всего повторов: <span class = "text-success"><strong><?php echo $statsForPeriod['repetitionCount']; ?></strong></span></p>
		<p>Из них успешных: <span class = "text-success"><strong><?php echo $statsForPeriod['successCount'] . " (" . floor($statsForPeriod['successCount'] / (($statsForPeriod['repetitionCount'] == 0) ? 1 : $statsForPeriod['repetitionCount']) * 100) . "%)"; ?></strong></span></p>
	</div>
</div>