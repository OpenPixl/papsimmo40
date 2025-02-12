<?php

namespace App\Form\Model;

class AddPropertyModel
{
    public function __construct(
        public ?bool $nomandat = null,
        public ?int $mandat = null,
        public ?int $type_mandat = null,
        public ?int $destination = null,
    )
    {}
}