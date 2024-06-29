<?php

namespace App\Service;

use App\Entity\Gestapp\Property;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Component\HttpFoundation\RequestStack;

class QrcodeService
{
    public function __construct(
        private RequestStack $request,
    ){}

    public function qrcode($query)
    {
        $url = '';

        $objDateTime = new \DateTime('NOW');
        $dateString = $objDateTime->format('d-m-Y H:i:s');

        $path = dirname(__DIR__, 2).'/public/';

        // set qrcode
        $result = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data('Custom QR code contents')
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::High)
            ->size(300)
            ->margin(10)
            ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
            //->logoPath($path.'images/png/LogoPAPSimmo.png')
            //->logoResizeToWidth(80)
            //->logoPunchoutBackground(true)
            ->labelText('This is the label')
            ->labelFont(new NotoSans(20))
            ->labelAlignment(LabelAlignment::Center)
            ->validateResult(false)
            ->build();
        ;

        //generate name
        $namePng = uniqid('', '') . '.png';

        //Save img png
        $result->saveToFile($path.'doc/qrcode/'.$namePng);

        return $result->getDataUri();
    }

    public function qrcodeOneProperty(Property $property)
    {
        $url = '/gestapp/propertypublic/oneproperty/'.$property->getId();

        // récupération de la référence
        $ref = explode("/", $property->getRef());
        $newref = $ref[0].'-'.$ref[1];

        $objDateTime = new \DateTime('NOW');
        $dateString = $objDateTime->format('d-m-Y H:i:s');

        $path = dirname(__DIR__, 2).'/public/';

        // set qrcode
        $result = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data($path.$url)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::High)
            ->size(300)
            ->margin(10)
            ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
            //->logoPath($path.'images/png/LogoPAPSimmo.png')
            //->logoResizeToWidth(80)
            //->logoPunchoutBackground(true)
            ->labelText('Accéder au site')
            ->labelFont(new NotoSans(20))
            ->labelAlignment(LabelAlignment::Center)
            ->validateResult(false)
            ->build();
        ;

        //generate name
        $namePng = uniqid('', '') . '.png';

        //Save img png
        $result->saveToFile($path.'properties/'.$newref.'/'.$namePng);

        return $result->getDataUri();
    }
}