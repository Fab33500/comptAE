<?php
/**
 * \file    comptae/recettes.php
 * \ingroup comptae
 * \brief   Page du Livre des Recettes
 */

// 1. Chargement de l'environnement Dolibarr
$res = 0;
if (! $res && file_exists("../main.inc.php")) $res = @include "../main.inc.php";
if (! $res && file_exists("../../main.inc.php")) $res = @include "../../main.inc.php";
if (! $res && file_exists("../../../main.inc.php")) $res = @include "../../../main.inc.php";
if (! $res) die("Include of main fails");

// 2. Chargement des bibliothèques et traductions
require_once DOL_DOCUMENT_ROOT . '/core/lib/date.lib.php';
$langs->load("comptae@comptae"); // Charge votre fichier de langue

// 3. Contrôle d'accès (Sécurité)
if (!$user->rights->comptae->read) {
    accessforbidden();
}

// 4. En-tête de la page (Menu haut, CSS, JS)
$title = $langs->trans("Livre des Recettes");
llxHeader("", $title);

// 5. Contenu de la page
print load_fiche_titre($title, '', 'bill');

print '<div class="fichecenter">';
print '<div class="underbanner clearboth"></div>';

// Zone de description ou de filtres (à développer plus tard)
print '<table class="border centpercent">';
print '<tr class="liste_titre">';
print '<td>Date</td>';
print '<td>Tiers</td>';
print '<td>Description</td>';
print '<td>Montant</td>';
print '<td>Mode de règlement</td>';
print '</tr>';

print '<tr class="oddeven"><td colspan="5" class="opacitymedium">Ici s\'affichera la liste de vos encaissements (Requête SQL sur llx_paiement à faire).</td></tr>';

print '</table>';

print '</div>';

// 6. Pied de page
llxFooter();
$db->close();
?>