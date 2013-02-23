<?php
	confirmAdminLoggedIn();
	$activePage = ADMIN_PAGE; // Пока нужно, чтобы ставить на странице правильный заголовок.
?>
<?php
	if (isset($_POST['submit']) && $_POST['submit'] == CLEAR) {
		clearTodaysResults();
		redirectTo(getLink(ADMIN_PAGE));
	}
?>
<?php					
	$fullStats = getFullStats();
	$flashcardCount = $fullStats['flashcardCount'];
	$maxAttempt = $fullStats['maxAttempt'];
	$maxDay = $fullStats['maxDay'];
	$startDate = $fullStats['startDate'];
	$endDate = $fullStats['endDate'];
	$statsByAttempt = $fullStats['statsByAttempt'];
	$statsByDate = $fullStats['statsByDate'];
?>

<!DOCTYPE HTML>
<html class = "no-js">
	<?php include(getFullPath(HEADER_PAGE_ELEMENT)); ?>
	<body>
		<div class = "container">
			<?php include(getFullPath(NAVBAR_PAGE_ELEMENT)); ?>
			<div class = "row">
				<div class = "span12">
					
					<?php showMessage(); ?>
					
					<h2>Администраторская статистика</h2>
					
					<form action = "<?php /*echo ADMIN_PAGE;*/ ?>" method = "post">
						<div class = "row">
							<div class = "span3">
								<button class = "btn btn-primary" type = "submit" name = "submit" value = "clear">Очистить сегодняшние результаты</button>
							</div>
							<div class = "span3">
								<a href = "<?php echo getLink(DUPLICATE_USER_PAGE); ?>">Копировать пользователя</a>
							</div>
						</div>
					</form>
					
					<h4>Общая</h4>
					<table class = "table table-bordered">
						<tr>
							<td>Всего карточек:</td>
							<td><?php echo $flashcardCount; ?></td>
						</tr>
						<tr>
							<td>Выучено:</td>
							<td><?php echo sqlGetStudiedFlashcardCount(getUserId()); ?></td>
						</tr>
					</table>
					
					<h4>По повторам</h4>
					<table class = "table table-bordered">
						<?php
							echo "<tr><th></th>";
							for ($attempt = 1; $attempt <= $maxAttempt; $attempt++) {
								echo "<th>" . $attempt . "-я попытка</th>";
							}
							echo "</tr>";
							for ($iteration = 1; $iteration <= MAX_REPETITIONS; $iteration++) {
								for ($day = 1; $day <= $maxDay; $day++) {
									echo "<tr><td>" . $iteration . "-я итерация, " . $day . "-й день</td>";
									for ($attempt = 1; $attempt <= $maxAttempt; $attempt++) {
										if (!isset($statsByAttempt [$iteration] [$day] [$attempt] [0])) {
											$statsByAttempt [$iteration] [$day] [$attempt] [0] = 0;
										}
										if (!isset($statsByAttempt [$iteration] [$day] [$attempt] [1])) {
											$statsByAttempt [$iteration] [$day] [$attempt] [1] = 0;
										}
										$count = $statsByAttempt [$iteration] [$day] [$attempt] [0] + $statsByAttempt [$iteration] [$day] [$attempt] [1];
										if ($count != 0) {
											$successRate = round($statsByAttempt [$iteration] [$day] [$attempt] [1] / $count * 100);
										} else {
											$successRate = 0;
										}
										echo "<td>";
										if ($count != 0) {
											echo $count . " повтор" . caseEnding($count, "", "а", "ов") . " / " . $successRate . "% успешно";
										}
										echo "</td>";
									}
									echo "</tr>";
								}
							}
						?>
					</table>
					<h4>По дням</h4>
					<table class = "table table-bordered">
						<tr>
							<th>Дата</th>
							<th>Введено</th>
							<th>Запланировано</th>
							<th>Повторено по факту</th>
							<th>Процент успеха из повторенных</th>
							<th>Выучено в этот день</th>
							<th>Выучено на сегодня из введённых</th>
						</tr>
						<?php
							if ($startDate != "0000-00-00") {
								$date = $startDate;
								while ($date <= $endDate) {
									echo "<tr><td>" . $date . "</td><td>";
									if (isset($statsByDate[$date]['created'])) {
										echo $statsByDate[$date]['created'];
									}
									echo "</td><td>";
									if (isset($statsByDate[$date]['planned'])) {
										echo $statsByDate[$date]['planned'];
									}
									echo "</td><td>";
									if (isset($statsByDate[$date]['actual'])) {
										echo $statsByDate[$date]['actual'];
									}
									echo "</td><td>";
									if (isset($statsByDate[$date]['success'])) {
										echo round($statsByDate[$date]['success'] / $statsByDate[$date]['actual'] * 100) . "%";
									}
									echo "</td><td>";
									if (isset($statsByDate[$date]['last_successful_repetition'])) {
										echo $statsByDate[$date]['last_successful_repetition'];
									}
									echo "</td><td>";
									if (isset($statsByDate[$date]['studied'])) {
										echo $statsByDate[$date]['studied'] . " (" . round($statsByDate[$date]['studied'] / $statsByDate[$date]['created'] * 100) . "%)";
									}
									echo "</td></tr>";
										
									// Берём следующую дату.
									$date = date("Y-m-d", strtotime($date) + 60 * 60 * 24);
								}
							}
						?>
					</table>
				</div>
			</div>
			<div class = "push"></div>
		</div>
		<?php include(getFullPath(FOOTER_PAGE_ELEMENT)); ?>
	</body>
</html>