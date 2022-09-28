<?php

/**
 * This bootstrap file connects the module to the OpenEMR system hooking to the API, api scopes, and event notifications
 *
 * @package openemr
 * @link      http://www.open-emr.org
 * @author    Stephen Nielson <stephen@nielson.org>
 * @copyright Copyright (c) 2021 Comlink
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

namespace Comlink\OpenEMR\Modules\TeleHealthModule;

use Comlink\OpenEMR\Modules\TeleHealthModule\Admin\TeleHealthUserAdminController;
use Comlink\OpenEMR\Modules\TeleHealthModule\Repository\TeleHealthPersonSettingsRepository;
use Comlink\OpenEMR\Modules\TeleHealthModule\Repository\TeleHealthProviderRepository;
use OpenEMR\Common\Logging\SystemLogger;
use OpenEMR\Common\Twig\TwigContainer;
use OpenEMR\Core\Kernel;
use OpenEMR\Events\Appointments\AppointmentSetEvent;
use OpenEMR\Events\Core\TwigEnvironmentEvent;
use OpenEMR\Events\Globals\GlobalsInitializedEvent;
use OpenEMR\Events\Main\Tabs\RenderEvent;
use OpenEMR\Events\User\UserCreatedEvent;
use OpenEMR\Events\User\UserEditRenderEvent;
use OpenEMR\Events\User\UserUpdatedEvent;
use OpenEMR\Services\Globals\GlobalSetting;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Note the below use statements are importing classes from the OpenEMR core codebase
 */
use OpenEMR\Menu\MenuEvent;
use OpenEMR\Events\RestApiExtend\RestApiCreateEvent;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Contracts\EventDispatcher\Event;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Bootstrap
{
    const OPENEMR_GLOBALS_LOCATION = "../../../../globals.php";
    const MODULE_INSTALLATION_PATH = "/interface/modules/custom_modules/";
    const MODULE_NAME = "";
    const MODULE_MENU_NAME = "TeleHealth";

    /**
     * @var EventDispatcherInterface The object responsible for sending and subscribing to events through the OpenEMR system
     */
    private $eventDispatcher;

    private $moduleDirectoryName;

    /**
     * The OpenEMR Twig Environment
     * @var Environment
     */
    private $twig;

    /**
     * @var TelehealthGlobalConfig
     */
    private $globalsConfig;

    const COMLINK_VIDEO_TELEHEALTH_API = 'comlink_telehealth_video_uri';

    /**
     * @var TeleHealthPatientPortalController
     */
    private $patientPortalController;

    /**
     * @var TeleHealthVideoRegistrationController
     */
    private $registrationController;

    /**
     * @var TeleHealthTranslationController
     */
    private $translationController;

    /**
     * @var TeleHealthUserAdminController
     */
    private $adminSettingsController;

    /**
     * @var TeleHealthPersonSettingsRepository
     */
    private $personSettingsRepository;

    /**
     * @var TeleHealthProviderRepository
     */
    private $providerRepository;

    /**
     * @var SystemLogger
     */
    private $logger;

    /**
     * @var TeleHealthCalendarController
     */
    private $calendarController;

    public function __construct(EventDispatcher $dispatcher, ?Kernel $kernel = null)
    {
        global $GLOBALS;

        if (empty($kernel)) {
            $kernel = new Kernel();
        }
        $this->eventDispatcher = $dispatcher;
        $twig = new TwigContainer($this->getTemplatePath(), $kernel);
        $twigEnv = $twig->getTwig();
        $this->twig = $twigEnv;

        $this->globalsConfig = new TelehealthGlobalConfig($GLOBALS);
        $this->moduleDirectoryName = basename(dirname(__DIR__));
        $this->translationController = new TeleHealthTranslationController($this->twig);
        $this->logger = new SystemLogger();
    }

    public function getTemplatePath()
    {
        return \dirname(__DIR__) . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR;
    }

    public function getURLPath()
    {
        return self::MODULE_INSTALLATION_PATH . $this->moduleDirectoryName . "/public/";
    }

    /**
     * @return \Twig\Environment
     */
    public function getTwig()
    {
        return $this->twig;
    }

    public function subscribeToEvents()
    {
        $this->addGlobalSettings();
        // we only show the telehealth settings if all of the telehealth configuration has been configured.
        if ($this->globalsConfig->isTelehealthConfigured()) {
            $this->subscribeToTemplateEvents();
            $this->subscribeToProviderEvents();
            // note we need to subscribe at the admin controller as it must precede the registration controller
            // we need our telehealth settings setup for a user before we hit the registration controller
            // as there is an implicit data dependency here.
            // TODO: would it be better to abstract this into a separate controller that controls the flow of events
            // instead of relying on the admin being called before the registration?
            $this->getTeleHealthUserAdminController()->subscribeToEvents($this->eventDispatcher);
            $this->getPatientPortalController()->subscribeToEvents($this->eventDispatcher);
            $this->getRegistrationController()->subscribeToEvents($this->eventDispatcher);
            $this->getCalendarController()->subscribeToEvents($this->eventDispatcher);
        }
    }

    public function getCalendarController()
    {
        if (empty($this->calendarController)) {
            $this->calendarController = new TeleHealthCalendarController(
                $this->globalsConfig,
                $this->getTwig(),
                $this->logger,
                $this->getAssetPath(),
                $this->getCurrentLoggedInUser()
            );
        }
        return $this->calendarController;
    }

    public function getCurrentLoggedInUser()
    {
        return $_SESSION['authUserID'] ?? null;
    }

    public function subscribeToProviderEvents()
    {
        $this->eventDispatcher->addListener(AppointmentSetEvent::EVENT_HANDLE, [$this, 'createSessionRecord'], 10);
    }

    public function createSessionRecord(AppointmentSetEvent $event)
    {
        $pc_catid = $event->givenAppointmentData()['pc_catid'] ?? null;
        $calCatRepo = new CalendarEventCategoryRepository();
        if (empty($calCatRepo->getEventCategoryForId($pc_catid))) {
            // not a telehealth category so we will just skip this.
            return;
        }

        $sessionRepo = new TeleHealthSessionRepository();
        $sessionRepo->getSessionByAppointmentId($event->eid);
    }

    public function subscribeToTemplateEvents()
    {
        $this->eventDispatcher->addListener(TwigEnvironmentEvent::EVENT_CREATED, [$this, 'addTemplateOverrideLoader']);
        $this->eventDispatcher->addListener(RenderEvent::EVENT_BODY_RENDER_POST, [$this, 'renderMainBodyTelehealthScripts']);
    }


    public function addTemplateOverrideLoader(TwigEnvironmentEvent $event)
    {
        $twig = $event->getTwigEnvironment();
        if ($twig === $this->twig) {
            // we do nothing if its our own twig environment instantiated that we already setup
            return;
        }
        // we make sure we can override our file system directory here.
        $loader = $twig->getLoader();
        if ($loader instanceof FilesystemLoader) {
            $loader->prependPath($this->getTemplatePath());
        }
    }

    private function getPublicPath()
    {
        return self::MODULE_INSTALLATION_PATH . ($this->moduleDirectoryName ?? '') . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR;
    }

    private function getAssetPath()
    {
        return $this->getPublicPath() . 'assets' . DIRECTORY_SEPARATOR;
    }

    public function renderMainBodyTelehealthScripts()
    {
        ?>
        <script src="<?php echo $this->getAssetPath();?>../index.php?action=get_translations"></script>
        <link rel="stylesheet" href="<?php echo $this->getAssetPath();?>css/telehealth.css">
        <script src="<?php echo $this->getAssetPath();?>js/telehealth.js"></script>
        <script src="<?php echo $this->getAssetPath();?>js/telehealth-provider.js"></script>
        <?php
    }

    public function addGlobalSettings()
    {
        $this->eventDispatcher->addListener(GlobalsInitializedEvent::EVENT_HANDLE, [$this, 'addGlobalTeleHealthSettings']);
    }

    public function addGlobalTeleHealthSettings(GlobalsInitializedEvent $event)
    {
        global $GLOBALS;

        $service = $event->getGlobalsService();
        $section = xlt("TeleHealth");
        $service->createSection($section, 'Portal');

        $settings = $this->globalsConfig->getGlobalSettingSectionConfiguration();

        foreach ($settings as $key => $config) {
            $value = $GLOBALS[$key] ?? $config['default'];
            $service->appendToSection(
                $section,
                $key,
                new GlobalSetting(
                    xlt($config['title']),
                    $config['type'],
                    $value,
                    xlt($config['description']),
                    true
                )
            );
        }
    }

    public function getTeleconferenceRoomController($isPatient): TeleconferenceRoomController
    {
        return new TeleconferenceRoomController(
            $this->getTwig(),
            new SystemLogger(),
            $this->getRegistrationController(),
            $this->getAssetPath(),
            $isPatient
        );
    }

    public function getRegistrationController(): TeleHealthVideoRegistrationController
    {
        $globalsConfig = $this->globalsConfig;
        if (empty($this->registrationController)) {
            $this->registrationController = new TeleHealthVideoRegistrationController(
                $this->getProviderRepository(),
                $globalsConfig->getRegistrationAPIURI(),
                $globalsConfig->getRegistrationAPIUserId(),
                $globalsConfig->getRegistrationAPIPassword(),
                $globalsConfig->getRegistrationAPICmsId(),
                $globalsConfig->getInstitutionId(),
                $globalsConfig->getInstitutionName()
            );
        }
        return $this->registrationController;
    }
    public function getPatientPortalController(): TeleHealthPatientPortalController
    {
        if (empty($this->patientPortalController)) {
            $this->patientPortalController = new TeleHealthPatientPortalController($this->twig, $this->getAssetPath());
        }
        return $this->patientPortalController;
    }


    private function getTeleHealthUserAdminController()
    {
        if (empty($this->adminSettingsController)) {
            $this->adminSettingsController = new TeleHealthUserAdminController(
                $this->globalsConfig,
                $this->getTwig(),
                $this->getPersonSettingsRepository()
            );
        }
        return $this->adminSettingsController;
    }

    private function getPersonSettingsRepository(): TeleHealthPersonSettingsRepository
    {
        if (empty($this->personSettingsRepository)) {
            $this->personSettingsRepository = new TeleHealthPersonSettingsRepository($this->logger);
        }
        return $this->personSettingsRepository;
    }

    private function getProviderRepository(): TeleHealthProviderRepository
    {
        if (empty($this->providerRepository)) {
            $this->providerRepository = new TeleHealthProviderRepository($this->logger, $this->globalsConfig);
        }
        return $this->providerRepository;
    }
}