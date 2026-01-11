<?php
// ... (Chargement standard Dolibarr comme avant) ...
$res = 0;
if (! $res && file_exists("../main.inc.php")) $res = @include "../main.inc.php";
if (! $res && file_exists("../../main.inc.php")) $res = @include "../../main.inc.php";
if (! $res && file_exists("../../../main.inc.php")) $res = @include "../../../main.inc.php";
if (! $res) die("Include of main fails");

require_once DOL_DOCUMENT_ROOT . '/core/lib/date.lib.php';
// IMPORTANT : On charge la librairie du module pour avoir la fonction des onglets
require_once DOL_DOCUMENT_ROOT . '/custom/comptae/lib/comptae.lib.php'; 

$langs->load("comptae@comptae");

// Sécurité
if (!$user->rights->comptae->read) accessforbidden();

$title = $langs->trans("URSSAF");
llxHeader("", $title);

// --- AFFICHAGE ---

// 1. On charge les onglets définis dans la lib
$head = comptaeUrssafPrepareHead();

// 2. On affiche l'entête avec les onglets (le 2eme paramètre 'payment' indique que c'est l'onglet actif)
print dol_get_fiche_head($head, 'payment', $langs->trans("Paiement URSSAF"), -1, 'bank');

print '<div class="fichecenter">';
print 'Ici le contenu de votre page de paiement URSSAF...';
print '</div>';

// 3. On ferme la zone des onglets
print dol_get_fiche_end();

llxFooter();
$db->close();
?>