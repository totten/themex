<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 5                                                  |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2018                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
 */

namespace Civi\Themex;

class Hook {

  /**
   * A theme is a set of CSS files which are loaded on CiviCRM pages. To register a new
   * theme, add it to the $themes array. Use these properties:
   *
   *  - ext: string (required)
   *         The full name of the extension which defines the theme.
   *         Ex: "org.civicrm.themes.greenwich".
   *  - title: string (required)
   *         Visible title.
   *  - help: string (optional)
   *         Description of the theme's appearance.
   *  - url_callback: mixed (optional)
   *         A function ($themes, $themeKey, $cssExt, $cssFile) which returns the URL(s) for a CSS resource.
   *         Returns either an array of URLs or PASSTHRU.
   *         Ex: \Civi\Core\Themes\Resolvers::simple (default)
   *         Ex: \Civi\Core\Themes\Resolvers::none
   *  - prefix: string (optional)
   *         A prefix within the extension folder to prepend to the file name.
   *  - search_order: array (optional)
   *         A list of themes to search.
   *         Generally, the last theme should be "*fallback*" (Civi\Core\Themes::FALLBACK).
   *  - excludes: array (optional)
   *         A list of files (eg "civicrm:css/bootstrap.css" or "$ext:$file") which should never
   *         be returned (they are excluded from display).
   *
   * @param array $themes
   *   List of themes, keyed by name.
   * @return null
   *   the return value is ignored
   */
  public static function themes(&$themes) {
    \Civi::dispatcher()
      ->dispatch('hook_civicrm_themes', \Civi\Core\Event\GenericHookEvent::create([
        'themes' => &$themes,
      ]));
    return NULL;
    //    return self::singleton()->invoke(array('themes'), $themes,
    //      self::$_nullObject, self::$_nullObject, self::$_nullObject, self::$_nullObject, self::$_nullObject,
    //      'civicrm_themes'
    //    );
  }

  /**
   * The activeTheme hook determines which theme is active.
   *
   * @param string $theme
   *   The identifier for the theme. Alterable.
   *   Ex: 'greenwich'.
   * @param array $context
   *   Information about the current page-request. Includes some mix of:
   *   - page: the relative path of the current Civi page (Ex: 'civicrm/dashboard').
   *   - themes: an instance of the Civi\Core\Themes service.
   * @return null
   *   the return value is ignored
   */
  public static function activeTheme(&$theme, $context) {
    \Civi::dispatcher()
      ->dispatch('hook_civicrm_activeTheme', \Civi\Core\Event\GenericHookEvent::create([
        'theme' => &$theme,
        'context' => $context,
      ]));
    return NULL;

    //    return self::singleton()->invoke(array('theme', 'context'), $theme, $context,
    //      self::$_nullObject, self::$_nullObject, self::$_nullObject, self::$_nullObject,
    //      'civicrm_activeTheme'
    //    );
  }

  /**
   * @param array $bundles
   *  Each key is the name of bundle. For each, specificy:
   *    - ext: string|NULL, the default location in which to lookup resources (optional)
   *    - resources: array, each item is a string like:
   *      'js/foo.js' (relative to the default extension)
   *      or it can be
   * @return null
   */
  public static function resourceBundles(&$bundles) {
    \Civi::dispatcher()
      ->dispatch('hook_civicrm_resourceBundles', \Civi\Core\Event\GenericHookEvent::create([
        'bundles' => &$bundles,
      ]));
    return NULL;
  }

}
