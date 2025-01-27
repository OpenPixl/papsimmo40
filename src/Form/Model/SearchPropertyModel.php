<?php

namespace App\Form\Model;

class SearchPropertyModel
{
    public function __construct(
        public ?int $refmandat = null,
        public ?string $zipcode = null,
        public ?int $minPrice = null,
        public ?int $maxPrice = null
    )
    {}
}