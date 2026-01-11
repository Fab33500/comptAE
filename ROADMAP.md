# Module ComptAE - Gestion Auto-Entrepreneur pour Dolibarr

Ce module permet de gérer la comptabilité spécifique des Auto-Entrepreneurs (Micro-Entreprises) directement dans Dolibarr. Il offre un tableau de bord simplifié, le suivi des seuils de TVA et le calcul des cotisations URSSAF.

---

## 📅 Roadmap d'Implémentation

### Phase 1 : Structure & Configuration (✅ Terminé)
**Objectif :** Mettre en place l'architecture du module, les menus et la configuration des règles métiers.

* [x] **Génération du Module :** Création de l'arborescence standard (`core`, `admin`, `sql`, etc.).
* [x] **Définition des Menus :**
    * Tableau de bord (Accueil).
    * Livre des Recettes.
    * Livre des Dépenses.
    * URSSAF (Paiement & Config).
* [x] **Gestion des Droits (Permissions) :**
    * `read` : Consulter le tableau de bord et les livres.
    * `write` : Configurer les taux et enregistrer les paiements URSSAF.
* [x] **Page de Configuration Avancée (`urssaf_taux.php`) :**
    * Saisie des taux de cotisations (Vente, Service BIC, BNC).
    * Gestion des taxes annexes (CFP, Libératoire).
    * **Gestion des Seuils TVA :** Configuration du double seuil (Global 101k€(peut etre modifié) / Service 39k€) pour les activités mixtes.
    * Sauvegarde des paramètres en base de données (`llx_const`).

---

### Phase 2 : Connexion des Données / Backend (🚧 En cours - Priorité 1)
**Objectif :** Remplacer les données fictives du tableau de bord par les données réelles de Dolibarr.

* [ ] **Requêtes SQL - Recettes :**
    * Interroger la table `llx_paiement` pour récupérer les encaissements réels.
    * **Défi technique :** Distinguer "Vente" et "Service" en croisant avec les lignes de factures (`llx_facturedet`) et le type de produit (`product_type`).
* [ ] **Requêtes SQL - Dépenses :**
    * Interroger les factures fournisseurs (`llx_facture_fourn`) et/ou les notes de frais.
* [ ] **Calcul Dynamique URSSAF :**
    * Appliquer les taux configurés en Phase 1 sur les montants réels récupérés.
* [ ] **Mise à jour du Tableau de Bord (`comptaeindex.php`) :**
    * Remplacer les tableaux PHP statiques (`$recettes_ventes = array(...)`) par les résultats des requêtes SQL.

---

### Phase 3 : Pages Fonctionnelles "Livres" (❌ À faire)
**Objectif :** Rendre les pages de listes consultables pour répondre aux obligations légales (Livre chronologique).

* [ ] **Page `recettes.php` :**
    * Afficher un tableau HTML listant les encaissements.
    * Colonnes : Date, Tiers, N° Facture, Montant, Mode de règlement, Ventilation (Vente/Service).
    * Ajouter un système de pagination et de filtres (par mois/année).
* [ ] **Page `depenses.php` :**
    * Afficher la liste des achats et frais.
    * Colonnes : Date, Fournisseur, Description, Montant, Type.

---

### Phase 4 : Gestion des Paiements URSSAF (❌ À faire)
**Objectif :** Pouvoir déclarer qu'une période a été payée pour que le "C.A. Net" soit juste.

* [ ] **Création Table SQL :** Créer une table `llx_comptae_urssaf` (via `sql/llx_comptae_urssaf.sql` ou l'onglet Objets du Builder) pour stocker :
    * `date_period` (Période concernée, ex: 2025-01).
    * `amount` (Montant payé).
    * `date_payment` (Date du versement).
* [ ] **Interface de saisie (`urssaf.php`) :**
    * Ajouter un formulaire pour valider un paiement trimestriel ou mensuel.
* [ ] **Historique :** Afficher la liste des déclarations passées.

---

### Phase 5 : Finalisation & Packaging (❌ À faire)
**Objectif :** Rendre le module propre et distribuable.

* [ ] **Internationalisation (i18n) :**
    * Remplacer tous les textes "en dur" dans le code PHP par des clés de langue (`$langs->trans("MyKey")`).
    * Compléter les fichiers `langs/fr_FR/comptae.lang` et `en_US`.
* [ ] **Nettoyage du code :** Retirer les commentaires de debug et les mocks.
* [ ] **Packaging :** Générer le fichier `.zip` via l'outil de build de Dolibarr pour l'installation sur d'autres instances.

---

## 🛠 Installation

1.  Dézipper le dossier `comptae` dans le répertoire `/custom` de votre Dolibarr.
2.  Activer le module dans **Accueil > Configuration > Modules**.
3.  Vérifier que les tables SQL se sont créées (si applicable en Phase 4).
4.  Configurer vos taux et seuils dans le menu **ComptAE > URSSAF > Configuration Taux**.
5.  Accorder les permissions à votre utilisateur.