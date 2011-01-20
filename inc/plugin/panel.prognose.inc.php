<?php
/**
 * This file contains the panel-plugin "Prognose".
 * 
 * @author Hannes Christiansen <mail@laufhannes.de>
 * @version 1.0
 * @uses functions.php::prognose()
 *
 * Last modified 2010/08/09 22:08 by Hannes Christiansen
 */
/**
 * Plugin-installer, will be called by class::Plugin for installing this plugin.
 */
function prognose_installer() {
	$type = 'panel';
	$filename = 'panel.prognose.inc.php';
	$name = 'Prognose';
	$description = 'Anzeige der aktuellen Wettkampfprognose.';
	// TODO Include the plugin-installer
}

/**
 * Sets the right symbol in the h1-header of this panel
 * @return string (HTML)
 */
function prognose_rightSymbol() {
	return '';
}

/**
 * Display-function for this plugin, will be called by class::Panel::display()
 */
function prognose_display() {
	global $global, $config;

	Error::getInstance()->add('TODO','Add Zwischenzeiten/Marschtabelle',__FILE__,__LINE__);

	echo Helper::Prognose(1, true);
	echo Helper::Prognose(3, true);
	echo Helper::Prognose(5);
	echo Helper::Prognose(10);
	echo Helper::Prognose(21.1);
	echo Helper::Prognose(42.2);
}
?>