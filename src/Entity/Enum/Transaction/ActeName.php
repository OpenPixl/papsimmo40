<?php

namespace App\Entity\Enum\Transaction;

enum ActeName: string
{
    case avenant_compromis = 'Avenant au Compromis de vente';
    case avenant_attestation = 'Avenant à l\'attestation de l\'acte de vente';
    case avenant_tracfin = 'Avenant au Tracfin';
    case avenant_facthonoraires = 'Avenant à la facture des honoraires';

    public function label(): string
    {
        return match($this) {
            self::avenant_compromis => 'Avenant au Compromis de vente',
            self::avenant_attestation => 'Avenant à l\'attestation de l\'acte de vente',
            self::avenant_tracfin => 'Avenant au Tracfin',
            self::avenant_facthonoraires => 'Avenant à la facture des honoraires',
        };
    }
}