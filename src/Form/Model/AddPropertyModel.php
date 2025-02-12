<?php

namespace App\Form\Model;

class AddPropertyModel
{
    public function __construct(
        public ?bool $isNomandat = null,
        public ?int $refMandat = null,
        public ?string $type_mandat = null,
        public ?int $destination = null,
    )
    {}
}