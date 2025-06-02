<?php

define('TAUX_TVA', [0,10,18]);

function getNumberToWord($number , $language='fr'){
    // create the number to words "manager" class
    $numberToWords = new NumberToWords();
    // build a new number transformer using the RFC 3066 language identifier
    $numberTransformer = $numberToWords->getNumberTransformer($language);
    return  $numberTransformer->toWords($number);
}

function getInvoiceNumber($invoince_id){
    return INVOICE_PREFIX.str_pad($invoince_id, 6, "0", STR_PAD_LEFT);
}

function remplacerPremierePartie($chaine, $nouvelleValeur , $key=0) {
    // Séparer la chaîne par les slashs
    $parties = explode('/', $chaine);
    // Vérifier qu'il y a bien des parties à modifier
    if (count($parties) > 1) {
        // Remplacer la première partie par la nouvelle valeur
        $parties[$key] = $nouvelleValeur;
        // Rejoindre les parties pour reformer la chaîne
        return implode('/', $parties);
    }
    // Retourner la chaîne d'origine si aucune modification n'a été faite
    return $chaine;
}


function convertTimestamp($timestamp) {
    // Use DateTime class to parse the input string
    $dateTime = DateTime::createFromFormat('YmdHis', $timestamp);
    // Return the formatted date as 'YYYY-MM-DD hh:mm:ss'
    return $dateTime->format('Y-m-d H:i:s');
}


function prixVenteHorsTva($price, $taux = 0.18){
    $res = $price / (1 + $taux );
    return ARRONDIR_RESULTAT ? round($res) : number_format($res, 2 );
}
function prixVenteTvac($price, $taux = 0.18){
    $res = $price * (1 + $taux );
    return ARRONDIR_RESULTAT ? round($res) : number_format($res, 2 );
}

function calculerTauxTVA($prixHT, $montantTVA) {
    if ($prixHT <= 0) {
        return "Erreur : Le prix HT doit être supérieur à 0";
    }
    // Calcul du taux de TVA
    $tauxTVA = ($montantTVA / $prixHT) * 100;
    // Arrondir à 2 décimales
    return round($tauxTVA, 2);
}
function getPrice($price)
{
    $price = floatval($price);
    return number_format($price, 2, ',', ' . ');
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

function isValideNumber($number){
    return is_numeric($number);
}
function getMaisonById($id){
    return MaisonLocation::find($id);
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
