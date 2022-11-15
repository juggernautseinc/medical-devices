<?php

/*
 *
 *  @package       OpenEMR
 *  @link    https://www.open-emr.org https://affordablecustomehr.com
 *  @author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  @copyright Copyright (c) 2022 Sherwin Gaddis <sherwingaddis@gmail.com>
 *  @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 *
 */

namespace Comlink\OpenEMR\Module\FHIR;

    use OpenEMR\Services\FHIR\FhirServiceBase;
    use OpenEMR\Services\PatientService;
    use OpenEMR\FHIR\R4\FHIRDomainResource\FHIRPatient;
    use OpenEMR\FHIR\R4\FHIRElement\FHIRAddress;
    use OpenEMR\FHIR\R4\FHIRElement\FHIRHumanName;
    use OpenEMR\FHIR\R4\FHIRElement\FHIRAdministrativeGender;
    use OpenEMR\FHIR\R4\FHIRElement\FHIRId;
    use OpenEMR\Services\Search\FhirSearchParameterDefinition;
    use OpenEMR\Services\Search\SearchFieldType;
    use OpenEMR\Services\Search\ServiceField;
    use OpenEMR\Validators\ProcessingResult;

    /**
     * FHIR Patient Service
     *
     * @coversDefaultClass OpenEMR\Services\FHIR\FhirPatientService
     * @package   OpenEMR
     * @link      http://www.open-emr.org
     * @author    Jerry Padgett <sjpadgett@gmail.com>
     * @author    Dixon Whitmire <dixonwh@gmail.com>
     * @copyright Copyright (c) 2020 Jerry Padgett <sjpadgett@gmail.com>
     * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
     *
     */
class FhirPatientBulkService extends FhirServiceBase
{
    /**
     * @var PatientService
     */
    private $patientService;

    public function __construct()
    {

        parent::__construct();
        $this->patientService = new PatientService();
    }

    /**
     * Returns an array mapping FHIR Patient Resource search parameters to OpenEMR Patient search parameters
     * @return array The search parameters
     */
    protected function loadSearchParameters()
    {
        return [
            '_id' => new FhirSearchParameterDefinition('_id', SearchFieldType::TOKEN, [new ServiceField('uuid', ServiceField::TYPE_UUID)])
            , 'identifier' => new FhirSearchParameterDefinition('identifier', SearchFieldType::TOKEN, ['ss', 'pubpid']),
            'name' => new FhirSearchParameterDefinition('name', SearchFieldType::STRING, ['title', 'fname', 'mname', 'lname']),
            'birthdate' => new FhirSearchParameterDefinition('birthdate', SearchFieldType::DATE, ['DOB']),
            'gender' => new FhirSearchParameterDefinition('gender', SearchFieldType::TOKEN, ['sex']),
            'address' => new FhirSearchParameterDefinition(
                'address',
                SearchFieldType::STRING,
                ['street', 'street_line_2', 'postal_code', 'city', 'state', 'contact_address_line1'
                    , 'contact_address_line2', 'contact_address_postal_code', 'contact_address_city'
                    ,
                    'contact_address_state',
                    'contact_address_district']
            ),

            // these are not standard in US Core
            'address-city' => new FhirSearchParameterDefinition(
                'address-city',
                SearchFieldType::STRING,
                ['city', 'contact_address_city']
            ),
            'address-postalcode' => new FhirSearchParameterDefinition(
                'address-postalcode',
                SearchFieldType::STRING,
                ['postal_code', 'contact_address_postal_code']
            ),
            'address-state' => new FhirSearchParameterDefinition(
                'address-state',
                SearchFieldType::STRING,
                ['state', 'contact_address_state']
            ),

            'email' => new FhirSearchParameterDefinition('email', SearchFieldType::TOKEN, ['email']),
            'family' => new FhirSearchParameterDefinition('family', SearchFieldType::STRING, ['lname']),
            'given' => new FhirSearchParameterDefinition('given', SearchFieldType::STRING, ['fname', 'mname']),
            'phone' => new FhirSearchParameterDefinition('phone', SearchFieldType::TOKEN, ['phone_home', 'phone_biz', 'phone_cell']),
            'telecom' => new FhirSearchParameterDefinition('telecom', SearchFieldType::TOKEN, ['email', 'phone_home', 'phone_biz', 'phone_cell'])
        ];
    }

    /**
     * Parses an OpenEMR patient record, returning the equivalent FHIR Patient Resource
     *
     * @param array $dataRecord The source OpenEMR data record
     * @param boolean $encode Indicates if the returned resource is encoded into a string. Defaults to false.
     * @return FHIRPatient
     */
    public function parseOpenEMRRecord($dataRecord = array(), $encode = false)
    {
        $patientResource = new FHIRPatient();

        $meta = array('versionId' => '1', 'lastUpdated' => gmdate('c'));
        $patientResource->setMeta($meta);

        $patientResource->setActive(true);

        $narrativeText = '';
        if (isset($dataRecord['fname'])) {
            $narrativeText = $dataRecord['fname'];
        }
        if (isset($dataRecord['lname'])) {
            $narrativeText .= ' ' . $dataRecord['lname'];
        }
        $text = array(
            'status' => 'generated',
            'div' => '<div xmlns="http://www.w3.org/1999/xhtml"> <p>' . $narrativeText . '</p></div>'
        );
        $patientResource->setText($text);

        $id = new FHIRId();
        $id->setValue($dataRecord['uuid']);
        $patientResource->setId($id);

        $name = new FHIRHumanName();
        $name->setUse('official');

        if (isset($dataRecord['title'])) {
            $name->addPrefix($dataRecord['title']);
        }
        if (isset($dataRecord['lname'])) {
            $name->setFamily($dataRecord['lname']);
        }

        $givenName = array();
        if (isset($dataRecord['fname'])) {
            array_push($givenName, $dataRecord['fname']);
        }

        if (isset($dataRecord['mname'])) {
            array_push($givenName, $dataRecord['mname']);
        }

        if (count($givenName) > 0) {
            $name->given = $givenName;
        }

        $patientResource->addName($name);

        if (isset($dataRecord['DOB'])) {
            $patientResource->setBirthDate($dataRecord['DOB']);
        }

        $address = new FHIRAddress();
        if (isset($dataRecord['street'])) {
            $address->addLine($dataRecord['street']);
        }
        if (isset($dataRecord['city'])) {
            $address->setCity($dataRecord['city']);
        }
        if (isset($dataRecord['state'])) {
            $address->setState($dataRecord['state']);
        }
        if (isset($dataRecord['postal_code'])) {
            $address->setPostalCode($dataRecord['postal_code']);
        }
        if (isset($dataRecord['country_code'])) {
            $address->setCountry($dataRecord['country_code']);
        }

        $patientResource->addAddress($address);

        if (isset($dataRecord['phone_home'])) {
            $patientResource->addTelecom(array(
                'system' => 'phone',
                'value' => $dataRecord['phone_home'],
                'use' => 'home'
            ));
        }

        if (isset($dataRecord['phone_biz'])) {
            $patientResource->addTelecom(array(
                'system' => 'phone',
                'value' => $dataRecord['phone_biz'],
                'use' => 'work'
            ));
        }

        if (isset($dataRecord['phone_cell'])) {
            $patientResource->addTelecom(array(
                'system' => 'phone',
                'value' => $dataRecord['phone_cell'],
                'use' => 'mobile'
            ));
        }

        if (isset($dataRecord['email'])) {
            $patientResource->addTelecom(array(
                'system' => 'email',
                'value' => $dataRecord['email'],
                'use' => 'home'
            ));
        }

        $gender = new FHIRAdministrativeGender();
        if (isset($dataRecord['sex'])) {
            $gender->setValue(strtolower($dataRecord['sex']));
        }
        $patientResource->setGender($gender);

        if (isset($dataRecord['ss'])) {
            $fhirIdentifier = [
                'use' => 'official',
                'type' => [
                    'coding' => [
                        [
                            'system' => 'http://terminology.hl7.org/CodeSystem/v2-0203',
                            'code' => 'SS'
                        ]
                    ]
                ],
                'system' => 'http://hl7.org/fhir/sid/us-ssn',
                'value' => $dataRecord['ss']
            ];
            $patientResource->addIdentifier($fhirIdentifier);
        }

        if (isset($dataRecord['pubpid'])) {
            $fhirIdentifier = [
                'use' => 'official',
                'type' => [
                    'coding' => [
                        [
                            'system' => 'http://terminology.hl7.org/CodeSystem/v2-0203',
                            'code' => 'PT'
                        ]
                    ]
                ],
                'system' => 'http:\\terminology.hl7.org\ValueSet\v2-0203',
                'value' => $dataRecord['pubpid']
            ];
            $patientResource->addIdentifier($fhirIdentifier);
        }

        if ($encode) {
            return json_encode($patientResource);
        } else {
            return $patientResource;
        }
    }

    /**
     * Parses a FHIR Patient Resource, returning the equivalent OpenEMR patient record.
     *
     * @param array $fhirResource The source FHIR resource
     * @return array a mapped OpenEMR data record (array)
     */
    public function parseFhirResource($fhirResource = array())
    {

        $data = array();

        if (isset($fhirResource['id'])) {
            $data['uuid'] = $fhirResource['id'];
        }

        if (isset($fhirResource['name'])) {
            $name = [];
            foreach ($fhirResource['name'] as $sub_name) {
                if ($sub_name['use'] === 'official') {
                    $name = $sub_name;
                    break;
                }
            }
            if (isset($name['family'])) {
                $data['lname'] = $name['family'];
            }
            if ($name['given'][0]) {
                $data['fname'] = $name['given'][0];
            }
            if (isset($name['given'][1])) {
                $data['mname'] = $name['given'][1];
            }
            if (isset($name['prefix'][0])) {
                $data['title'] = $name['prefix'][0];
            }
        }
        if (isset($fhirResource['address'])) {
            if (isset($fhirResource['address'][0]['line'][0])) {
                $data['street'] = $fhirResource['address'][0]['line'][0];
            }
            if (isset($fhirResource['address'][0]['postalCode'][0])) {
                $data['postal_code'] = $fhirResource['address'][0]['postalCode'];
            }
            if (isset($fhirResource['address'][0]['city'][0])) {
                $data['city'] = $fhirResource['address'][0]['city'];
            }
            if (isset($fhirResource['address'][0]['state'][0])) {
                $data['state'] = $fhirResource['address'][0]['state'];
            }
            if (isset($fhirResource['address'][0]['country'][0])) {
                $data['country_code'] = $fhirResource['address'][0]['country'];
            }
        }
        if (isset($fhirResource['telecom'])) {
            foreach ($fhirResource['telecom'] as $telecom) {
                switch ($telecom['system']) {
                    case 'phone':
                        switch ($telecom['use']) {
                            case 'mobile':
                                $data['phone_cell'] = $telecom['value'];
                                break;
                            case 'home':
                                $data['phone_home'] = $telecom['value'];
                                break;
                            case 'work':
                                $data['phone_biz'] = $telecom['value'];
                                break;
                        }
                        break;
                    case 'email':
                        $data['email'] = $telecom['value'];
                        break;
                    default:
                        //Should give Error for incapability
                        break;
                }
            }
        }
        if (isset($fhirResource['birthDate'])) {
            $data['DOB'] = $fhirResource['birthDate'];
        }
        if (isset($fhirResource['gender'])) {
            $data['sex'] = $fhirResource['gender'];
        }
        if (isset($fhirResource['identifier'])) {
            foreach ($fhirResource['identifier'] as $index => $identifier) {
                if (!isset($identifier['type']['coding'][0])) {
                    continue;
                }

                $code = $identifier['type']['coding'][0]['code'];
                switch ($code) {
                    case 'SS':
                        $data['ss'] = $identifier['value'];
                        break;
                    case 'PT':
                        $data['pubpid'] = $identifier['value'];
                        break;
                }
            }
        }
        if (isset($fhirResource['bulkVitals'])) {
            foreach ($fhirResource['bulkVitals'] as $bulkvalues) {

            }
        }

// echo $fhirResource['bulkVitals'];
// print_r($fhirResource['bulkVitals']);
// die;
        return $data;
    }

    /**
     * Comlink medical device import data
     * Inserts an OpenEMR record into the system.
     *
     * @param array $openEmrRecord OpenEMR patient record
     *
     */
    public function insertOpenEMRRecord($openEmrRecord)
    {
        die($this->patientService->insertbulkpatient($openEmrRecord));
    }


    /**
     * Updates an existing OpenEMR record.
     *
     * @param $fhirResourceId //The OpenEMR record's FHIR Resource ID.
     * @param $updatedOpenEMRRecord //The "updated" OpenEMR record.
     * @return ProcessingResult
     */
    public function updateOpenEMRRecord($fhirResourceId, $updatedOpenEMRRecord)
    {
        $processingResult = $this->patientService->update($fhirResourceId, $updatedOpenEMRRecord);
        return $processingResult;
    }

    /**
     * Performs a FHIR Patient Resource lookup by FHIR Resource ID
     * @param $fhirResourceId //The OpenEMR record's FHIR Patient Resource ID.
     */
    public function getOne($fhirResourceId, $puuidBind = null): ProcessingResult
    {
        $processingResult = $this->patientService->getOne($fhirResourceId);
        if (!$processingResult->hasErrors()) {
            if (count($processingResult->getData()) > 0) {
                $openEmrRecord = $processingResult->getData()[0];
                $fhirRecord = $this->parseOpenEMRRecord($openEmrRecord);
                $processingResult->setData([]);
                $processingResult->addData($fhirRecord);
            }
        }
        return $processingResult;
    }

    /**
     * Searches for OpenEMR records using OpenEMR search parameters
     *
     * @param array openEMRSearchParameters OpenEMR search fields
     * @return ProcessingResult
     */
    public function searchForOpenEMRRecords($openEMRSearchParameters): ProcessingResult
    {
        return $this->patientService->getAll($openEMRSearchParameters, false);
    }

    public function createProvenanceResource($dataRecord = array(), $encode = false)
    {
        // TODO: If Required in Future
    }
}

