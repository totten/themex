<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 5                                                  |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2016                                |
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

namespace Civi\Themex\Themes;

use Civi;
use Civi\Themex\Themes;

/**
 * Perform a scan to load a list of available themes.
 *
 * Note: The outputs of the loader should be cached -- so this class-file is not
 * needed during a typical page-request.
 *
 * @package CiviCRM_Hook
 * @copyright CiviCRM LLC (c) 2004-2016
 */
class Loader {

  /**
   * Build the list of available themes.
   *
   * @return array
   *   List of themes, keyed by name.
   * @see Hook::themes
   */
  public function findAll() {
    $themes = array(
      'default' => array(
        'ext' => 'civicrm',
        'title' => ts('Automatic'),
        'help' => ts('Determine a system default automatically'),
        // This is an alias. url_callback, search_order don't matter.
      ),
      'greenwich' => array(
        'ext' => 'civicrm',
        'title' => 'Greenwich',
        'help' => ts('CiviCRM 4.x look-and-feel'),
      ),
      'none' => array(
        'ext' => 'civicrm',
        'title' => ts('None (Unstyled)'),
        'help' => ts('Disable CiviCRM\'s built-in CSS files.'),
        'search_order' => array('none', Themes::FALLBACK_THEME),
        'excludes' => array(
          "css/civicrm.css",
          "css/bootstrap.css",
        ),
      ),
      Themes::FALLBACK_THEME => array(
        'ext' => 'civicrm',
        'title' => 'Fallback (Abstract Base Theme)',
        'url_callback' => '\Civi\Themex\Themes\Resolvers::fallback',
        'search_order' => array(Themes::FALLBACK_THEME),
      ),
    );

    Civi\Themex\Hook::themes($themes);

    foreach (array_keys($themes) as $themeKey) {
      $themes[$themeKey] = $this->build($themeKey, $themes[$themeKey]);
    }

    return $themes;
  }

  /**
   * Apply defaults for a given theme.
   *
   * @param string $themeKey
   *   The name of the theme. Ex: 'greenwich'.
   * @param array $theme
   *   The original theme definition of the theme (per Hook::themes).
   * @return array
   *   The full theme definition of the theme (per Hook::themes).
   * @see Hook::themes
   */
  public function build($themeKey, $theme) {
    $defaults = array(
      'name' => $themeKey,
      'url_callback' => '\Civi\Themex\Themes\Resolvers::simple',
      'search_order' => array($themeKey, Themes::FALLBACK_THEME),
    );
    $theme = array_merge($defaults, $theme);

    return $theme;
  }

}
