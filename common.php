<?php

/**
 * @file Common functions for contact_form section
 */

/**
 * The section type machine name.
 * @var String
 */
define('SECTION_TYPE', 'contact_form');

// --------------------- GLOBAL -----------------------

global $config, $addonPathCode;

// Load language translation files:
// Include default language file
require($addonPathCode . '/_types/contact_form/language/en.php');

// Include language file by the configured TS language.
$language = $config['language'];
if(!empty($language) &&  file_exists($addonPathCode.'/_types/contact_form/language/' . $language . '.php')){
  require($addonPathCode.'/_types/contact_form/language/'.$language.'.php');
}
