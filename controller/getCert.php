<?php

namespace AndrewSvirin\Ebics\Tests\Factories\X509;

use AndrewSvirin\Ebics\Models\X509\AbstractX509Generator;

/**
 * Legacy X509 certificate generator @see X509GeneratorInterface.
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Guillaume Sainthillier
 */
class BNPPX509Generator extends AbstractX509Generator
{
    public function __construct()
    {
        parent::__construct();
        $this->setCertificateOptions([
            'subject' => [
                'DN' => [
                    'id-at-commonName' => 'ebics.ecodrop.net',
                    'id-at-organizationName' => 'ECODROP',
                    'id-at-stateOrProvinceName' => 'IDF',
                    'id-at-localityName' => 'SAINT-CLOUD',
                    'id-at-countryName' => 'FR',
                ],
            ],
            'issuer' => [
                'DN' => [
                    'id-at-commonName' => 'ebics.ecodrop.net',
                    'id-at-organizationName' => 'ECODROP',
                    'id-at-countryName' => 'FR',
                ],
            ],
        ]);
    }
}
