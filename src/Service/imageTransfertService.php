<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\Admin\EmployedRepository;
use App\Service\PathService;

class imageTransfertService
{

    public function __construct(
        public PathService $pathService,
        public ApplicationService $applicationService,
        private HttpClientInterface $httpClient,
        private string $targetDirectoryAvatar,
        private string $targetDirectoryCi,
        public EmployedRepository $employedRepository
    ){}

    public function transfertAvatarImage(string $name, Request $request): void
    {
        $authorizationHeader = $request->headers->get('Authorization');
        //dd($authorizationHeader);
        if (!$authorizationHeader || !preg_match('/Bearer\s(\S+)/', $authorizationHeader, $matches)) {
            throw new \Exception('Invalid or missing JWT token.');
        }

        $token = $matches[1];

        $tokenParts = explode(".", $token);
        $tokenPayload = base64_decode($tokenParts[1]);
        $jwtPayload = json_decode($tokenPayload);

        $email = $jwtPayload->email;

        $user = $this->employedRepository->findOneBy(['email' => $email]);

        $scheme = $this->pathService->getScheme();
        $port = $this->applicationService->getPwaHost();
        $url = $this->applicationService->getPwaUrl();

        if(!$port){
            $imageUrl = $scheme.'://'.$url.'/prescriptors/'.$user->getSlug().'/'.$user->getAvatarName();
        }else{
            $imageUrl = $scheme.'://'.$url.':'.$port.'/prescriptors/'.$user->getSlug().'/'.$user->getAvatarName();
        }

        if(in_array("ROLE_PRESCRIBER", $jwtPayload->roles)) {
            $path = $this->targetDirectoryAvatar.$user->getSlug();
            dd($path);
            $response = $this->httpClient->request('GET', $imageUrl);
            //dd($response);
            if ($response->getStatusCode() === 200) {
                $imageContent = $response->getContent();
                $filename = basename(parse_url($imageUrl, PHP_URL_PATH));
                if(is_dir($path)) {
                    //throw new \Exception('le dossier existe.');
                    file_put_contents($path . '/' . $filename, $imageContent);
                }else{
                    //throw new \Exception('Pas de dossier.');
                    mkdir($path, 0775, true);
                    file_put_contents($path . '/' . $filename, $imageContent);
                }
            } else {
                throw new \Exception('Impossible de charger  le document.');
            }
        }else{
            throw new \Exception('Vous n\'êtes pas autoriser par l\'application à charger l\'image');
        }


    }

    public function transfertCiImage(string $name, Request $request): void
    {
        $authorizationHeader = $request->headers->get('Authorization');

        if (!$authorizationHeader || !preg_match('/Bearer\s(\S+)/', $authorizationHeader, $matches)) {
            throw new \Exception('Invalid or missing JWT token.');
        }

        $token = $matches[1];

        $tokenParts = explode(".", $token);
        $tokenPayload = base64_decode($tokenParts[1]);
        $jwtPayload = json_decode($tokenPayload);

        $email = $jwtPayload->email;
        $user = $this->employedRepository->findOneBy(['email' => $email]);

        $scheme = $this->pathService->getScheme();
        $port = $this->applicationService->getPwaHost();
        $url = $this->applicationService->getPwaUrl();

        if(!$port){
            $imageUrl = $scheme.'://'.$url.'/prescriptors/'.$user->getSlug().'/'.$user->getAvatarName();
        }else{
            $imageUrl = $scheme.'://'.$url.':'.$port.'/prescriptors/'.$user->getSlug().'/'.$user->getAvatarName();
        }

        if(in_array("ROLE_PRESCRIBER", $jwtPayload->roles)) {
            $path = $this->targetDirectoryCi.$user->getSlug();
            $response = $this->httpClient->request('GET', $imageUrl);
            if ($response->getStatusCode() === 200) {
                $imageContent = $response->getContent();
                $filename = basename(parse_url($imageUrl, PHP_URL_PATH));
                if(is_dir($path)) {
                    //throw new \Exception('le dossier existe.');
                    file_put_contents($path . '/' . $filename, $imageContent);
                }else{
                    //throw new \Exception('Pas de dossier.');
                    mkdir($path, 0775, true);
                    file_put_contents($path . '/' . $filename, $imageContent);
                }
            } else {
                throw new \Exception('Impossible de charger  le document.');
            }
        }else{
            throw new \Exception('Vous n\'ếtes pas autoriser par l\'application à charger le document');
        }


    }
}