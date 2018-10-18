<?php

use CRM_Themex_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Themex_Form_ThemeAdmin extends CRM_Admin_Form_Preferences {

  protected $_settings = array(
    'theme_backend' => CRM_Core_BAO_Setting::SYSTEM_PREFERENCES_NAME,
    'theme_frontend' => CRM_Core_BAO_Setting::SYSTEM_PREFERENCES_NAME,
  );

  public function preProcess() {
    $this->_varNames = array();
    parent::preProcess();
  }

}
