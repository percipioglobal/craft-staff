<?php
/**
 * staff-management plugin for Craft CMS 3.x
 *
 * Craft Staff Management provides an HR solution for payroll and benefits
 *
 * @link      http://percipio.london
 * @copyright Copyright (c) 2021 Percipio
 */

namespace percipiolondon\craftstaff;

use percipiolondon\craftstaff\services\Employers as EmployersService;
use percipiolondon\craftstaff\services\Employees as EmployeesService;
use percipiolondon\craftstaff\services\PayRun as PayRunService;
use percipiolondon\craftstaff\services\PayRunEntries as PayRunEntriesService;
use percipiolondon\craftstaff\services\HardingUsers as HardingUsersService;
use percipiolondon\craftstaff\models\Settings;
use percipiolondon\craftstaff\elements\Employer as EmployerElement;
use percipiolondon\craftstaff\elements\Employee as EmployeeElement;
use percipiolondon\craftstaff\elements\PayRun as PayRunElement;
use percipiolondon\craftstaff\elements\PayRunEntry as PayRunEntryElement;
use percipiolondon\craftstaff\elements\HardingUser as HardingUserElement;
use percipiolondon\craftstaff\plugin\Services as StaffServices;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\console\Application as ConsoleApplication;
use craft\web\UrlManager;
use craft\services\Elements;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;

use yii\base\Event;

/**
 * Craft plugins are very much like little applications in and of themselves. We’ve made
 * it as simple as we can, but the training wheels are off. A little prior knowledge is
 * going to be required to write a plugin.
 *
 * For the purposes of the plugin docs, we’re going to assume that you know PHP and SQL,
 * as well as some semi-advanced concepts like object-oriented programming and PHP namespaces.
 *
 * https://docs.craftcms.com/v3/extend/
 *
 * @author    Percipio
 * @package   Craftstaff
 * @since     1.0.0-alpha.1
 *
 * @property  EmployersService $employers
 * @property  EmployeesService $employees
 * @property  PayRunService $payRun
 * @property  PayRunEntriesService $payRunEntries
 * @property  HardingUsersService $hardingUsers
 * @property  Settings $settings
 * @method    Settings getSettings()
 */
class Craftstaff extends Plugin
{
    use StaffServices;

    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * Craftstaff::$plugin
     *
     * @var Craftstaff
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
    public $schemaVersion = '1.0.0-alpha.1';

    /**
     * Set to `true` if the plugin should have a settings view in the control panel.
     *
     * @var bool
     */
    public $hasCpSettings = true;

    /**
     * Set to `true` if the plugin should have its own section (main nav item) in the control panel.
     *
     * @var bool
     */
    public $hasCpSection = true;

    // Public Methods
    // =========================================================================

    /**
     * Set our $plugin static property to this class so that it can be accessed via
     * Craftstaff::$plugin
     *
     * Called after the plugin class is instantiated; do any one-time initialization
     * here such as hooks and events.
     *
     * If you have a '/vendor/autoload.php' file, it will be loaded for you automatically;
     * you do not need to load it in your init() method.
     *
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;
        $this->_setPluginComponents();

        // Add in our console commands
        if (Craft::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'percipiolondon\craftstaff\console\controllers';
        }

        // Register our CP routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['staff-management/employers/fetch'] = 'staff-management/employer-controller/fetch';
                $event->rules['staff-management/employees/fetch'] = 'staff-management/employee-controller/fetch';
            }
        );

        // Register our elements
        Event::on(
            Elements::class,
            Elements::EVENT_REGISTER_ELEMENT_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = EmployerElement::class;
                $event->types[] = EmployeeElement::class;
                $event->types[] = PayRunElement::class;
                $event->types[] = PayRunEntryElement::class;
                $event->types[] = HardingUserElement::class;
            }
        );

        // Do something after we're installed
        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                    // We were just installed
                }
            }
        );

/**
 * Logging in Craft involves using one of the following methods:
 *
 * Craft::trace(): record a message to trace how a piece of code runs. This is mainly for development use.
 * Craft::info(): record a message that conveys some useful information.
 * Craft::warning(): record a warning message that indicates something unexpected has happened.
 * Craft::error(): record a fatal error that should be investigated as soon as possible.
 *
 * Unless `devMode` is on, only Craft::warning() & Craft::error() will log to `craft/storage/logs/web.log`
 *
 * It's recommended that you pass in the magic constant `__METHOD__` as the second parameter, which sets
 * the category to the method (prefixed with the fully qualified class name) where the constant appears.
 *
 * To enable the Yii debug toolbar, go to your user account in the AdminCP and check the
 * [] Show the debug toolbar on the front end & [] Show the debug toolbar on the Control Panel
 *
 * http://www.yiiframework.com/doc-2.0/guide-runtime-logging.html
 */
        Craft::info(
            Craft::t(
                'staff-management',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates and returns the model used to store the plugin’s settings.
     *
     * @return \craft\base\Model|null
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * Returns the rendered settings HTML, which will be inserted into the content
     * block on the settings page.
     *
     * @return string The rendered settings HTML
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'staff-management/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function getCpNavItem(): array
    {
        $nav = parent::getCpNavItem();

        $nav['label'] = Craft::t('staff-management', 'Staff Management');

        $nav['subnav']['dashboard'] = [
            'label' => Craft::t('staff-management', 'Dashboard'),
            'url' => 'staff-management'
        ];

        if (Craft::$app->getUser()->checkPermission('companies-mangeCompanies')) {
            $nav['subnav']['employers'] = [
                'label' => Craft::t('staff-management', 'Employers'),
                'url' => 'staff-management/employers'
            ];

            $nav['subnav']['employees'] = [
                'label' => Craft::t('staff-management', 'Employees'),
                'url' => 'staff-management/employees'
            ];

            $nav['subnav']['payrun'] = [
                'label' => Craft::t('staff-management', 'Pay Run'),
                'url' => 'staff-management/payrun'
            ];
        }

        return $nav;
    }
}