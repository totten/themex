<?php

require_once 'themex.civix.php';
use CRM_Themex_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function themex_civicrm_config(&$config) {
  _themex_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function themex_civicrm_xmlMenu(&$files) {
  _themex_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function themex_civicrm_install() {
  _themex_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function themex_civicrm_postInstall() {
  _themex_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function themex_civicrm_uninstall() {
  _themex_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function themex_civicrm_enable() {
  _themex_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function themex_civicrm_disable() {
  _themex_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function themex_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _themex_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function themex_civicrm_managed(&$entities) {
  _themex_civix_civicrm_managed($entities);
}

///**
// * Implements hook_civicrm_caseTypes().
// *
// * Generate a list of case-types.
// *
// * Note: This hook only runs in CiviCRM 4.4+.
// *
// * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
// */
//function themex_civicrm_caseTypes(&$caseTypes) {
//  _themex_civix_civicrm_caseTypes($caseTypes);
//}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function themex_civicrm_angularModules(&$angularModules) {
  _themex_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function themex_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _themex_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

///**
// * Implements hook_civicrm_entityTypes().
// *
// * Declare entity types provided by this module.
// *
// * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_entityTypes
// */
//function themex_civicrm_entityTypes(&$entityTypes) {
//  _themex_civix_civicrm_entityTypes($entityTypes);
//}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_container().
 */
function themex_civicrm_container(\Symfony\Component\DependencyInjection\ContainerBuilder $container) {
  $container->addResource(new \Symfony\Component\Config\Resource\FileResource(__FILE__));

  $container->setDefinition('resources', new \Symfony\Component\DependencyInjection\Definition(
    'CRM_Core_Resources',
    [new \Symfony\Component\DependencyInjection\Reference('service_container')]
  ))->setFactory('_themex_create_resources');

  $container->setDefinition('themes', new \Symfony\Component\DependencyInjection\Definition(
    'Civi\Themex\Themes',
    array()
  ));
}

function _themex_create_resources($container) {
  $sys = \CRM_Extension_System::singleton();
  return new Civi\Themex\Resources(
    $sys->getMapper(),
    $container->get('cache.js_strings'),
    \CRM_Core_Config::isUpgradeMode() ? NULL : 'resCacheCode'
  );
}
