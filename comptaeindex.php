<?php
/**
 * \file    comptae/comptaeindex.php
 * \ingroup comptae
 * \brief   Tableau de bord ComptAE
 */

// 1. Chargement de l'environnement
$res = 0;
if (! $res && file_exists("../main.inc.php")) $res = @include "../main.inc.php";
if (! $res && file_exists("../../main.inc.php")) $res = @include "../../main.inc.php";
if (! $res && file_exists("../../../main.inc.php")) $res = @include "../../../main.inc.php";
if (! $res) die("Include of main fails");

require_once DOL_DOCUMENT_ROOT . '/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT . '/custom/comptae/lib/comptae.lib.php';

$langs->load("comptae@comptae");
$langs->load("bills");

// 2. Sécurité
if (!$user->rights->comptae->read) accessforbidden();

$title = $langs->trans("Tableau de bord AE");

// 3. EN-TETE AVEC CHARGEMENT CHART.JS
llxHeader("", $title, '', '', 0, 0, array('/includes/chart.js/dist/Chart.min.js'));

// 4. Préparation des données (MOCKUP)
// -------------------------------------------------------------------------------

// Taux récupérés de la config (ou défaut)
$taux_vente   = getDolGlobalString('COMPTAE_TAUX_VENTE', '12.30') / 100;
$taux_service = getDolGlobalString('COMPTAE_TAUX_SERVICE_BIC', '21.20') / 100;

// Données fictives (Pour test)
$recettes_ventes   = array(850, 1200, 450, 1600, 1100, 950, 100, 0, 0, 0, 0, 0); 
$recettes_services = array(400, 600, 350, 800, 500, 1100, 200, 0, 0, 0, 0, 0); 
$mes_depenses      = array(250, 180, 420, 150, 320, 280, 50, 0, 0, 0, 0, 0);

$mes_recettes_totales = array();
$mon_urssaf           = array();

for($i=0; $i<12; $i++) {
    $r_v = $recettes_ventes[$i];
    $r_s = $recettes_services[$i];
    $mes_recettes_totales[$i] = $r_v + $r_s;
    $mon_urssaf[$i] = round(($r_v * $taux_vente) + ($r_s * $taux_service));
}

$total_ventes_annuel   = array_sum($recettes_ventes);
$total_services_annuel = array_sum($recettes_services);
$total_recettes_annuel = $total_ventes_annuel + $total_services_annuel;
$total_depenses_annuel = array_sum($mes_depenses);
$total_urssaf_annuel   = array_sum($mon_urssaf);
$ca_net                = $total_recettes_annuel - $total_depenses_annuel - $total_urssaf_annuel;

// --- GESTION DES SEUILS (DOUBLE JAUGE) ---
// Récupération des deux seuils
$seuil_vente   = getDolGlobalString('COMPTAE_SEUIL_TVA_VENTE', '101000'); // Plafond Global
$seuil_service = getDolGlobalString('COMPTAE_SEUIL_TVA_SERVICE', '39100'); // Plafond Service

// Calcul Jauge 1 : Global (Tout cumulé vs Seuil Vente)
$percent_global  = ($seuil_vente > 0) ? round(($total_recettes_annuel / $seuil_vente) * 100) : 0;

// Calcul Jauge 2 : Service (Service uniquement vs Seuil Service)
$percent_service = ($seuil_service > 0) ? round(($total_services_annuel / $seuil_service) * 100) : 0;
// -------------------------------------------------------------------------------


print load_fiche_titre($title, '', 'object_home');

print '<div class="fichecenter">';

// KPI
print '<div class="underbanner clearboth"></div>';
print '<div class="div-table-responsive-no-min">';
print '<table class="noborder centpercent userlists">';
print '<tr class="liste_titre">
        <td class="center">Recettes Totales (Année)</td>
        <td class="center">Dépenses</td>
        <td class="center">URSSAF (Est.)</td>
        <td class="center"><b>C.A. Net</b></td>
       </tr>';
print '<tr class="oddeven" style="font-size: 1.4em; font-weight: bold;">';
print '<td class="center text-success">'.price($total_recettes_annuel).'</td>';
print '<td class="center text-warning">'.price($total_depenses_annuel).'</td>';
print '<td class="center" style="color: #d35400;">'.price($total_urssaf_annuel).'</td>'; 
print '<td class="center text-primary">'.price($ca_net).'</td>';
print '</tr>';
print '<tr class="oddeven"><td colspan="4" class="center small opacitymedium">';
print 'Dont Ventes: '.price($total_ventes_annuel).' | Dont Services: '.price($total_services_annuel);
print '</td></tr>';
print '</table>';
print '</div><br>';


print '<div class="fichethirdleft">';
// COLONNE GAUCHE

    // --- WIDGET 1 : DOUBLE JAUGE SEUILS ---
    print '<table class="noborder centpercent">';
    print '<tr class="liste_titre"><th colspan="2">Seuils Franchise TVA</th></tr>';
    print '<tr><td colspan="2">';
    
    // JAUGE 1 : GLOBAL
    $color_bar = ($percent_global > 70) ? ($percent_global >= 100 ? 'progress-bar-danger' : 'progress-bar-warning') : 'progress-bar-success';
    print '<div class="small"><b>Global</b> (Plafond '.price($seuil_vente, 0, '', 1, -1, -1, $conf->currency).')</div>';
    print '<div class="progress" style="height: 18px; margin-bottom: 2px;">';
    print '<div class="progress-bar '.$color_bar.'" role="progressbar" style="width: '.$percent_global.'%; line-height: 18px;">'.$percent_global.'%</div>';
    print '</div>';
    print '<div class="right small opacitymedium" style="margin-bottom: 8px;">'.price($total_recettes_annuel).' / '.price($seuil_vente).'</div>';

    // JAUGE 2 : SERVICE
    $color_bar_s = ($percent_service > 70) ? ($percent_service >= 100 ? 'progress-bar-danger' : 'progress-bar-warning') : 'progress-bar-info'; // Bleu par défaut pour distinguer
    print '<div class="small"><b>Dont Services</b> (Plafond '.price($seuil_service, 0, '', 1, -1, -1, $conf->currency).')</div>';
    print '<div class="progress" style="height: 18px; margin-bottom: 2px;">';
    print '<div class="progress-bar '.$color_bar_s.'" role="progressbar" style="width: '.$percent_service.'%; line-height: 18px;">'.$percent_service.'%</div>';
    print '</div>';
    print '<div class="right small opacitymedium">'.price($total_services_annuel).' / '.price($seuil_service).'</div>';
    
    print '</td></tr>';
    print '</table><br>';

    // --- WIDGET 2 : Camembert Répartition ---
    print '<table class="noborder centpercent">';
    print '<tr class="liste_titre"><th>Répartition Annuelle</th></tr>';
    print '<tr><td class="center">';
    print '<div style="position: relative; height:180px; width:100%;">'; 
    ?>
    <canvas id="repartitionChart"></canvas>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function(event) {
            if (typeof Chart === 'undefined') return;
            var ctxPie = document.getElementById('repartitionChart').getContext('2d');
            new Chart(ctxPie, {
                type: 'doughnut',
                data: {
                    labels: ['C.A. Net', 'Dépenses', 'URSSAF'],
                    datasets: [{
                        data: [<?php echo $ca_net; ?>, <?php echo $total_depenses_annuel; ?>, <?php echo $total_urssaf_annuel; ?>],
                        backgroundColor: ['#3498db', '#f1c40f', '#e67e22'],
                        borderWidth: 1
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { boxWidth: 12 } } } }
            });
        });
    </script>
    <?php
    print '</td></tr>';
    print '</table><br>';

    // --- WIDGET 3 : Graphique URSSAF ---
    print '<table class="noborder centpercent">';
    print '<tr class="liste_titre"><th>Paiements URSSAF (Mensuel)</th></tr>';
    print '<tr><td class="center">';
    print '<div style="position: relative; height:140px; width:100%;">'; 
    ?>
    <canvas id="urssafChart"></canvas>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function(event) {
            if (typeof Chart === 'undefined') return;
            var ctx = document.getElementById('urssafChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['J','F','M','A','M','J','J','A','S','O','N','D'],
                    datasets: [{
                        label: 'Cotisations',
                        data: [<?php echo implode(',', $mon_urssaf); ?>],
                        backgroundColor: '#e67e22',
                        borderColor: '#d35400',
                        borderWidth: 1
                    }]
                },
                options: { 
                    responsive: true, maintainAspectRatio: false,
                    scales: { x: { ticks: { font: { size: 10 } } }, y: { beginAtZero: true, ticks: { display: false } } },
                    plugins: { legend: { display: false } } 
                }
            });
        });
    </script>
    <?php
    print '</td></tr>';
    print '</table>';


print '</div><div class="fichetwothirdright">';
// COLONNE DROITE

    // --- WIDGET 4 : Evolution Complète ---
    print '<table class="noborder centpercent">';
    print '<tr class="liste_titre"><th>Evolution de votre Activité (Ventes vs Services vs Charges)</th></tr>';
    print '<tr><td class="center">';
    ?>
    <div style="position: relative; height:400px; width:100%;">
        <canvas id="mainChart"></canvas>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function(event) {
            if (typeof Chart === 'undefined') return;
            var ctx2 = document.getElementById('mainChart').getContext('2d');
            new Chart(ctx2, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Fev', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aout', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [
                        { label: 'Ventes', data: [<?php echo implode(',', $recettes_ventes); ?>], borderColor: '#2ecc71', backgroundColor: 'rgba(46, 204, 113, 0.1)', fill: true, tension: 0.3 },
                        { label: 'Services', data: [<?php echo implode(',', $recettes_services); ?>], borderColor: '#1abc9c', backgroundColor: 'rgba(26, 188, 156, 0.1)', fill: true, tension: 0.3 },
                        { label: 'Dépenses', data: [<?php echo implode(',', $mes_depenses); ?>], borderColor: '#f1c40f', borderDash: [5, 5], pointStyle: 'rectRot', fill: false, tension: 0.3 }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: false, interaction: { mode: 'index', intersect: false }, scales: { y: { beginAtZero: true } } }
            });
        });
    </script>
    <?php
    print '</td></tr>';
    print '</table>';

print '</div></div>';

llxFooter();
$db->close();
?>