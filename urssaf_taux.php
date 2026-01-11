<?php
/**
 * \file    comptae/urssaf_taux.php
 * \ingroup comptae
 * \brief   Page de configuration des taux URSSAF et Seuils
 */

// 1. Chargement environnement
$res = 0;
if (! $res && file_exists("../main.inc.php")) $res = @include "../main.inc.php";
if (! $res && file_exists("../../main.inc.php")) $res = @include "../../main.inc.php";
if (! $res && file_exists("../../../main.inc.php")) $res = @include "../../../main.inc.php";
if (! $res) die("Include of main fails");

require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/html.form.class.php';
require_once DOL_DOCUMENT_ROOT . '/custom/comptae/lib/comptae.lib.php'; 

$langs->load("comptae@comptae");

// 2. Sécurité
if (!$user->rights->comptae->read) accessforbidden();
$action = GETPOST('action', 'alpha');

$form = new Form($db);

// 3. Enregistrement des données
if ($action == 'update') {
    // Taux Cotisations
    dolibarr_set_const($db, "COMPTAE_TAUX_VENTE", GETPOST('taux_vente', 'int'), 'chaine', 0, '', $conf->entity);
    dolibarr_set_const($db, "COMPTAE_TAUX_SERVICE_BIC", GETPOST('taux_service_bic', 'int'), 'chaine', 0, '', $conf->entity);
    dolibarr_set_const($db, "COMPTAE_TAUX_SERVICE_BNC", GETPOST('taux_service_bnc', 'int'), 'chaine', 0, '', $conf->entity);
    dolibarr_set_const($db, "COMPTAE_TAUX_CFP", GETPOST('taux_cfp', 'int'), 'chaine', 0, '', $conf->entity);
    dolibarr_set_const($db, "COMPTAE_TAUX_LIBERATOIRE", GETPOST('taux_liberatoire', 'int'), 'chaine', 0, '', $conf->entity);
    
    // NOUVEAU : Enregistrement des DEUX Seuils
    dolibarr_set_const($db, "COMPTAE_SEUIL_TVA_VENTE", GETPOST('seuil_tva_vente', 'int'), 'chaine', 0, '', $conf->entity);
    dolibarr_set_const($db, "COMPTAE_SEUIL_TVA_SERVICE", GETPOST('seuil_tva_service', 'int'), 'chaine', 0, '', $conf->entity);
    
    setEventMessages($langs->trans("ConfigurationSaved"), null, 'mesgs');
}

// 4. Affichage
$title = $langs->trans("Configuration Taux & Seuils");
llxHeader("", $title);

$head = comptaeUrssafPrepareHead();
print dol_get_fiche_head($head, 'taux', $langs->trans("Configuration"), -1, 'generic');

print '<div class="fichecenter">';
print info_admin("Configurez ici vos taux de cotisations et vos seuils de TVA.");
print '<br>';

print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
print '<input type="hidden" name="action" value="update">';
print '<input type="hidden" name="token" value="'.newToken().'">';

print '<table class="noborder centpercent">';

// --- SECTION SEUILS (DOUBLE) ---
print '<tr class="liste_titre"><td colspan="3">Seuils de Franchise en base de TVA (Activité mixte)</td></tr>';

$help_seuil = "<b>La règle de cumul :</b><br>Si vous faites de l'activité mixte, vous ne devez dépasser ni le seuil global, ni le seuil de service.";

// Récupération valeurs (défauts 2025)
$val_seuil_vente   = getDolGlobalString('COMPTAE_SEUIL_TVA_VENTE', '101000'); 
$val_seuil_service = getDolGlobalString('COMPTAE_SEUIL_TVA_SERVICE', '39100'); 

print '<tr class="oddeven">';
print '<td class="titlefield">Seuil Global (Vente + Service) ' . $form->textwithpicto('', $help_seuil) . '</td>';
print '<td><input type="text" name="seuil_tva_vente" size="10" value="'.$val_seuil_vente.'"> €</td>';
print '<td class="opacitymedium">Plafond général (ex: 101 000 €)</td>';
print '</tr>';

print '<tr class="oddeven">';
print '<td class="titlefield">Seuil Service (Service uniquement)</td>';
print '<td><input type="text" name="seuil_tva_service" size="10" value="'.$val_seuil_service.'"> €</td>';
print '<td class="opacitymedium">Plafond spécifique aux services inclus dans le global (ex: 39 100 €)</td>';
print '</tr>';


// --- SECTION TAUX (Inchangée) ---
$help_text = "<b>Comment configurer votre taux ?</b><br>Modifiez les valeurs si vous avez l'ACCRE ou situation spécifique.";
print '<tr class="liste_titre">';
print '<td>Taux de Cotisations ' . $form->textwithpicto('', $help_text) . '</td>';
print '<td>Valeur (%)</td>';
print '<td>Note</td>';
print '</tr>';

// Vente Marchandise
$val = getDolGlobalString('COMPTAE_TAUX_VENTE', '12.30');
print '<tr class="oddeven"><td>Vente de marchandises (BIC)</td>';
print '<td><input type="text" name="taux_vente" size="5" value="'.$val.'"> %</td>';
print '<td class="opacitymedium">Standard 2025</td></tr>';

// Presta BIC
$val = getDolGlobalString('COMPTAE_TAUX_SERVICE_BIC', '21.20');
print '<tr class="oddeven"><td>Prestations de services (BIC)</td>';
print '<td><input type="text" name="taux_service_bic" size="5" value="'.$val.'"> %</td>';
print '<td class="opacitymedium">Artisans / Commerçants</td></tr>';

// Presta BNC
$val = getDolGlobalString('COMPTAE_TAUX_SERVICE_BNC', '24.60');
print '<tr class="oddeven"><td>Prestations de services (BNC)</td>';
print '<td><input type="text" name="taux_service_bnc" size="5" value="'.$val.'"> %</td>';
print '<td class="opacitymedium">Professions Libérales</td></tr>';

print '<tr class="liste_titre"><td colspan="3">Taxes additionnelles</td></tr>';
$val = getDolGlobalString('COMPTAE_TAUX_CFP', '0.10'); 
print '<tr class="oddeven"><td>Contribution Formation Pro (CFP)</td>';
print '<td><input type="text" name="taux_cfp" size="5" value="'.$val.'"> %</td>';
print '<td class="opacitymedium">0.1% (Commerce), 0.2% (Service), 0.3% (Artisan)</td></tr>';

$val = getDolGlobalString('COMPTAE_TAUX_LIBERATOIRE', '0.00');
print '<tr class="oddeven"><td>Versement Libératoire de l\'impôt</td>';
print '<td><input type="text" name="taux_liberatoire" size="5" value="'.$val.'"> %</td>';
print '<td class="opacitymedium">Laissez à 0 si non applicable</td></tr>';

print '</table>';

print '<br><div class="center"><input type="submit" class="button" value="'.$langs->trans("Enregistrer").'"></div>';
print '</form>';

print '</div>';
print dol_get_fiche_end();

llxFooter();
$db->close();
?>