<h1>
	<span class="right">
<?php $this->displayIconLinks(); ?>
	</span>

<?php $this->displayNavigationLinks(); ?>
</h1>

<table style="width:100%;">
	<tr class="space">
		<td colspan="<?php echo ($this->Dataset->column_count + 2); ?>" />
	</tr>
<?php
foreach ($this->days as $i => $day) {
	$date_string = '<small>'.date("d.m.", $day['date']).'</small> '.Helper::Weekday(date("w", $day['date']), true);

	if (!empty($day['trainings'])) {
		foreach ($day['trainings'] as $t => $training) {
			$wk_class = Helper::TrainingIsCompetition($training) ? ' wk' : '';
			echo('<tr class="a'.($i%2+1).' r training'.$wk_class.'" rel="'.$training.'">');

			if ($t != 0)
				echo('<td colspan="2" />');
			else {
				echo('<td class="l" style="width:24px;">');

				foreach ($day['shorts'] as $short) {
					$this->Dataset->setTrainingId($short);
					$this->Dataset->displayShortLink();
				}

				echo('</td><td class="l">'.$date_string.'</td>');
			}

			$this->Dataset->setTrainingId($training);
			$this->Dataset->displayTableColumns();

			echo('</tr>');
		}
	} else {
		echo('
		<tr class="a'.($i%2+1).' r">
			<td class="l" style="width:24px;">');

		foreach ($day['shorts'] as $short) {
			$this->Dataset->setTrainingId($short);
			$this->Dataset->displayShortLink();
		}

		echo('</td>
			<td class="l">'.$date_string.'</td>
			<td colspan="'.$this->Dataset->column_count.'" />
		</tr>');
	}

	if (date("w", $day['date']) == 0 || $i == ($this->day_count-1))
		echo (NL.'
	<tr class="space">
		<td colspan="'.($this->Dataset->column_count+2).'" />
	</tr>'.NL);
}
#
// Z U S A M M E N F A S S U N G
$sports = $this->Mysql->fetchAsArray('SELECT `id`, `time`, `sportid`, SUM(1) as `num` FROM `'.PREFIX.'training` WHERE `time` BETWEEN '.($this->timestamp_start-10).' AND '.($this->timestamp_end-10).' GROUP BY `sportid`');
foreach ($sports as $sportdata) {
	$Sport = new Sport($sportdata['sportid']);
	echo('
<tr class="a'.(($i++)%2+1).' r">
	<td colspan="2">
		<small>'.$sportdata['num'].'x</small>
		'.$Sport->name().'
	</td>');

	$this->Dataset->loadGroupOfTrainings($sportdata['sportid'], $this->timestamp_start, $this->timestamp_end);
	$this->Dataset->displayTableColumns();

	echo('
</tr>'.NL);
}
?>

</table>