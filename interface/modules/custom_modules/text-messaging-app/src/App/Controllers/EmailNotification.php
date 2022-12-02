<?php

/**
 *  package OpenEMR
 *  link    https://www.open-emr.org
 *  author  Sherwin Gaddis <sherwingaddis@gmail.com>
 *  Copyright (c) 2022.
 *  All Rights Reserved
 */

namespace Juggernaut\App\Controllers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use OpenEMR\Common\Crypto\CryptoGen;

class EmailNotification
{
    protected $smtpUser;
    protected $smtpPwd;
    protected $patientid;
    protected $host;
    protected $secure;
    protected $port;
    protected $apptTime;
    protected $apptDate;

    public function __construct(array $appointment)
    {
        $cryptoGen = new CryptoGen();
        $this->smtpPwd = $cryptoGen->decryptStandard($GLOBALS['SMTP_PASS']);
        $this->smtpUser = $GLOBALS['SMTP_USER'];
        $this->secure = $GLOBALS['SMTP_SECURE'];
        $this->host = $GLOBALS['SMTP_HOST'];
        $this->port = $GLOBALS['SMTP_PORT'];
        $this->patientid = $appointment['form_pid'];
        $this->apptTime = $appointment['form_hour'] . ":" . $appointment['form_minute'];
        $this->apptDate = $appointment['selected_date'];
        if (($appointment['form_title'] == 'TeleHealth - New Patient') || ($appointment['form_title'] == 'New Patient')) {
            self::sendStaffEmail();
        }
    }

    private function sendStaffEmail()
    {
        $mail = new PHPMailer();
        try {
            $mail->SMTPDebug = true;
            $mail->isSMTP();
            $mail->IsHTML(true);
            $mail->Host = $this->host;
            $mail->SMTPAuth = true;
            $mail->Username = $this->smtpUser;
            $mail->Password = $this->smtpPwd;
            $mail->SMTPSecure = $this->secure;
            $mail->Port = $this->port;

            $mail->setFrom($GLOBALS['practice_return_email_path']);

            $mail->addAddress($GLOBALS['patient_reminder_sender_email'], 'Staff');

            $mail->Subject = 'New Patient Appointment ';
            $mail->Body = self::emailBody();
            $mail->send();

        }
        catch (Exception $e)
        {
            error_log('There has been a mail error sending to' .  $mail->ErrorInfo);
        }
    }

    private function emailBody()
    {
        return "Patient " . $this->patientid . " has an appointment on this date "
            . $this->apptDate . " and time " . $this->apptTime . ".";
    }

}
