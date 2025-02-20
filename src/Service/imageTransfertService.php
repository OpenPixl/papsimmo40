<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Request;

class imageTransfertService
{

    public function __construct(
        private HttpClientInterface $httpClient,
        private string $targetDirectoryAvatar,
        private string $targetDirectoryCi,
    ){}

    public function transfertAvatarImage(string $imageUrl, Request $request): void
    {
        $authorizationHeader = $request->headers->get('Authorization');

        if (!$authorizationHeader || !preg_match('/Bearer\s(\S+)/', $authorizationHeader, $matches)) {
            throw new \Exception('Invalid or missing JWT token.');
        }

        $token = $matches[1];

        $tokenParts = explode(".", $token);
        $tokenPayload = base64_decode($tokenParts[1]);
        $jwtPayload = json_decode($tokenPayload);

        if(in_array("ROLE_PRESCRIBER", $jwtPayload->roles)) {
            $response = $this->httpClient->request('GET', $imageUrl);

            if ($response->getStatusCode() === 200) {
                $imageContent = $response->getContent();
                $filename = basename(parse_url($imageUrl, PHP_URL_PATH));
                file_put_contents($this->targetDirectoryAvatar . '/' . $filename, $imageContent);
            } else {
                throw new \Exception('Impossible de charger  l\'image.');
            }
        }else{
            throw new \Exception('Vous n\'ếtes pas autoriser par l\'application à charger l\'image');
        }


    }

    public function transfertCiImage(string $imageUrl, Request $request): void
    {
        $authorizationHeader = $request->headers->get('Authorization');

        if (!$authorizationHeader || !preg_match('/Bearer\s(\S+)/', $authorizationHeader, $matches)) {
            throw new \Exception('Invalid or missing JWT token.');
        }

        $token = $matches[1];

        $tokenParts = explode(".", $token);
        $tokenPayload = base64_decode($tokenParts[1]);
        $jwtPayload = json_decode($tokenPayload);

        if(in_array("ROLE_PRESCRIBER", $jwtPayload->roles)) {
            $response = $this->httpClient->request('GET', $imageUrl);

            if ($response->getStatusCode() === 200) {
                $imageContent = $response->getContent();
                $filename = basename(parse_url($imageUrl, PHP_URL_PATH));
                file_put_contents($this->targetDirectoryCi . '/' . $filename, $imageContent);
            } else {
                throw new \Exception('Impossible de charger  le document.');
            }
        }else{
            throw new \Exception('Vous n\'ếtes pas autoriser par l\'application à charger le document');
        }


    }
}