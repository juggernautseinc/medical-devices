/**
 * Handles the checking of a provider's telehealth registration when they login.
 *
 * @package openemr
 * @link      http://www.open-emr.org
 * @author    Stephen Nielson <stephen@nielson.org>
 * @copyright Copyright (c) 2021 Comlink
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */
(function(window, comlink) {
    let telehealth = comlink.telehealth || {};

    if (telehealth && telehealth.launchRegistrationChecker)
    {
        window.addEventListener('load', function() {
            telehealth.launchRegistrationChecker(false);
        });
    }
})(window, window.comlink || {});