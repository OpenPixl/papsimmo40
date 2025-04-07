<?php

namespace App\Form\Model;

class SearchCustomerPropertyModel
{
    public function __construct(
        public ?string $name = null,
    )
    {}
}