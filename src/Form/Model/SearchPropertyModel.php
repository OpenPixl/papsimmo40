<?php

namespace App\Form\Model;

class SearchPropertyModel
{
    public function __construct(
        public ?string $projet = null,
        public ?int $refmandat = null,
        public ?string $zipcode = null,
        public ?string $city = null,
        public ?int $minPrice = null,
        public ?int $maxPrice = null
    )
    {}
}