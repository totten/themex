<?php

namespace Civi\Themex;

use Civi\Test\Api3TestTrait;
use CRM_Themex_ExtensionUtil as E;
use Civi\Test\HeadlessInterface;
use Civi\Test\HookInterface;
use Civi\Test\TransactionalInterface;

/**
 * Class ThemesTest
 * @group headless
 */
class ThemesTest extends \PHPUnit_Framework_TestCase implements HeadlessInterface, HookInterface, TransactionalInterface {

  use Api3TestTrait;

  public function setUpHeadless() {
    // Civi\Test has many helpers, like install(), uninstall(), sql(), and sqlFile().
    // See: https://github.com/civicrm/org.civicrm.testapalooza/blob/master/civi-test.md
    return \Civi\Test::headless()
      ->installMe(__DIR__)
      ->apply();
  }

  public function setUp() {
    // $this->useTransaction();
    \CRM_Core_Resources::singleton(\_themex_create_resources(\Civi::container()));
    \Civi::service('themes')->clear();
    parent::setUp();
  }

  public function getThemeExamples() {
    $cases = array();

    // --- Library of example themes which we can include in tests. ---

    $hookJudy = array(
      'judy' => array(
        'title' => 'Judy Garland',
        'ext' => 'org.civicrm.themex',
        'prefix' => 'tests/phpunit/Civi/Themex/Theme/judy/',
        'excludes' => array('test.extension.uitest-files/ignoreme.css'),
      ),
    );
    $hookLiza = array(
      'liza' => array(
        'title' => 'Liza Minnelli',
        'prefix' => 'tests/phpunit/Civi/Themex/Theme/liza/',
        'ext' => 'org.civicrm.themex',
      ),
    );
    $hookBlueMarine = array(
      'bluemarine' => array(
        'title' => 'Blue Marine',
        'url_callback' => array(__CLASS__, 'fakeCallback'),
        'ext' => 'org.civicrm.themex',
      ),
    );
    $hookAquaMarine = array(
      'aquamarine' => array(
        'title' => 'Aqua Marine',
        'url_callback' => array(__CLASS__, 'fakeCallback'),
        'ext' => 'org.civicrm.themex',
        'search_order' => array('aquamarine', 'bluemarine', '_fallback_'),
      ),
    );

    // --- Library of tests ---

    // Use the default theme, Greenwich.
    $cases[] = array(
      array(),
      'default',
      'Greenwich',
      array(
        'civicrm-css/civicrm.css' => array("%%CIVICRM_BASE_URL%%/css/civicrm.css"),
        'civicrm-css/joomla.css' => array("%%CIVICRM_BASE_URL%%/css/joomla.css"),
        'test.extension.uitest-files/foo.css' => array("%%THEMEX_BASE_URL%%/tests/extensions/test.extension.uitest/files/foo.css"),
      ),
    );

    // judy is defined. Let's use judy.
    $cases[] = array(
      // Example hook data
      $hookJudy,
      'judy',
      // Example theme to inspect
      'Judy Garland',
      array(
        'civicrm-css/civicrm.css' => array("%%THEMEX_BASE_URL%%/tests/phpunit/Civi/Themex/Theme/judy/css/civicrm.css"),
        'civicrm-css/joomla.css' => array("%%CIVICRM_BASE_URL%%/css/joomla.css"),
        'test.extension.uitest-files/foo.css' => array("%%THEMEX_BASE_URL%%/tests/extensions/test.extension.uitest/files/foo.css"),
        'test.extension.uitest-files/ignoreme.css' => array(), // excluded
      ),
    );

    // Misconfiguration: liza was previously used but then disappeared. Fallback to default, Greenwich.
    $cases[] = array(
      $hookJudy,
      'liza',
      'Greenwich',
      array(
        'civicrm-css/civicrm.css' => array("%%CIVICRM_BASE_URL%%/css/civicrm.css"),
        'civicrm-css/joomla.css' => array("%%CIVICRM_BASE_URL%%/css/joomla.css"),
        'test.extension.uitest-files/foo.css' => array("%%THEMEX_BASE_URL%%/tests/extensions/test.extension.uitest/files/foo.css"),
      ),
    );

    // We have some themes available, but the admin opted out.
    $cases[] = array(
      $hookJudy,
      'none',
      'None (Unstyled)',
      array(
        'civicrm-css/civicrm.css' => array(),
        'civicrm-css/joomla.css' => array("%%CIVICRM_BASE_URL%%/css/joomla.css"),
        'test.extension.uitest-files/foo.css' => array("%%THEMEX_BASE_URL%%/tests/extensions/test.extension.uitest/files/foo.css"),
      ),
    );

    // Theme which overrides an extension's CSS file.
    $cases[] = array(
      $hookJudy + $hookLiza,
      'liza',
      'Liza Minnelli',
      array(
        'civicrm-css/civicrm.css' => array("%%THEMEX_BASE_URL%%/tests/phpunit/Civi/Themex/Theme/liza/css/civicrm.css"),
        'civicrm-css/joomla.css' => array("%%CIVICRM_BASE_URL%%/css/joomla.css"),
        'test.extension.uitest-files/foo.css' => array("%%THEMEX_BASE_URL%%/tests/phpunit/Civi/Themex/Theme/liza/test.extension.uitest-files/foo.css"),
        // WARNING: If your local system has overrides for the **debug_enabled**, these results may vary.
        'civicrm-css/civicrm.min.css' => array("%%THEMEX_BASE_URL%%/tests/phpunit/Civi/Themex/Theme/liza/css/civicrm.min.css"),
      ),
    );

    // Theme has a custom URL-lookup function.
    $cases[] = array(
      $hookBlueMarine + $hookAquaMarine,
      'bluemarine',
      'Blue Marine',
      array(
        'civicrm-css/civicrm.css' => array('http://example.com/blue/civicrm.css'),
        'civicrm-css/joomla.css' => array("%%CIVICRM_BASE_URL%%/css/joomla.css"),
        'test.extension.uitest-files/foo.css' => array('http://example.com/blue/foobar/foo.css'),
      ),
    );

    // Theme is derived from another.
    $cases[] = array(
      $hookBlueMarine + $hookAquaMarine,
      'aquamarine',
      'Aqua Marine',
      array(
        'civicrm-css/civicrm.css' => array('http://example.com/aqua/civicrm.css'),
        'civicrm-css/joomla.css' => array("%%CIVICRM_BASE_URL%%/css/joomla.css"),
        'test.extension.uitest-files/foo.css' => array('http://example.com/blue/foobar/foo.css'),
      ),
    );

    return $cases;
  }

  /**
   * @param array $inputtedHook
   * @param string $themeKey
   * @param array $expectedUrls
   *   List of files to lookup plus the expected URLs.
   *   Array("{$extName}-{$fileName}" => "{$expectUrl}").
   * @dataProvider getThemeExamples
   */
  public function testTheme($inputtedHook, $themeKey, $expectedTitle, $expectedUrls) {
    \CRM_Utils_Hook::singleton()->setHook('civicrm_themes', function (&$themes) use ($inputtedHook) {
      foreach ($inputtedHook as $key => $value) {
        $themes[$key] = $value;
      }
    });

    \Civi::settings()->set('theme_frontend', $themeKey);
    \Civi::settings()->set('theme_backend', $themeKey);

    /** @var \Civi\Themex\Themes $themeSvc */
    $themeSvc = \Civi::service('themes');
    $theme = $themeSvc->get($themeSvc->getActiveThemeKey());
    if ($expectedTitle) {
      $this->assertEquals($expectedTitle, $theme['title']);
    }

    foreach ($expectedUrls as $inputFile => $expectedUrl) {
      $expectedUrl = $this->interpolateAll($expectedUrl);
      list ($ext, $file) = explode('-', $inputFile, 2);
      $actualUrl = $themeSvc->resolveUrls($themeSvc->getActiveThemeKey(), $ext, $file);
      foreach (array_keys($actualUrl) as $k) {
        // Ignore cache revision key (`?r=abcd1234`).
        list ($actualUrl[$k]) = explode('?', $actualUrl[$k], 2);
      }
      $this->assertEquals($expectedUrl, $actualUrl, "Check URL for $inputFile");
    }
  }

  public static function fakeCallback($themes, $themeKey, $cssExt, $cssFile) {
    $map['bluemarine']['civicrm']['css/bootstrap.css'] = array('http://example.com/blue/bootstrap.css');
    $map['bluemarine']['civicrm']['css/civicrm.css'] = array('http://example.com/blue/civicrm.css');
    $map['bluemarine']['test.extension.uitest']['files/foo.css'] = array('http://example.com/blue/foobar/foo.css');
    $map['aquamarine']['civicrm']['css/civicrm.css'] = array('http://example.com/aqua/civicrm.css');
    return isset($map[$themeKey][$cssExt][$cssFile]) ? $map[$themeKey][$cssExt][$cssFile] : Themes::PASSTHRU;
  }

  public function testGetAll() {
    $all = \Civi::service('themes')->getAll();
    $this->assertTrue(isset($all['greenwich']));
    $this->assertTrue(isset($all['_fallback_']));
  }

  public function testGetAvailable() {
    $all = \Civi::service('themes')->getAvailable();
    $this->assertTrue(isset($all['greenwich']));
    $this->assertFalse(isset($all['_fallback_']));
  }

  public function testApiOptions() {
    $result = $this->callAPISuccess('Setting', 'getoptions', array(
      'field' => 'theme_backend',
    ));
    $this->assertTrue(isset($result['values']['greenwich']));
    $this->assertFalse(isset($result['values']['_fallback_']));
  }

  /**
   * @param array $array
   *   Ex: [0 => '%%FOO%%/bar']
   * @return array
   *   Ex: [1 => '/the/real/foo/bar']
   */
  private function interpolateAll($array) {
    $vars = [
      '%%CIVICRM_BASE_URL%%' => '',
      '%%THEMEX_BASE_URL%%' => rtrim(\Civi::resources()->getUrl('org.civicrm.themex'), '/'),
    ];

    $result = [];
    foreach ($array as $key => $str) {
      $result[$key] = strtr($str, $vars);
    }
    return $result;
  }

}
