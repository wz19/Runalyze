<?php
/**
 * This file contains class::SectionMiscellaneousRow
 * @package Runalyze\DataObjects\Training\View\Section
 */

use Runalyze\View\Activity;
use Runalyze\Model\Trackdata;

/**
 * Row: Miscellaneous
 * 
 * @author Hannes Christiansen
 * @package Runalyze\DataObjects\Training\View\Section
 */
class SectionMiscellaneousRow extends TrainingViewSectionRowTabbedPlot {
	/**
	 * Right content: notes
	 * @var string
	 */
	protected $NotesContent = '';

	/**
	 * Set content
	 */
	protected function setContent() {
		//$this->withShadow = true;

		$this->setBoxedValues();
	}

	/**
	 * Set content right
	 */
	protected function setRightContent() {
		$this->fillNotesContent();
		$this->addRightContent('notes', __('Additional notes'), $this->NotesContent);

		if ($this->Context->trackdata()->has(Trackdata\Object::CADENCE)) {
			$Plot = new Activity\Plot\Cadence($this->Context);
			$this->addRightContent('cadence', __('Cadence plot'), $Plot);
		}

		if ($this->Context->trackdata()->has(Trackdata\Object::VERTICAL_OSCILLATION)) {
			$Plot = new Activity\Plot\VerticalOscillation($this->Context);
			$this->addRightContent('verticaloscillation', __('Oscillation plot'), $Plot);
		}

		if ($this->Context->trackdata()->has(Trackdata\Object::GROUNDCONTACT)) {
			$Plot = new Activity\Plot\GroundContact($this->Context);
			$this->addRightContent('groundcontact', __('Ground contact plot'), $Plot);
		}

		if ($this->Context->trackdata()->has(Trackdata\Object::POWER)) {
			$Plot = new Activity\Plot\Power($this->Context);
			$this->addRightContent('power', __('Power plot'), $Plot);
		}

		if ($this->Context->trackdata()->has(Trackdata\Object::TEMPERATURE)) {
			$Plot = new Activity\Plot\Temperature($this->Context);
			$this->addRightContent('temperature', __('Temperature plot'), $Plot);
		}
	}

	/**
	 * Set boxed values
	 */
	protected function setBoxedValues() {
		$this->addDateAndTime();
		$this->addCadenceAndPower();
		$this->addRunningDynamics();
		$this->addWeather();
		$this->addEquipment();
		$this->addTrainingPartner();
	}

	/**
	 * Add date and time
	 */
	protected function addDateAndTime() {
		$Date = new BoxedValue($this->Context->dataview()->date(), '', __('Date'));

		if ($this->Context->dataview()->daytime() != '') {
			$Daytime = new BoxedValue($this->Context->dataview()->daytime(), '', __('Time of day'));
			$Daytime->defineAsFloatingBlock('w50');
			$Date->defineAsFloatingBlock('w50');

			$this->BoxedValues[] = $Date;
			$this->BoxedValues[] = $Daytime;
		} else {
			$Date->defineAsFloatingBlock('w100');
			$this->BoxedValues[] = $Date;
		}
	}

	/**
	 * Add cadence and power
	 */
	protected function addCadenceAndPower() {
		if ($this->Context->activity()->cadence() > 0 || $this->Context->activity()->power() > 0) {
			$Cadence = new BoxedValue(Helper::Unknown($this->Context->dataview()->cadence()->value(), '-'), $this->Context->dataview()->cadence()->unitAsString(), $this->Context->dataview()->cadence()->label());
			$Cadence->defineAsFloatingBlock('w50');

			$Power = new BoxedValue(Helper::Unknown($this->Context->activity()->power(), '-'), 'W', __('Power'));
			$Power->defineAsFloatingBlock('w50');

			$this->BoxedValues[] = $Cadence;
			$this->BoxedValues[] = $Power;
		}
	}

	/**
	 * Add running dynamics
	 */
	protected function addRunningDynamics() {
		if ($this->Context->activity()->groundcontact() > 0 || $this->Context->activity()->verticalOscillation() > 0) {
			$Contact = new BoxedValue(Helper::Unknown($this->Context->activity()->groundcontact(), '-'), 'ms', __('Ground contact'));
			$Contact->defineAsFloatingBlock('w50');

			$Oscillation = new BoxedValue(Helper::Unknown(round($this->Context->activity()->verticalOscillation()/10, 1), '-'), 'cm', __('Vertical oscillation'));
			$Oscillation->defineAsFloatingBlock('w50');

			$this->BoxedValues[] = $Contact;
			$this->BoxedValues[] = $Oscillation;
		}
	}

	/**
	 * Add weather
	 */
	protected function addWeather() {
		if (!$this->Context->activity()->weather()->isEmpty()) {
			$Weather = new BoxedValue($this->Context->activity()->weather()->condition()->string(), '', __('Weather condition'), $this->Context->activity()->weather()->condition()->icon()->code());
			$Weather->defineAsFloatingBlock('w50');

			$Temp = new BoxedValue($this->Context->activity()->weather()->temperature()->asStringWithoutUnit(), $this->Context->activity()->weather()->temperature()->unit(), __('Temperature'));
			$Temp->defineAsFloatingBlock('w50');

			$this->BoxedValues[] = $Weather;
			$this->BoxedValues[] = $Temp;
		}

		if (!$this->Context->activity()->clothes()->isEmpty()) {
			$Clothes = new BoxedValue($this->Context->dataview()->clothesAsLinks(), '', __('Clothes'));
			$Clothes->defineAsFloatingBlock('w100 flexible-height');

			$this->BoxedValues[] = $Clothes;
		}
	}

	/**
	 * Add equipment
	 */
	protected function addEquipment() {
		$id = $this->Context->activity()->shoeID();

		if ($id) {
			$Shoe = new Shoe($id);

			if (!$Shoe->isDefaultId()) {
				$RunningShoe = new BoxedValue($Shoe->getSearchLink(), '', __('Running shoe'));
				$RunningShoe->defineAsFloatingBlock('w100 flexible-height');

				$this->BoxedValues[] = $RunningShoe;
			}
		}
	}

	/**
	 * Add training partner
	 */
	protected function addTrainingPartner() {
		if (!$this->Context->activity()->partner()->isEmpty()) {
			$TrainingPartner = new BoxedValue($this->Context->dataview()->partnerAsLinks(), '', __('Training partner'));
			$TrainingPartner->defineAsFloatingBlock('w100 flexible-height');

			$this->BoxedValues[] = $TrainingPartner;
		}
	}

	/**
	 * Fill notes content
	 */
	protected function fillNotesContent() {
		$this->NotesContent = '<div class="panel-content">';

		$this->addNotes();
		$this->addCreationAndModificationTime();

		$this->NotesContent .= '</div>';
	}

	/**
	 * Add notes
	 */
	protected function addNotes() {
		if ($this->Context->activity()->notes() != '') {
			$Notes = '<strong>'.__('Notes').':</strong><br>'.$this->Context->dataview()->notes();
			$this->NotesContent .= HTML::fileBlock($Notes);
		}
	}

	/**
	 * Add created/edited
	 */
	protected function addCreationAndModificationTime() {
		$created = $this->Context->activity()->get(\Runalyze\Model\Activity\Object::TIMESTAMP_CREATED);
		$edited = $this->Context->activity()->get(\Runalyze\Model\Activity\Object::TIMESTAMP_EDITED);

		if ($created > 0 || $edited > 0) {
			$CreationTime = ($created == 0) ? '' : sprintf( __('You created this training on <strong>%s</strong> at <strong>%s</strong>.'),
				date('d.m.Y', $created),
				date('H:i', $created)
			);

			$ModificationTime = ($edited == 0) ? '' : '<br>'.sprintf( __('Last modification on <strong>%s</strong> at <strong>%s</strong>.'),
				date('d.m.Y', $edited),
				date('H:i', $edited)
			);

			$this->NotesContent .= HTML::fileBlock($CreationTime.$ModificationTime);
		}
	}
}