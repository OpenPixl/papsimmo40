<?php

namespace App\Form\Model;

class SearchConstructModel
{
    public function __construct(
        public ?int $refmandat = null,
        public ?string $name = null,
        public ?string $zipcode = null,
        public ?string $city = null,
        public ?bool $isNomandat = null
    )
    {

    }
}