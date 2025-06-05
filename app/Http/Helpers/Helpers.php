<?php
use NumberToWords\NumberToWords;

define('TAUX_TVA', [0,10,18]);

function getNumberToWord($number , $language='fr'){
    // create the number to words "manager" class
    $numberToWords = new NumberToWords();
    // build a new number transformer using the RFC 3066 language identifier
    $numberTransformer = $numberToWords->getNumberTransformer($language);
    return  $numberTransformer->toWords($number);
}

const MOUVEMENT_STOCK = [
    'EN' => 'Entrée Normales',
    'ER' => 'Entrée Retour',
    'EI' => 'Entrée Inventaire',
    'EAJ' => 'Entrées Ajustement',
    'ET' => 'Entrées Transfert',
    'EAU' => 'Entrées Autres',
    'SN' => 'Sorties Normales',
    'SP' => 'Sorties Perte',
    'SV' => 'Sorties Vol',
    'SD' => 'Sorties Désuétude',
    'SC' => 'Sorties Casse',
    'SAJ' => 'Sorties Ajustement',
    'ST' => 'Sorties Transfert',
    'SAU' => 'Sorties Autres',
];

const TYPE_PAYMENT = [
    1 => 'En espèce',
    2 => 'banque',
    3 => 'à crédit',
    4 => 'autres',
];

const TVA_RANGES =[18,10,4,0];

function getMouvement($key){
    return  MOUVEMENT_STOCK[$key];
}

function setActiveRoute($route){
    return request()->routeIs($route) ? 'active' : '';
}

function sub_letters($text, $limit = 50, $ellipsis = '...') {
    $text = trim($text);
    if (mb_strlen($text) <= $limit) {
        return $text; // Return original text if within the limit
    }    // Cut the text at the limit
    $truncated = mb_substr($text, 0, $limit);
    // Find last space to avoid breaking words
    if (($lastSpace = mb_strrpos($truncated, ' ')) !== false) {
        $truncated = mb_substr($truncated, 0, $lastSpace);
    }
    return $truncated . $ellipsis;
}
