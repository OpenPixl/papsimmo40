<?php

namespace App\Entity\Enum;

enum transactionStep: string
{
    case firststep_open = 'ouverture du dossier';
    case secondStep_DatePromise = 'Date de signature promesse de vente fixée';
    case secondStep_pdfPromise_waitValid = 'Chargement de l\'attestation de promesse de vente | Attente de validation par l\'administration';
    case secondStep_pdfPromise_isvalid = 'Chargement de l\'attestation de promesse de vente | Document validé par l\'administrateur';
    case secondStep_pdfPromise_honoraires = 'Dépot des honoraires';
    case thirdStep_DateActe = 'Date de signature de l\'acte vente fixée';
    case thirdStep_pdfActe_waitValid = 'Chargement de l\'attestation de l\'acte  de vente | Attente de validation par l\'administration';
    case thirdStep_pdfActe_isvalid = 'Chargement de l\'attestation de l\'acte de vente | Document validé par l\'administrateur';
    case fourthStep_pdfTracfin_waitValid = 'Chargement de l\'attestation du Tracfin | Attente de validation par l\'administration';
    case foursthStep_pdfTracfin_isvalid = 'Chargement de l\'attestation de du Tracfin | Document validé par l\'administrateur';
}