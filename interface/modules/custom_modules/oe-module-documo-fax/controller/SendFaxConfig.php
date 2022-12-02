<?php
/*
 * package   OpenEMR
 *  link      http://www.open-emr.org
 *  author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  copyright Copyright (c )2021. Sherwin Gaddis <sherwingaddis@gmail.com>
 *  license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 *
 */

namespace OpenEMR\Modules\Documo;

use Exception;
use http\Exception\UnexpectedValueException;

class SendFaxConfig
{
    private $uuid;
    const STATUS_WAITING = "waiting";
    const STATUS_PARAMETER_ERROR = 'parameter-error';
    const STATUS_UPLOAD_ERROR = 'fax upload error';
    const STATUS_IN_PROGRESS = 'in progress';
    const STATUS_SUCCESS = 'success';
    const SITE_ID = '/sites';
    const TABLE_NAME = 'documo_fax_log';
    private $userAccount;
    private $userUuid;
    private $userEmail;


    public function __construct()
    {
        $getuuid = new Database();
        $this->uuid = $getuuid->getAccountId();
    }

    /**
     * @throws Exception
     */
    public static function faxDir(): string
    {
        $documo_path = dirname(__FILE__, 6) . self::SITE_ID . DIRECTORY_SEPARATOR . $_SESSION['site_id'] . "/documents/documo";
        $inbound_path = dirname(__FILE__, 6) . self::SITE_ID . DIRECTORY_SEPARATOR . $_SESSION['site_id'] . "/documents/documo/inbound";
        $outbound_path = dirname(__FILE__, 6) . self::SITE_ID . DIRECTORY_SEPARATOR . $_SESSION['site_id'] . "/documents/documo/outbound";

        if (!is_dir($documo_path)) {
            if (!mkdir($documo_path)) {
                $mkdirErrorArray = error_get_last();
                throw new UnexpectedValueException('cannot create director ' . $mkdirErrorArray['message']);
            }
            mkdir($outbound_path);
            mkdir($inbound_path);
            $response = "Created";
        } else {
            $response = "Found";
        }
        return $response;
    }

    public function createWebHookURI()
    {
        $userInfo = new Database();
        $userPassword = $userInfo->getPassword();

        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $http = "https://";
        } else {
            echo xlt('You must have an SSL certificate to receive inbound faxes');
            die;
        }

        $hookUrl = $http . $_SERVER['HTTP_HOST'] . $GLOBALS['webroot'] .
            '/interface/modules/custom_modules/oe-module-documo-fax/fax/inbound/' . $_SESSION['site_id'] . '/';

        //remove any returns and spaces from the string
        $hookUrl = str_replace(PHP_EOL, '', $hookUrl);
        $hookUrl = str_replace(' ', '', $hookUrl);

        $hookString = 'name=oe-fax-module
        &url=' . $hookUrl .
        '&events=%7B%20%22fax.inbound%22%3A%20true%2C%20%22fax.outbound%22%3A%20false%2C%20%22fax.outbound.extended%22%3A%20false%2C%20%22user.create%22%3A%20false%2C%20%22user.delete%22%3A%20false%2C%20%22number.add%22%3A%20false%2C%20%22number.release%22%3A%20false%2C%20%22document.complete%22%3A%20false%2C%20%22document.failed%22%3A%20false%20%7D
        &auth=' . $this->userEmail . ':' . $userPassword . '&accountId=' . $this->userAccount . '&numberId=' . $this->userUuid . '&attachmentEnabled=true&notificationEmails=' . $this->userEmail . "'";
        $hookString = str_replace(PHP_EOL, '', $hookString); //remove returns
        $hookString = str_replace(' ', '', $hookString); //remove white spaces
        $sendWebHook = new ApiDispatcher();
        $response = $sendWebHook->setWebHook($hookString);
        $response_e = json_decode($response, true);
        if ($response_e['error']) {
            return $response_e; //if there is an error return it else return nothing
        }
    }

    /**
     * @param mixed $userAccount
     */
    public function setUserAccount($userAccount): void
    {
        $this->userAccount = $userAccount;
    }

    /**
     * @param mixed $userUuid
     */
    public function setUserUuid($userUuid): void
    {
        $this->userUuid = $userUuid;
    }

    /**
     * @param mixed $userEmail
     */
    public function setUserEmail($userEmail): void
    {
        $this->userEmail = $userEmail;
    }
}
