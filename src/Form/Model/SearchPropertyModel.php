<?php

namespace App\Form\Model;

use App\Entity\Admin\Employed;

class SearchPropertyModel
{
    public function __construct(
        public ?string $name = null,
        public ?Employed $refEmployed = null,
    )
    {

    }
}