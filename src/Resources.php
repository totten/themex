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

}
