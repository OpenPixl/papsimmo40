<?php

namespace App\Service;

use App\Entity\Gestapp\Property;
use App\Repository\Gestapp\PropertyRepository;
use App\Repository\Gestapp\PublicationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use SoapClient;

class ProtexaService
{
    public function getClient()
    {
        $client = new SoapClient('https://production.protexa.fr/WSPROTEXA_WEB/awws/wsprotexa.awws?wsdl', ['trace' => 1]);

        return $client;
    }

    public function callService($client, $service, $parameters)
    {
        $results = (array)$client->__soapCall($service, [
            'parameters' => $parameters
        ]);
        $xml = simplexml_load_string($results[$service.'Result']);
        $json = json_encode($xml);
        $ws = json_decode($json,TRUE);

        return $ws;
    }
}