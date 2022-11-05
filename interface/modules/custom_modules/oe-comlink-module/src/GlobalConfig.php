<?php

/**
 * Bootstrap custom module Comlink.  This file is an example custom module that can be used
 * to create modules that can be utilized inside the OpenEMR system.  It is NOT intended for
 * production and is intended to serve as the barebone requirements you need to get started
 * writing modules that can be installed and used in OpenEMR.
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 *
 * @author    Stephen Nielson <stephen@nielson.org>
 * @copyright Copyright (c) 2021 Stephen Nielson <stephen@nielson.org>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

namespace Comlink\OpenEMR\Module;

use OpenEMR\Common\Crypto\CryptoGen;
use OpenEMR\Services\Globals\GlobalSetting;

class GlobalConfig
{
    const CONFIG_OPTION_TEXT = 'oe_comlink_config_option_text';
    const CONFIG_OPTION_ENCRYPTED = 'oe_comlink_config_option_encrypted';
    const CONFIG_OVERRIDE_TEMPLATES = "oe_comlink_override_twig_templates";
    const CONFIG_ENABLE_MENU = "oe_comlink_add_menu_button";
    const CONFIG_ENABLE_BODY_FOOTER = "oe_comlink_add_body_footer";
    const CONFIG_ENABLE_FHIR_API = "oe_comlink_enable_fhir_api";

    private array $globalsArray;

    /**
     * @var CryptoGen
     */
    private CryptoGen $cryptoGen;

    public function __construct(array &$globalsArray)
    {
        $this->globalsArray = $globalsArray;
        $this->cryptoGen = new CryptoGen();
    }

    /**
     * Returns true if all of the settings have been configured.  Otherwise it returns false.
     * @return bool
     */
    public function isConfigured()
    {
        $keys = [self::CONFIG_OPTION_TEXT, self::CONFIG_OPTION_ENCRYPTED];
        foreach ($keys as $key) {
            $value = $this->getGlobalSetting($key);
            if (empty($value)) {
                return false;
            }
        }
        return true;
    }

    public function getTextOption()
    {
        return $this->getGlobalSetting(self::CONFIG_OPTION_TEXT);
    }

    /**
     * Returns our decrypted value if we have one, or false if the value could not be decrypted or is empty.
     * @return bool|string
     */
    public function getEncryptedOption()
    {
        $encryptedValue = $this->getGlobalSetting(self::CONFIG_OPTION_ENCRYPTED);
        return $this->cryptoGen->decryptStandard($encryptedValue);
    }

    public function getGlobalSetting($settingKey)
    {
        return $this->globalsArray[$settingKey] ?? null;
    }

    /**
     * @return array[]
     */
    public function getGlobalSettingSectionConfiguration()
    {
        $settings = [
            self::CONFIG_OPTION_TEXT => [
                'title' => 'Comlink Module Text Option'
                ,'description' => 'Example global config option with text'
                ,'type' => GlobalSetting::DATA_TYPE_TEXT
                ,'default' => ''
            ]
            ,self::CONFIG_OPTION_ENCRYPTED => [
                'title' => 'Comlink Module Encrypted Option (Encrypted)'
                ,'description' => 'Example of adding an encrypted global configuration value for your module.  Used for sensitive data'
                ,'type' => GlobalSetting::DATA_TYPE_ENCRYPTED
                ,'default' => ''
            ]
            ,self::CONFIG_OVERRIDE_TEMPLATES => [
                'title' => 'Comlink Module enable overriding twig files'
                ,'description' => 'Shows example of overriding a twig file'
                ,'type' => GlobalSetting::DATA_TYPE_BOOL
                ,'default' => ''
            ]
            ,self::CONFIG_ENABLE_MENU => [
                'title' => 'Comlink Module add module menu item'
                ,'description' => 'Shows example of adding a menu item to the system (requires logging out and logging in again)'
                ,'type' => GlobalSetting::DATA_TYPE_BOOL
                ,'default' => ''
            ]
            ,self::CONFIG_ENABLE_BODY_FOOTER => [
                'title' => 'Comlink Module Enable Body Footer example.'
                ,'description' => 'Shows example of adding a menu item to the system (requires logging out and logging in again)'
                ,'type' => GlobalSetting::DATA_TYPE_BOOL
                ,'default' => ''
            ]
            ,self::CONFIG_ENABLE_FHIR_API => [
                'title' => 'Comlink Module Enable FHIR API Extension example.'
                ,'description' => 'Shows example of extending the FHIR api with the Comlink module.'
                ,'type' => GlobalSetting::DATA_TYPE_BOOL
                ,'default' => ''
            ]
        ];
        return $settings;
    }
}
