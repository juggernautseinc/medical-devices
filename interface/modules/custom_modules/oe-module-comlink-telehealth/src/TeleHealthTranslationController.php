<?php

/**
 * Contains all of the translations used by the client side portion of the TeleHealth.
 *
 * @package openemr
 * @link      http://www.open-emr.org
 * @author    Stephen Nielson <stephen@nielson.org>
 * @copyright Copyright (c) 2021 Comlink
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

namespace Comlink\OpenEMR\Modules\TeleHealthModule;

use Twig\Environment;

class TeleHealthTranslationController
{
    /**
     * @var Environment The twig environment
     */
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function renderClientSideTranslations()
    {
        $translations = [
                'CALL_CONNECT_FAILED' => xlt("Failed to connect the call."),
                'SESSION_LAUNCH_FAILED' => xlt("There was an error in launching your telehealth session.  Please try again or contact support"),
                'DUPLICATE_SESSION' => xlt("You are already in a conference session.  Please hangup the current call to start a new telehealth session"),
                'HOST_LEFT' => xlt("Host left the call"),
                'PROVIDER_SESSION_START_PROMPT' => xlt("Would you like to start a telehealth session with this patient (This will create an encounter if one does not exist)?"),
                'PROVIDER_SESSION_CLONE_START_PROMPT' => xlt("This appointment belongs to a different provider. Would you still like to start a telehealth session (this will copy the appointment to your calendar and create an encounter if needed)?"),
                'PROVIDER_SESSION_TELEHEALTH_UNENROLLED' => xlt("This is a Telehealth session appointment.  If you would like to provide telehealth sessions to your clients contact your administrator to enroll today."),
                'CONFIRM_SESSION_CLOSE' => xlt("Are you sure you want to close this session?"),
                "TELEHEALTH_MODAL_TITLE" => xlt("TeleHealth Session"),
                "TELEHEALTH_MODAL_CONFIRM_TITLE" => xlt("Confirm Session Close"),
                "UPDATE_APPOINTMENT_STATUS" => xlt("Update appointment status"),
                "STATUS_NO_SHOW" => xlt("No Show"),
                "STATUS_CANCELED" => xlt("Canceled"),
                "STATUS_CHECKED_OUT" => xlt("Checked Out"),
                "CONFIRM" => xlt("Confirm"),
                "CALENDAR_EVENT_DISABLED" => xlt("TeleHealth Sessions can only be launched within two hours of the current appointment time."),
                "CALENDAR_EVENT_COMPLETE" => xlt("This TeleHealth appointment has been completed."),
                "STATUS_SKIP_UPDATE" => xlt("Skip Update"),
                "STATUS_NO_UPDATE" => xlt("No Change"),
                "STATUS_OTHER" => xlt("Other"),
                'APPOINTMENT_STATUS_UPDATE_FAILED' => xlt('There was an error in saving the telehealth appointment status.  Please contact support or update the appointment manually in the calendar')
        ];
        echo $this->twig->render("comlink/telehealth-translations.js.twig", ['translations' => $translations]);
    }
}
