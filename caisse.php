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

define("STANDARD", "standard");
define("PETITES_COUPURES", "petites_coupures");
define("PREFERENCE", 20);

// === État de la caisse (trésorerie) ===
// Clés en CHAÎNES pour éviter la conversion 0.5 → 0
$tresorerie = [
    50000   => 0,
    20000   => 1,
    10000   => 1,
    5000    => 10,
    2000    => 5,
    1000    => 10,
    200     => 45,
    100     => 30,
    50   => 30,
    20   => 45,
    10   => 45,
    5  => 45,
    2  => 45,
    1  => 45
];

function resteAtteint(float $reste): bool {
    if ($reste <= 1) {
        echo "Reste: $reste €\n";
        return true;
    }
    return false;
}

function calculerLeReste(float $montantAchat, float $montantDonne): float {
    $reste = round($montantDonne - $montantAchat, 2);
    echo "Montant à rendre : $reste €\n";
    return $reste;
}

function rendreMonnaie($montant, &$quantite, &$reste): void {
    $nb = 0;
    while ($reste >= $montant && $quantite > 0) {
        $reste -= $montant;
        $quantite--;
        $nb++;
    }
    if ($nb > 0) {
        $montant = $montant / 100;
        echo ($montant >= 10000) ? "$nb billet(s) de $montant €\n" : "$nb pièce(s) de $montant €\n";
    }
}

function sommeCaisse(array $tresorerie): float {
    $somme = 0;
    foreach ($tresorerie as $montant => $quantite) {
        $somme += $montant * $quantite;
    }
    echo "Somme totale en caisse : " . $somme/100 . "€ \n";
    return $somme;
}

function encaissement(array $tresorerie, float $montantARendre, $base = "standard"): void {
    
    $reste = $montantARendre * 100;

    if (sommeCaisse($tresorerie) < $reste) {
        echo "La caisse n'a pas assez de monnaie pour rendre le reste dû.\n";
        return;
    }
    
    if ($reste < 1) return;

    if ($base == STANDARD) {
        
        krsort($tresorerie, SORT_NUMERIC);

        foreach ($tresorerie as $montant => $quantite) {
            rendreMonnaie($montant, $quantite, $reste);
            if (resteAtteint($reste)) break;

        }
        
    } elseif ($base == PETITES_COUPURES) {
    
        ksort($tresorerie, SORT_NUMERIC);
        $map = [];
        $resteTmp = $reste;
        $quantite_prec = 0;
        foreach ($tresorerie as $montant => $quantite) {
            $quantiteNecessaire = min(($resteTmp / $montant), $quantite);
            $quantiteNecessaire = ($quantite_prec > 0 && $quantite_prec <= $quantiteNecessaire) ? $quantite_prec : $quantiteNecessaire;
            if ($montant * $quantiteNecessaire >= $resteTmp) {
                $map[$montant] = round($quantiteNecessaire);
                break;
            }
            $map[$montant] = round($quantiteNecessaire);
            $resteTmp -= $montant * $quantiteNecessaire;
            $quantite_prec = $quantiteNecessaire;
        }
        foreach ($map as $montant => $quantite) {
            rendreMonnaie($montant, $quantite, $reste);
            if (resteAtteint($reste)) break;
        }
    } elseif ($base == PREFERENCE) {
        $preference = PREFERENCE * 100;
        if (isset($tresorerie[$preference])) {
            rendreMonnaie($preference, $tresorerie[$preference], $reste);
            if (resteAtteint($reste)) return;
        }
        encaissement($tresorerie, $reste/100, STANDARD);
    }
}

// === TEST ===
// $achat = 45;
// $donne = 100;
// $reste = calculerLeReste($achat, $donne);

// encaissement($tresorerie, $reste, PREFERENCE);