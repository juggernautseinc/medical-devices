<?php

/**
 * package   OpenEMR
 *  link      http://www.open-emr.org
 *  author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  copyright Copyright (c )2021. Sherwin Gaddis <sherwingaddis@gmail.com>
 *  license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 *
 */

namespace OpenEMR\Modules\Documo;

use OpenEMR\Common\Crypto\CryptoGen;

class ApiDispatcher
{
    private $apiKey;
    public $accountId;
    public $areacode;
    public $attachment;
    public $callerID;
    private $cryptoGen;
    public $direction;
    public $filePost;
    public $faxNumber;
    public $faxstatus;
    public $limit;
    public $name;
    public $offset;
    public $senderName;
    public $schedule;
    public $subject;
    public $useraccountid;
    private $userKey;
    private $fromDate;
    private $toDate;

    public function __construct()
    {
        $this->cryptoGen = new CryptoGen();
        $this->apiKey = self::findApiKey();
        $this->userKey = $this->cryptoGen->decryptStandard($GLOBALS['oedocumofax_enable']);
    }

    private function headerArray(): array
    {
        $value = 'Authorization: Basic ' . $this->apiKey;
        return array(
            $value,
            'Content-Type: application/x-www-form-urlencoded'
        );
    }

    public function createUser($postData)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.documo.com/v1/users',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => self::headerArray(),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

            return $response;
    }

    public function createAccount($postfields)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.documo.com/v1/accounts',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postfields,
            CURLOPT_HTTPHEADER => self::headerArray(),
        ));

        $response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        curl_close($curl);
        if ($status === 200) {

            return $response;
        } else {
            return $status;
        }
    }

    public function findAvailableFaxNumber($areacode, $state, $zip)
    {
        $curl = curl_init();
        $post = 'npa=' . $areacode . '&state=' . $state . '&zipcode=' . $zip;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.documo.com/v1/numbers/provision/search?'.$post,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => self::headerArray(),
        ));

        $response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        curl_close($curl);
        if ($status !== 200) {

            if ($status === 400) {
                $response = xlt("Invalid Input");
            } elseif ($status === 401) {
                $response = xlt("Unauthorized - Key may be expired");
            } elseif ($status === 404) {
                $response = xlt("Not Found");
            }
        }
        return $response;
    }

    private function findApiKey()
    {
        $url = "https://api.affordablecustomehr.com/api.php?name=pen";
        $client = curl_init($url);
        curl_setopt($client,CURLOPT_RETURNTRANSFER,true);
        $response = curl_exec($client);

        $result = json_decode($response);
        return $result->data;
    }

    public function registerSelectedNumbers($faxNumbersRequest)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.documo.com/v1/numbers/provision',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $faxNumbersRequest,
            CURLOPT_HTTPHEADER => self::headerArray(),
        ));

        $response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        curl_close($curl);
        if ($status === 200) {
            return $response;
        } else {
            return $status;
        }
    }

    public function getNetworkStatus()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.documo.com/ping',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        if ($response === 'OK') {
            $response = true;
        } else {
            $response = false;
        }
        return $response;
    }

    public function sendFax()
    {
        $curl = curl_init();
        $postData = array("faxNumber"=>$this->faxNumber,"'attachments'"=>$this->filePost,"coverPage"=>'false',"coverPageId"=>'','tags'=>'','recipientName'=>$this->name,'senderName'=>$this->senderName,'subject'=>$this->subject,'callerId'=>$this->callerID,'notes'=>'','scheduledDate'=>$this->schedule);
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.documo.com/v1/faxes',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => 0,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . $this->userKey,
                'Content-Type: multipart/form-data'
            )
        ));

        return curl_exec($curl);
    }

    public function setWebHook($hookString) : string
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.documo.com/webhooks',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $hookString,
            CURLOPT_HTTPHEADER => self::headerArray(),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }

    public function getFaxHistory()
    {
        $curl = curl_init();
        $getData = 'accountId='. $this->useraccountid .
            '&offset=' . $this->offset .
            '&limit=' . $this->limit .
            '&direction=' . $this->direction .
            '&status=' . $this->faxstatus .
            '&fromDate=' . $this->fromDate .
            '&toDate=' . $this->toDate;

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.documo.com/v1/fax/history?' . $getData,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . $this->userKey
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    public function downLoadFax()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.documo.com/v1/fax/download',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . $this->userKey
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
    }

    public function faxSummaryReport()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.documo.com/v1/reports/summary',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . $this->userKey
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;

    }

    public function registration($clinic)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.affordablecustomehr.com/register.php',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array("name"=>$clinic['name'],"phone"=>$clinic['phone'],"email"=>$clinic['email']),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    /**
     * @param mixed $fromDate
     */
    public function setFromDate($fromDate): void
    {
        $this->fromDate = $fromDate;
    }

    /**
     * @param mixed $toDate
     */
    public function setToDate($toDate): void
    {
        $this->toDate = $toDate;
    }
}
