// caisse enregistreuse

// etat de caisse de base 
/**
 * 
 *  piece de 2 euro : 45
 *  piece de 1 euro : 30
 *  piece de 0,5 euro : 30
 *  piece de 0,2 euro : 45
 *  piece de 0,1 euro : 45
 *  piece de 0,05 euro : 45
 *  piece de 0,02 euro : 45
 *  piece de 0,01 euro : 45
 * 
 *  Billet de 10 : 10
 *  Billet de 20 : 5
 *  Billet de 50 : 10
 *  Billet de 100 : 1
 *  Billet de 200 : 1
 *  Billet de 500 : 0
 * 
 *  Logiciel de caisse, qui va calculer le reste dû au client (client qui donne une somme moins le montant total des articles)
 *  Le logiciel va aussi calculer le nombre de pièce ou billet (avec la valeur) à rendre au client.
 * 
 * ex : le client achète pour 33,40 de bonbons
 * il donne 1 billet de 50 euros
 * le logiciel calcule qu'il faut lui rendre 16,60 Euros 
 * le logiciel calcule qu'il faut rendre 1 billet de 10, 3 pièces de 2, 1 pièce de 0.50 et 1 pièce de 0.10
 * 
 */

<?php

// === État de la caisse (trésorerie) ===
// Clés en CHAÎNES pour éviter la conversion 0.5 → 0
$tresorerie = [
    '500'   => 0,
    '200'   => 1,
    '100'   => 1,
    '50'    => 10,
    '20'    => 5,
    '10'    => 10,
    '2'     => 45,
    '1'     => 30,
    '0.5'   => 30,
    '0.2'   => 45,
    '0.1'   => 45,
    '0.05'  => 45,
    '0.02'  => 45,
    '0.01'  => 45
];

function calculerLeReste(float $montantAchat, float $montantDonne): float {
    return round($montantDonne - $montantAchat, 2);
}

function calculeBillet($billet, $quantite, &$reste): int {
    $nb_billet = 0;
    $billet = (float)$billet;
    while ($reste >= $billet && $quantite > 0) {
        $reste -= $billet;
        $quantite--;
        $nb_billet++;
    }
    if ($nb_billet > 0) {
        echo "$nb_billet billet(s) de $billet €\n";
    }
    return $nb_billet;
}

function calculePiece($piece, $quantite, &$reste): int {
    $nb_piece = 0;
    $piece = (float)$piece;
    while ($reste >= $piece && $quantite > 0) {
        $reste -= $piece;
        $quantite--;
        $nb_piece++;
    }
    if ($nb_piece > 0) {
        echo "$nb_piece pièce(s) de $piece €\n";
    }
    return $nb_piece;
}

function monnaieRendre(array $tresorerie, float $montantRendre): void {
    $reste = $montantRendre;
    echo "Montant à rendre : $reste €\n";

    $montants = array_keys($tresorerie);
    $montants = array_map('floatval', $montants);
    rsort($montants, SORT_NUMERIC);

    foreach ($montants as $montant) {
        $quantite = $tresorerie[(string)$montant];

        if ($montant >= 10) {
            calculeBillet($montant, $quantite, $reste);
        } else {
            calculePiece($montant, $quantite, $reste);
        }

        $reste = round($reste, 2);
        if ($reste <= 0.001) break;
    }

    if ($reste > 0.001) {
        echo "Attention : reste non rendu : $reste €\n";
    }
}

// === TEST ===
$achat = 5.50;
$donne = 7;
$reste = calculerLeReste($achat, $donne);

monnaieRendre($tresorerie, $reste);