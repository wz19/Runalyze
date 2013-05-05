<?php
/**
 * This file contains class::ImporterFiletypeHRM
 * @package Runalyze\Importer\Filetype
 */
ImporterWindowTabUpload::addInfo('Gleichnamige hrm- und gpx-Dateien werden automatisch zusammengef&uuml;hrt.');
/**
 * Importer: *.hrm
 * 
 * Files of *.hrm are from Polar
 *
 * @author Hannes Christiansen
 * @package Runalyze\Importer\Filetype
 */
class ImporterFiletypeHRM extends ImporterFiletypeAbstract {
	/**
	 * Set parser
	 * @param string $String string to parse
	 */
	protected function setParserFor($String) {
		$this->Parser = new ParserHRMSingle($String);
	}
}