<?php

/**
 *  package OpenEMR
 *  link    https://www.open-emr.org
 *  author  Sherwin Gaddis <sherwingaddis@gmail.com>
 *  Copyright (c) 2022.
 *  license https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

namespace Juggernaut\App\Controllers;

use OpenEMR\Events\Appointments\AppointmentSetEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AppointmentsSubscriber implements EventSubscriberInterface
{

    /**
     * @inheritDoc
     */

    public static function getSubscribedEvents(): array
    {
        return [
          AppointmentSetEvent::EVENT_HANDLE => 'appointmentChanged'
        ];
    }

    /**
     * @param AppointmentSetEvent $event
     * @return EmailNotification
     */
    public function appointmentChanged(AppointmentSetEvent $event)
    {
        $appointmentdata = $event->givenAppointmentData();
        file_put_contents("/var/www/html/errors/apptStatus1.txt", print_r($appointmentdata, true), FILE_APPEND);
        return new EmailNotification($appointmentdata);
    }
}
