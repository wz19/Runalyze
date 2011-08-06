<?php
/**
 * This file contains the class to handle and display the datasat for any training
 */
/**
 * Class: Dataset
 * 
 * @author Hannes Christiansen <mail@laufhannes.de>
 * @version 1.0
 * @uses class::Mysql
 * @uses class:Error
 */

class Dataset {
	/**
	 * Internal ID in database
	 * @var int
	 */
	private $trainingId = 0;

	/**
	 * Counter for displayed columns
	 * @var int
	 */
	public $column_count = 0;

	/**
	 * Internal Training-object
	 * @var Training
	 */
	private $Training = null;

	/**
	 * Data array from database
	 * @var array
	 */
	private $data;

	/**
	 * Constructor
	 */
	public function __construct() {
		$dat = Mysql::getInstance()->fetch('SELECT * FROM `'.PREFIX.'dataset` WHERE `modus`>=2 AND `position`!=0 ORDER BY `position` ASC');
		if ($dat === false) {
			Error::getInstance()->addError('No dataset in database is active.');
			return false;
		}

		$this->data = $dat;
		$this->column_count = count($dat);
	}

	/**
	 * Load complete dataset where position != 0
	 */
	public function loadCompleteDataset() {
		$this->data = Mysql::getInstance()->fetch('SELECT * FROM `'.PREFIX.'dataset` WHERE `position`!=0 ORDER BY `position` ASC');
		$this->column_count = count($this->data);
	}

	/**
	 * Set training
	 * @param int $id
	 */
	public function setTrainingId($id) {
		$this->trainingId = $id;
		$this->Training = new Training($id);
	}

	/**
	 * Load a group of trainings for summary
	 * @param int $sportid
	 * @param int $timestamp_start
	 * @param int $timestamp_end
	 */
	public function loadGroupOfTrainings($sportid, $timestamp_start, $timestamp_end) {
		$this->setTrainingId(-1);

		$query_set = '';
		foreach ($this->data as $set)
			if ($set['summary'] == 1)
				if ($set['summary_mode'] != 'AVG')
					$query_set .= ', '.$set['summary_mode'].'(`'.$set['name'].'`) as `'.$set['name'].'`';

		$summary = Mysql::getInstance()->fetch('SELECT *, SUM(1) as `num`'.$query_set.' FROM `'.PREFIX.'training` WHERE `sportid`='.$sportid.' AND `time` BETWEEN '.($timestamp_start-10).' AND '.($timestamp_end-10).' GROUP BY `sportid`');
		foreach ($summary as $var => $value)
			$this->Training->set($var, $value);

		foreach ($this->data as $set)
			if ($set['summary'] == 1 && $set['summary_mode'] == 'AVG') {
				$avg_data = Mysql::getInstance()->fetch('SELECT COUNT(1) as `num`, SUM(`s`) as `ssum`, AVG(`'.$set['name'].'`*`s`) as `'.$set['name'].'` FROM `'.PREFIX.'training` WHERE `time` BETWEEN '.($timestamp_start-10).' AND '.($timestamp_end-10).' AND `'.$set['name'].'`!=0 AND `'.$set['name'].'`!="" AND `sportid`="'.$sportid.'" GROUP BY `sportid`');
				if ($avg_data === false)
					$avg_data[$set['name']] = '';
				$this->Training->set($set['name'], ($avg_data['num']*$avg_data[$set['name']]/$avg_data['ssum']));
			}
	}

	/**
	 * Is Dataset running in summary-mode?
	 * @return bool
	 */
	private function isSummaryMode() {
		return ($this->trainingId == -1);
	}

	/**
	 * Display short link for e.g. 'Gymnastik'
	 */
	public function displayShortLink() {
		$name = Helper::Time( $this->Training->get('s') ); 
		$icon = Icon::getSportIcon($this->Training->get('sportid'), $name);
		echo $this->Training->trainingLink($icon);
	}

	/**
	 * Display this dataset as a table-row
	 */
	public function displayTableColumns() {
		foreach ($this->data as $set)
			$this->displayDataset($set);
	}

	/**
	 * Display a single dataset
	 * @param array $dataset
	 */
	private function displayDataset($set) {
		if ($this->isSummaryMode() && $set['summary'] == 0) {
			echo HTML::emptyTD();
			return;
		}

		$class = $set['class'] != '' ? ' class="'.$set['class'].'"' : '';
		$style = $set['style'] != '' ? ' style="'.$set['style'].'"' : '';

		echo '<td'.$class.$style.'>'.$this->getDataset($set['name']).'</td>';
	}

	/**
	 * Get content for a given dataset
	 * @param string $name
	 * @return string
	 */
	private function getDataset($name) {
		switch($name) {
			case 'sportid':
				return $this->datasetSport();
			case 'typeid':
				return $this->datasetType();
			case 'time':
				return $this->datasetDate();
			case 'distance':
				return $this->datasetDistance();
			case 's':
				return $this->datasetTime();
			case 'pace':
				return $this->datasetPace();
			case 'elevation':
				return $this->datasetElevation();
			case 'kcal':
				return $this->datasetCalories();
			case 'pulse_avg':
				return $this->datasetPulse();
			case 'pulse_max':
				return $this->datasetPulseMax();
			case 'trimp':
				return $this->datasetTRIMP();
			case 'temperature':
				return $this->datasetTemperature();
			case 'weatherid':
				return $this->datasetWeather();
			case 'route':
				return $this->datasetPath();
			case 'clothes':
				return $this->datasetClothes();
			case 'splits':
				return $this->datasetSplits();
			case 'comment':
				return $this->datasetDescription();
			case 'partner':
				return $this->datasetPartner();
			case 'abc':
				return $this->datasetABC();
			case 'shoeid':
				return $this->datasetShoe();
			case 'vdot':
				return $this->datasetVDOT();
			default:
				return '&nbsp;';
		}
	}

	/**
	 * Dataset for: `sportid`
	 * @return string
	 */
	private function datasetSport() {
		return $this->Training->Sport()->Icon();
	}

	/**
	 * Dataset for: `typeid`
	 * @return string
	 */
	private function datasetType() {
		if ($this->Training->hasType())
			return $this->Training->Type()->formattedAbbr();

		return '';
	}

	/**
	 * Dataset for: `time`
	 * @return string
	 */
	private function datasetDate() {
		return $this->Training->getDaytimeString();
	}

	/**
	 * Dataset for: `distance`
	 * @return string
	 */
	private function datasetDistance() {
		return $this->Training->getDistanceString();
	}

	/**
	 * Dataset for: `s`
	 * @return string
	 */
	private function datasetTime() {
		return $this->Training->getTimeString();
	}

	/**
	 * Dataset for: `pace`
	 * @return string
	 */
	private function datasetPace() {
		return $this->Training->getSpeedString();
	}

	/**
	 * Dataset for: `elevation`
	 * @return string
	 */
	private function datasetElevation() {
		return ($this->Training->get('elevation') != 0)
			? '<span title="&oslash; '.round($this->Training->get('elevation')/$this->Training->get('distance')/10, 2).' &#37;">'.$this->Training->get('elevation').'&nbsp;hm</span>'
			: '';
	}

	/**
	 * Dataset for: `kcal`
	 * @return string
	 */
	private function datasetCalories() {
		return Helper::Unknown($this->Training->get('kcal')).'&nbsp;kcal';
	}

	/**
	 * Dataset for: `pulse_avg`
	 * @return string
	 */
	private function datasetPulse() {
		return Helper::PulseString($this->Training->get('pulse_avg'), $this->Training->get('time'));
	}

	/**
	 * Dataset for: `pulse_max`
	 * @return string
	 */
	private function datasetPulseMax() {
		return Helper::PulseString($this->Training->get('pulse_max'), $this->Training->get('time'));
	}

	/**
	 * Dataset for: `trimp`
	 * @return string
	 */
	private function datasetTRIMP() {
		return '<span style="color:#'.Helper::Stresscolor($this->Training->get('trimp')).';">'.$this->Training->get('trimp').'</span>';
	}

	/**
	 * Dataset for: `temperature`
	 * @return string
	 */
	private function datasetTemperature() {
		return $this->Training->Weather()->temperatureString();
	}

	/**
	 * Dataset for: `weatherid`
	 * @return string
	 */
	private function datasetWeather() {
		if (!$this->Training->Weather()->isUnknown())
			return $this->Training->Weather()->icon();

		return '';
	}

	/**
	 * Dataset for: `route`
	 * @return string
	 */
	private function datasetPath() {
		return ($this->Training->get('route') != '') ? Helper::Cut($this->Training->get('route'), 20) : '';
	}

	/**
	 * Dataset for: `kleidung`
	 * @return string
	 */
	private function datasetClothes() {
		return Helper::Cut($this->Training->Clothes()->asString(), 20);
	}

	/**
	 * Dataset for: `splits`
	 * @return string
	 */
	private function datasetSplits() {
		if ($this->Training->get('splits') == '')
			return;

		return Icon::get( Icon::$CLOCK, $this->Training->getSplitsAsString() );
	}

	/**
	 * Dataset for: `comment`
	 * @return string
	 */
	private function datasetDescription() {
		return '<span title="'.$this->Training->get('comment').'">'.Helper::Cut($this->Training->get('comment'), 20).'</span>';
	}

	/**
	 * Dataset for: `partner`
	 * @return string
	 */
	private function datasetPartner() {
		return ($this->Training->get('partner') != '') ? 'mit '.Helper::Cut($this->Training->get('partner'), 15) : '';
	}

	/**
	 * Dataset for: `abc`
	 * @return string
	 */
	private function datasetABC() {
		if ($this->Training->get('abc') == 0)
			return;

		return Icon::get( Icon::$ABC, 'Lauf-ABC');
	}

	/**
	 * Dataset for: `shoeid`
	 * @return string
	 */
	private function datasetShoe() {
		return Helper::Shoe($this->Training->get('shoeid'));
	}

	/**
	 * Dataset for: `vdot`
	 * @return string
	 */
	private function datasetVDOT() {
		$VDOT = round($this->Training->get('vdot'), 2);
		if ($VDOT == 0)
			return '';

		if ($this->trainingId == -1)
			$VDOT = round(JD::correctVDOT($VDOT), 2);

		if ( $VDOT > (VDOT_FORM+3) )
			$icon = Icon::$FORM_UP;
		elseif ( $VDOT > (VDOT_FORM+1) )
			$icon = Icon::$FORM_UP_HALF;
		elseif ( $VDOT < (VDOT_FORM-3) )
			$icon = Icon::$FORM_DOWN;
		elseif ( $VDOT < (VDOT_FORM-1) )
			$icon = Icon::$FORM_DOWN_HALF;
		else
			$icon = Icon::$FORM_NORMAL;

		$title = $VDOT.': 3.000m in '.Helper::Prognosis(3, 0, $VDOT).',
			5 km in '.Helper::Prognosis(5, 0, $VDOT).',
			10 km in '.Helper::Prognosis(10, 0, $VDOT).',
			HM in '.Helper::Prognosis(21.1, 0, $VDOT);

		return Icon::get($icon, $title);
	}
}
?>