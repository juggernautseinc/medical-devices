<?php

/**
 * Communicates with the Comlink User provisioning api.
 *
 * @package openemr
 * @link      http://www.open-emr.org
 * @author    Stephen Nielson <stephen@nielson.org>
 * @copyright Copyright (c) 2021 Comlink
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

namespace Comlink\OpenEMR\Modules\TeleHealthModule;

use Comlink\OpenEMR\Modules\TeleHealthModule\Repository\TeleHealthPersonSettingsRepository;
use Comlink\OpenEMR\Modules\TeleHealthModule\Repository\TeleHealthProviderRepository;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use OpenEMR\Common\Database\SqlQueryException;
use OpenEMR\Common\Logging\SystemLogger;
use OpenEMR\Common\Uuid\UuidRegistry;
use OpenEMR\Events\Patient\PatientCreatedEvent;
use OpenEMR\Events\Patient\PatientUpdatedEvent;
use OpenEMR\Events\User\UserCreatedEvent;
use OpenEMR\Events\User\UserUpdatedEvent;
use OpenEMR\Services\PatientService;
use OpenEMR\Services\UserService;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Exception;

class TeleHealthVideoRegistrationController
{
    /**
     * API url endpoint to send registration requests to.
     * @var string
     */
    private $apiURL;

    /*
     * UserID for api authentication needed for comlink video service
     * @var string
     */
    private $apiId;

    /*
     * Password for api authentication needed for comlink video service
     * @var string
     */
    private $apiPassword;

    /*
     * CMSID for api authentication needed for comlink video service
     * @var string
     */
    private $apiCMSID;

    /**
     * Repository for saving / retrieving telehealth user settings.
     * @var TeleHealthUserRepository
     */
    private $userRepository;

    /**
     * Client
     */
    private $httpClient;

    /**
     * Unique installation id of the OpenEMR Institution
     * @var string
     */
    private $institutionId;

    /**
     * Name of the OpenEMR institution
     * @var string
     */
    private $institutionName;

    /**
     * @var SystemLogger
     */
    private $logger;

    /**
     * @var TeleHealthProviderRepository
     */
    private $providerRepository;

    public function __construct(TeleHealthProviderRepository $repo, $apiURL, $apiId, $apiPassword, $apiCMSID, $institutionId = null, $institutionName = null)
    {
        $this->providerRepository = $repo;
        $this->apiURL = $apiURL;
        $this->apiId = $apiId;
        $this->apiPassword = $apiPassword;
        $this->apiCMSID = $apiCMSID;
        $this->institutionId = $institutionId;
        $this->institutionName = $institutionName;
        $this->userRepository = new TeleHealthUserRepository();
        $this->httpClient = new Client();
        $this->logger = new SystemLogger();
    }

    public function subscribeToEvents(EventDispatcher $eventDispatcher)
    {
        $eventDispatcher->addListener(PatientCreatedEvent::EVENT_HANDLE, [$this, 'onPatientCreatedEvent']);
        $eventDispatcher->addListener(PatientUpdatedEvent::EVENT_HANDLE, [$this, 'onPatientUpdatedEvent']);
        $eventDispatcher->addListener(UserCreatedEvent::EVENT_HANDLE, [$this, 'onUserCreatedEvent']);
        $eventDispatcher->addListener(UserUpdatedEvent::EVENT_HANDLE, [$this, 'onUserUpdatedEvent']);
    }

    public function onPatientCreatedEvent(PatientCreatedEvent $event)
    {
        $patient = $event->getPatientData();
        $this->logger->debug(
            self::class . "->onPatientCreatedEvent received for patient ",
            ['uuid' => $patient['uuid'] ?? null, 'patient' => $patient]
        );
        try {
            $patient['uuid'] = UuidRegistry::uuidToString($patient['uuid']); // convert uuid to a string value
            $this->createPatientRegistration($patient);
        } catch (Exception $exception) {
            $this->logger->errorLogCaller("Failed to create patient registration. Error: "
                . $exception->getMessage(), ['trace' => $exception->getTraceAsString(), 'patient' => $patient['uuid']]);
        }
    }

    public function onPatientUpdatedEvent(PatientUpdatedEvent $event)
    {
        try {
            $patient = $event->getNewPatientData();
            $oldPatient = $event->getDataBeforeUpdate();
            // we need the patient uuid so we are going to grab it from the pid
            $patientService = new PatientService();

            $patient['uuid'] = UuidRegistry::uuidToString($oldPatient['uuid']); // convert uuid to a string value
            $this->logger->debug(
                self::class . "->onPatientUpdatedEvent received for patient ",
                ['uuid' => $patient['uuid'] ?? null, 'patient' => $patient]
            );
            // let's grab the patient data and create the patient if its not registered
            $apiUser = $this->userRepository->getUser($patient['uuid']);
            if (empty($apiUser)) {
                $this->createPatientRegistration($patient);
            }
        } catch (Exception $exception) {
            $this->logger->errorLogCaller("Failed to create patient registration. Error: "
                . $exception->getMessage(), ['trace' => $exception->getTraceAsString(), 'patient' => $patient['uuid']]);
        }
    }

    public function onUserCreatedEvent(UserCreatedEvent $event)
    {
        try {
            $user = $event->getUserData();
            $userService = new UserService();
            // our event doesn't have the uuid which is what we need
            $userWithUuid = $userService->getUserByUsername($event->getUsername());
            if (empty($userWithUuid)) {
                throw new \InvalidArgumentException("Could not find user with username " . $event->getUsername());
            }

            // we need to find out if we
            $providerRepo = $this->providerRepository;
            // find out if the provider is enabled, if so we create the registration
            $this->logger->debug(
                self::class . "->onUserCreatedEvent received for user ",
                ['username' => $event->getUsername(), 'userWithUuid' => $userWithUuid, 'uuid' => $userWithUuid['uuid'] ?? null]
            );
            if ($providerRepo->isEnabledProvider($userWithUuid['id'])) {
                $this->createUserRegistration($userWithUuid);
            } else {
                $this->logger->debug(
                    self::class . "->onUserCreatedEvent skipping registration as user is not enrolled",
                    ['username' => $event->getUsername(), 'userWithUuid' => $userWithUuid, 'uuid' => $userWithUuid['uuid'] ?? null]
                );
            }
        } catch (Exception $exception) {
            $this->logger->errorLogCaller("Failed to create user registration. Error: "
                . $exception->getMessage(), ['trace' => $exception->getTraceAsString(), 'user' => $user['uuid']]);
        }
    }

    public function onUserUpdatedEvent(UserUpdatedEvent $event)
    {
        try {
            $user = $event->getNewUserData();
            $userService = new UserService();
            // our event doesn't have the uuid which is what we need
            $userWithUuid = $userService->getUser($event->getUserId());
            if (empty($userWithUuid)) {
                throw new \InvalidArgumentException("Could not find user with username " . $event->getUsername());
            }
            $this->logger->debug(self::class . "->onUserUpdatedEvent received for user ", ['uuid' => $userWithUuid['uuid'] ?? null]);

            $providerRepo = $this->providerRepository;

            // create the registration
            $apiUser = $this->userRepository->getUser($userWithUuid['uuid']);

            if ($providerRepo->isEnabledProvider($userWithUuid['id'])) {
                // create our registration if there is one
                if (empty($apiUser)) {
                    $this->logger->debug(self::class . "->onUserUpdatedEvent registering user with comlink", ['uuid' => $userWithUuid['uuid'] ?? null]);
                    $this->createUserRegistration($userWithUuid);
                } else {
                    if (!$apiUser->getIsActive()) {
                        $this->logger->debug(
                            self::class . "->onUserUpdatedEvent user auth record is suspended, activating",
                            ['uuid' => $userWithUuid['uuid'] ?? null]
                        );
                        // we need to activate the user
                        $this->resumeUser($apiUser->getUsername(), $apiUser->getAuthToken());
                    } else {
                        $this->logger->debug(
                            self::class . "->onUserUpdatedEvent user auth record is already active",
                            ['uuid' => $userWithUuid['uuid'] ?? null]
                        );
                        // TODO: if we ever want to update the password registration here we can do that here
                        // since we don't change the username and its a randomly generated password, there's no need to change
                        // the password.
                    }
                }
            } else {
                // we need to find out if a registration exists... if it does we need to deactivate it
                if (empty($apiUser)) {
                    $this->logger->debug(
                        self::class . "->onUserUpdatedEvent telehealth disabled and no auth record exists",
                        ['uuid' => $userWithUuid['uuid'] ?? null]
                    );
                    // we do nothing here if the provider is not enabled and there's no auth we just ignore this
                } else if ($apiUser->getIsActive()) {
                    $this->logger->debug(
                        self::class . "->onUserUpdatedEvent telehealth is disabled but registration is active. suspending user",
                        ['uuid' => $userWithUuid['uuid'] ?? null]
                    );
                    $this->suspendUser($apiUser->getUsername(), $apiUser->getAuthToken());
                }
            }
        } catch (Exception $exception) {
            $this->logger->errorLogCaller("Failed to create user registration. Error: "
                . $exception->getMessage(), ['trace' => $exception->getTraceAsString(), 'user' => $user]);
        }
    }

    public function createPatientRegistration($patient)
    {
        $registrationRequest = new UserVideoRegistrationRequest();
        $registrationRequest->setDbRecordId($patient['id']);
        $registrationRequest->setIsPatient(true);
        $registrationRequest->setUsername($patient['uuid']);
        $registrationRequest->setPassword($this->userRepository->createUniquePassword());
        $registrationRequest->setInstituationId($this->institutionId);
        $registrationRequest->setInstitutionName($this->institutionName);
        $registrationRequest->setFirstName($patient['fname'] ?? null);
        $registrationRequest->setLastName($patient['lname'] ?? null);
        $this->logger->debug("createPatientRegistration called");
        $userId = $this->addNewUser($registrationRequest);
        return !empty($userId);
    }

    public function createUserRegistration($user)
    {
        $registrationRequest = new UserVideoRegistrationRequest();
        $registrationRequest->setDbRecordId($user['id']);
        $registrationRequest->setIsPatient(false);
        $registrationRequest->setUsername($user['uuid']);
        $registrationRequest->setPassword($this->userRepository->createUniquePassword());
        $registrationRequest->setInstituationId($this->institutionId);
        $registrationRequest->setInstitutionName($this->institutionName);
        $registrationRequest->setFirstName($user['fname'] ?? null);
        $registrationRequest->setLastName($user['lname'] ?? null);
        $this->logger->debug("createUserRegistration called");
        $userId = $this->addNewUser($registrationRequest);
        return !empty($userId);
    }

    /**
     * @return TeleHealthUserRepository
     */
    public function getUserRepository(): TeleHealthUserRepository
    {
        return $this->userRepository;
    }

    /**
     * @return Client
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }


    /**
     * Allows the http client used for api requests to be set for testing or extension purposes
     * @param Client $client
     */
    public function setHttpClient(Client $client)
    {
        $this->httpClient = $client;
    }

    /**
     * Allows the user repository to be set for testing or extension purposes
     * @param TeleHealthUserRepository $userRepository
     */
    public function setTelehealthUserRepository(TeleHealthUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Returns if a registration should be created for the given provider id.  This does not answer whether a registration
     * exists, but whether the user passes the criteria for creating a registration record regardless of whether it exists or not.
     * @param $providerId
     * @return bool
     */
    public function shouldCreateRegistrationForProvider($providerId)
    {
        return $this->providerRepository->isEnabledProvider($providerId);
    }

    /**
     * Provisions a new user with the Comlink video api system
     * @param UserVideoRegistrationRequest $request
     * @return false|int returns false if the user fails to add, otherwise returns the integer id of the provisioned user
     */
    public function addNewUser(UserVideoRegistrationRequest $request)
    {
        if (!$request->isValid()) {
            throw new \InvalidArgumentException("request is missing username, password, or institutionId");
        }

        $securePassword = $request->getPassword();
        $request->setPassword($this->userRepository->decryptPassword($securePassword));
        $httpDataRequest = $request->toArray();

        $response = $this->sendAPIRequest($this->getEndpointUrl("userprovision"), $httpDataRequest);

        if ($response['status'] != 200) {
            (new SystemLogger())->errorLogCaller("Failed to provision user", ['username' => $request->getUsername()
                , 'response' => $response]);
            return false;
        } else {
            try {
                $userSaveRecord = new TeleHealthUser();
                $userSaveRecord->setIsPatient($request->isPatient());
                $userSaveRecord->setDbRecordId($request->getDbRecordId());
                $userSaveRecord->setUsername($request->getUsername());
                $userSaveRecord->setAuthToken($securePassword);
                $userSaveRecord->setDateRegistered(new \DateTime());
                $userSaveRecord->setIsActive(true);
                $userId = $this->userRepository->saveUser($userSaveRecord);
                $this->logger->debug("Registered user on comlink api ", ['username' => $request->getUsername(), 'id' => $userId]);
            } catch (SqlQueryException $exception) {
                $this->logger->errorLogCaller("User registered on comlink api but did not save to database", ['record' => $userSaveRecord]);
                throw $exception;
            }
            return $userId;
        }
    }

    private function getEndpointUrl($endpoint)
    {
        return $this->apiURL . $endpoint;
    }

    /**
     * Updates an existing provisioned user with the Comlink video api system.  Everything but username can be changed
     * @param UserVideoRegistrationRequest $request
     * @return false|int returns false if the user fails to update, otherwise returns the integer id of the updated user
     */
    public function updateUser(UserVideoRegistrationRequest $request)
    {
        if (!$request->isValid()) {
            throw new \InvalidArgumentException("request is missing username, password, or institutionId");
        }

        // first make sure we can do the api request
        $dbUserRecord = $this->userRepository->getUser($request->getUsername());
        if (empty($dbUserRecord)) {
            throw new \BadMethodCallException("user does not exist for username " . $request->getUsername());
        }

        $securePassword = $request->getPassword();
        $request->setPassword($this->userRepository->decryptPassword($securePassword));
        $httpDataRequest = $request->toArray();

        $response = $this->sendAPIRequest($this->getEndpointUrl("userupdate"), $httpDataRequest);

        if ($response['status'] != 200) {
            $this->logger->errorLogCaller("Failed to update provisioned user", ['username' => $request->getUsername()
                , 'response' => $response]);
            return false;
        } else {
            $dbUserRecord->setAuthToken($securePassword);
            $dbUserRecord->setIsActive(true);
            $userId = $this->userRepository->saveUser($dbUserRecord);
            $this->logger->debug("Updated user on comlink api ", ['username' => $request->getUsername(), 'id' => $userId]);
            return $userId;
        }
    }

    public function suspendUser(string $username, string $password): bool
    {
        // first make sure we can do the api request
        $dbUserRecord = $this->userRepository->getUser($username);
        if (empty($dbUserRecord)) {
            throw new \BadMethodCallException("user does not exist for username " . $username);
        }

        $decryptedPassword = $this->userRepository->decryptPassword($password);
        $httpDataRequest = ['userName' => $username, 'passwordString' => $decryptedPassword];
        $decryptedPassword = null;

        $response = $this->sendAPIRequest($this->getEndpointUrl("usersuspend"), $httpDataRequest);
        unset($httpDataRequest['passwordString']);

        if ($response['status'] != 200) {
            $this->logger->errorLogCaller("Failed to suspend user", ['username' => $username, 'response' => $response]);
            return false;
        }
        $dbUserRecord->setIsActive(false);
        $this->userRepository->saveUser($dbUserRecord);
        return true;
    }

    public function resumeUser(string $username, string $password): bool
    {
        // first make sure we can do the api request
        $dbUserRecord = $this->userRepository->getUser($username);
        if (empty($dbUserRecord)) {
            throw new \BadMethodCallException("user does not exist for username " . $username);
        }

        $passwordString = $this->userRepository->decryptPassword($password);
        $httpDataRequest = ['userName' => $username, 'passwordString' => $passwordString];
        $passwordString = null; // clear out passwords in memory

        $response = $this->sendAPIRequest($this->getEndpointUrl("userresume"), $httpDataRequest);
        $httpDataRequest = null; // clear out passwords in memory
        if ($response['status'] != 200) {
            $this->logger->errorLogCaller("Failed to resume user", ['username' => $username, 'response' => $response]);
            return false;
        }
        $dbUserRecord->setIsActive(true);
        $this->userRepository->saveUser($dbUserRecord);
        return true;
    }

    public function deactivateUser(string $username, string $password)
    {
        // first make sure we can do the api request
        $dbUserRecord = $this->userRepository->getUser($username);
        if (empty($dbUserRecord)) {
            throw new \BadMethodCallException("user does not exist for username " . $username);
        }

        $httpDataRequest = ['userName' => $username, 'passwordString' => $password];

        $response = $this->sendAPIRequest($this->getEndpointUrl("userresume"), $httpDataRequest);

        if ($response['status'] != 200) {
            $this->logger->errorLogCaller("Failed to deactivate user", ['username' => $username, 'response' => $response]);
            return false;
        }
        $dbUserRecord->setIsActive(false);
        $this->userRepository->saveUser($dbUserRecord);
        return true;
    }

    private function sendAPIRequest($endpointUrl, array $body)
    {
        if (empty($this->httpClient)) {
            throw new \BadMethodCallException("httpClient must be setup in order to send request");
        }

        // because this could be an already existing event we've tried saving before we decode the json, even though
        // on the first event notification we may be doubling the work
        $client = $this->getHttpClient();
        $internalErrorResponse = null;
        $bodyResponse = null;
        $statusCode = 500;

        try {
            $httpRequestOptions = [
                "headers" => [
                    "SvcmgrTk1" => $this->apiId
                    ,"SvcmgrTk2" => $this->apiPassword
                    ,"SvcmgrTk3" => $this->apiCMSID
                ],
                "body" => json_encode($body)
            ];
            $response = $client->post($endpointUrl, $httpRequestOptions);
            $statusCode = $response->getStatusCode();
            $response->getBody()->rewind();
            $bodyResponse = $response->getBody()->getContents();
        } catch (GuzzleException $exception) {
            $this->logger->errorLogCaller(
                "Failed to send registration request Exception: " . $exception->getMessage(),
                ['trace' => $exception->getTraceAsString(), 'endUrl' => $endpointUrl]
            );
            $internalErrorResponse = $exception->getMessage();
        }

        return ['status' => $statusCode, 'bodyResponse' => $bodyResponse, 'internalError' => $internalErrorResponse];
    }
}
