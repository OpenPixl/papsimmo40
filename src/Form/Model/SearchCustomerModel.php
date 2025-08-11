<?php

namespace App\Form\Model;

class SearchCustomerModel
{
    public function __construct(
        public ?string $slug = null,
    )
    {}
}