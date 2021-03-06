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

namespace Civi\Themex;

use Civi;

/**
 *
 * @package CiviCRM_Hook
 * @copyright CiviCRM LLC (c) 2004-2016
 */
class Themes {

  /**
   * The "default" theme adapts based on the latest recommendation from civicrm.org
   * by switching to DEFAULT_THEME at runtime.
   */
  const DEFAULT_THEME = 'greenwich';

  /**
   * Fallback is a pseudotheme which can be included in "search_order".
   * It locates files in the core/extension (non-theme) codebase.
   */
  const FALLBACK_THEME = '_fallback_';

  const PASSTHRU = 'PASSTHRU';

  /**
   * @var string
   *   Ex: 'judy', 'liza'.
   */
  private $activeThemeKey = NULL;

  /**
   * @var array
   *   Array(string $themeKey => array $themeSpec).
   */
  private $themes = NULL;

  /**
   * @var \CRM_Utils_Cache_Interface
   */
  private $cache = NULL;

  /**
   * Theme constructor.
   * @param \CRM_Utils_Cache_Interface $cache
   */
  public function __construct($cache = NULL) {
    $this->cache = $cache ? $cache : Civi::cache('long');
  }

  public function clear() {
    $this->cache->delete($this->getCacheKey());
    $this->activeThemeKey = NULL;
    $this->themes = NULL;
  }

  /**
   * Determine the name of active theme.
   *
   * @return string
   *   Ex: "greenwich".
   */
  public function getActiveThemeKey() {
    if ($this->activeThemeKey === NULL) {
      // Ambivalent: is it better to use $config->userFrameworkFrontend or $template->get('urlIsPublic')?
      $config = \CRM_Core_Config::singleton();
      $settingKey = $config->userFrameworkFrontend ? 'theme_frontend' : 'theme_backend';

      $themeKey = Civi::settings()->get($settingKey);
      if ($themeKey === 'default') {
        $themeKey = self::DEFAULT_THEME;
      }

      Hook::activeTheme($themeKey, [
        'themes' => $this,
        'page' => \CRM_Utils_Array::value(\CRM_Core_Config::singleton()->userFrameworkURLVar, $_GET),
      ]);

      $themes = $this->getAll();
      $this->activeThemeKey = isset($themes[$themeKey]) ? $themeKey : self::DEFAULT_THEME;
    }
    return $this->activeThemeKey;
  }

  /**
   * Get the definition of the theme.
   *
   * @param string $themeKey
   *   Ex: 'greenwich', 'shoreditch'.
   * @return array|NULL
   * @see Hook::themes
   */
  public function get($themeKey) {
    $all = $this->getAll();
    return isset($all[$themeKey]) ? $all[$themeKey] : NULL;
  }

  /**
   * Get a list of all known themes, including hidden base themes.
   *
   * @return array
   *   List of themes, keyed by name. Same format as Hook::themes(),
   *   but any default values are filled in.
   * @see Hook::themes
   */
  public function getAll() {
    if ($this->themes === NULL) {
      // Cache includes URLs/paths, which change with runtime.
      $cacheKey = $this->getCacheKey();
      $this->themes = $this->cache->get($cacheKey);
      if ($this->themes === NULL) {
        $loader = new Civi\Themex\Themes\Loader();
        $this->themes = $loader->findAll();
        $this->cache->set($cacheKey, $this->themes);
      }
    }
    return $this->themes;
  }

  /**
   * Get a list of available themes, excluding hidden base themes.
   *
   * This is the same as getAll(), but abstract themes like "_fallback_"
   * or "_newyork_base_" are omitted.
   *
   * @return array
   *   List of themes.
   *   Ex: ['greenwich' => 'Greenwich', 'shoreditch' => 'Shoreditch'].
   * @see Hook::themes
   */
  public function getAvailable() {
    $result = array();
    foreach ($this->getAll() as $key => $theme) {
      if ($key{0} !== '_') {
        $result[$key] = $theme['title'];
      }
    }
    return $result;
  }

  /**
   * Get the URL(s) for a themed CSS file.
   *
   * This implements a prioritized search, in order:
   *  - Check for the specified theme.
   *  - If that doesn't exist, check for the default theme.
   *  - If that doesn't exist, use the 'none' theme.
   *
   * @param string $active
   *   Active theme key.
   *   Ex: 'greenwich'.
   * @param string $cssExt
   *   Ex: 'civicrm'.
   * @param string $cssFile
   *   Ex: 'css/bootstrap.css' or 'css/civicrm.css'.
   * @return array
   *   List of URLs to display.
   *   Ex: array(string $url)
   */
  public function resolveUrls($active, $cssExt, $cssFile) {
    $all = $this->getAll();
    if (!isset($all[$active])) {
      return array();
    }

    $cssId = $this->cssId($cssExt, $cssFile);

    foreach ($all[$active]['search_order'] as $themeKey) {
      if (isset($all[$themeKey]['excludes']) && in_array($cssId, $all[$themeKey]['excludes'])) {
        $result = array();
      }
      else {
        $result = Civi\Core\Resolver::singleton()
          ->call($all[$themeKey]['url_callback'], array($this, $themeKey, $cssExt, $cssFile));
      }

      if ($result !== self::PASSTHRU) {
        return $result;
      }
    }

    throw new \RuntimeException("Failed to resolve URL. Theme metadata may be incomplete.");
  }

  /**
   * @param string $cssExt
   * @param string $cssFile
   * @return string
   */
  public function cssId($cssExt, $cssFile) {
    return ($cssExt === 'civicrm') ? $cssFile : "$cssExt-$cssFile";
  }

  /**
   * @return string
   */
  private function getCacheKey() {
    return 'theme_list_' . \CRM_Core_Config_Runtime::getId();
  }

}
