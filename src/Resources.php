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

class Resources extends \CRM_Core_Resources {

  // --------------------------- Theming support ---------------------------

  /**
   * Add a CSS file to the current page using <LINK HREF>.
   *
   * @param string $ext
   *   extension name; use 'civicrm' for core.
   * @param string $file
   *   file path -- relative to the extension base dir.
   * @param int $weight
   *   relative weight within a given region.
   * @param string $region
   *   location within the file; 'html-header', 'page-header', 'page-footer'.
   * @return \CRM_Core_Resources
   */
  public function addStyleFile($ext, $file, $weight = self::DEFAULT_WEIGHT, $region = self::DEFAULT_REGION) {
    /** @var Themes $themes */
    $themes = \Civi::service('themes');
    foreach ($themes->resolveUrls($themes->getActiveThemeKey(), $ext, $file) as $url) {
      $this->addStyleUrl($url, $weight, $region);
    }
    return $this;
  }

  /**
   * Determine the minified file name.
   *
   * @param string $ext
   * @param string $file
   * @return string
   *   An updated $fileName. If a minified version exists and is supported by
   *   system policy, the minified version will be returned. Otherwise, the original.
   */
  public function filterMinify($ext, $file) {
    if (\CRM_Core_Config::singleton()->debug && strpos($file, '.min.') !== FALSE) {
      $nonMiniFile = str_replace('.min.', '.', $file);
      if ($this->getPath($ext, $nonMiniFile)) {
        $file = $nonMiniFile;
      }
    }
    return $file;
  }

  // --------------------------- Allow paths/URLs based on [vars] and URLs ---------------------------

  /**
   * Modify the behavior of getPath() -- $file *may* be an absolute-ish
   * expression like "[civicrm.root]/foo" or "ext://foo/bar".
   *
   * @param string $ext
   * @param string $file
   * @return bool|mixed|string
   * @throws \Exception
   */
  public function getPath($ext, $file = NULL) {
    if ($file{0} === '[') {
      $path = \Civi::paths()->getPath($file);
      // Ugh, don't like this file-checking behavior, but it's the contract...
      return (is_file($path) ? $path : FALSE);
    }
    if (strpos($file, '://') !== FALSE) {
      $url = parse_url($file);
      switch ($url['scheme']) {
        case 'ext':
          return $this->getPath($url['host'], $url['path']);

        case 'assetBuilder':
          // TODO // return \Civi::service('assetBuilder')->getPath(...);
          throw new \Exception("Not implemented: assetBuilder://");
      }
    }
    return parent::getPath($ext, $file);
  }

  /**
   * Modify the behavior of getUrl() -- $file *may* be an absolute-ish
   * expression like "[civicrm.root]/foo" or "ext://foo/bar".
   *
   * @param string $ext
   * @param null $file
   * @param bool $addCacheCode
   * @return mixed|string
   * @throws \Exception
   */
  public function getUrl($ext, $file = NULL, $addCacheCode = FALSE) {
    if ($file{0} === '[') {
      return \Civi::paths()->getUrl($file);
    }
    if (strpos($file, '://') !== FALSE) {
      $url = parse_url($file);
      switch ($url['scheme']) {
        case 'ext':
          return $this->getUrl($url['host'], $url['path']);

        case 'assetBuilder':
          // TODO // return \Civi::service('assetBuilder')->getUrl(...);
          throw new \Exception("Not implemented: assetBuilder://");
      }
    }
    return parent::getUrl($ext, $file, $addCacheCode);
  }

  // --------------------------- Bundle support ---------------------------

  const BUNDLE_TTL = 60 * 60;

  private $bundles;

  /**
   * @param string $ext
   * @param string $res
   * @return Resources
   * @throws \CRM_Core_Exception
   */
  public function addResourceFile($ext, $res) {
    if (preg_match(';\.css(\?.*)$;', $res)) {
      $this->addStyleFile($ext, $res);
    }
    elseif (preg_match(';\.js(\?.*)$;', $res)) {
      $this->addScriptFile($ext, $res);
    }
    else {
      throw new \CRM_Core_Exception("Unrecognized resource file: ($ext, $res)");
    }
    return $this;
  }

  /**
   * @param string|array $bundle
   *   The name of the bundle, or an array defining the bundle.
   * @return Resources
   * @throws \CRM_Core_Exception
   */
  public function addBundle($bundle) {
    if (is_string($bundle)) {
      $bundles = $this->getBundles();
      if (!isset($bundles[$bundle]['resources'])) {
        throw new \CRM_Core_Exception("Cannot load unknown bundle: $bundle");
      }
      $bundle = $bundles[$bundle];
    }
    $ext = $bundle['ext'] ?: NULL;
    foreach ($bundle['resources'] as $res) {
      $this->addResourceFile($ext, $res);
    }

    return $this;
  }

  public function getBundles() {
    if ($this->bundles === NULL) {
      $cache = \Civi::cache('long');
      $cacheKey = 'themex_resources_bundles';
      $this->bundles = $cache->get($cacheKey);
      if ($this->bundles === NULL || \Civi::settings()->get('debug_enabled')) {
        $this->bundles = [];
        Hook::resourceBundles($this->bundles);
        $cache->set($cacheKey, $this->bundles, self::BUNDLE_TTL);
      }
    }
    return $this->bundles;
  }

}
