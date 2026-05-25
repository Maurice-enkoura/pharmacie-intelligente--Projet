# 💊 Pharmacie Intelligente - Application de Gestion Pharmaceutique

## 📌 Présentation du sujet

**Pharmacie Intelligente** est une application web complète de gestion de pharmacie qui permet aux professionnels de santé de :

- 📦 **Gérer le catalogue de médicaments** : ajout, modification, suppression, recherche, import/export Excel
- 📊 **Gérer les stocks** : suivi des quantités, alertes automatiques (stock bas et péremption proche), entrées de stock par lot
- 💰 **Enregistrer les ventes** : panier d'achat, gestion des ordonnances, multi-paiements (Espèces, Orange Money, Wave, Carte)
- 👥 **Gérer les clients** : fiche client, historique d'achats, suivi des médicaments chroniques
- 🏭 **Gérer les fournisseurs et commandes** : bons de commande, réception avec mise à jour auto du stock
- 📈 **Consulter des rapports** : tableau de bord avec graphiques, chiffre d'affaires, top médicaments, performance des caissiers
- 📧 **Envoyer des factures** : génération PDF et envoi par email

L'application gère **3 types d'utilisateurs** avec des droits différents :
- **Administrateur** : accès complet à toutes les fonctionnalités
- **Pharmacien** : gestion des médicaments, stock, ventes et clients
- **Caissier** : enregistrement des ventes uniquement

---

## 👤 Auteur

**Maurice Enkoura** - Développeur Backend & Frontend

---

## 🛠 Technologies utilisées

| Composant | Technologie |
|-----------|-------------|
| Backend | Laravel 11 + Sanctum |
| Frontend | Vue.js 3 + Tailwind CSS + Pinia |
| Base de données | MySQL |
| API | REST (30+ endpoints) |
| Graphiques | Chart.js |
| PDF | DomPDF |
| Excel | PhpSpreadsheet |

---

## 👥 Comptes de démonstration

| Rôle | Email | Mot de passe | Droits |
|------|-------|--------------|--------|
| **Administrateur** | admin@pharmacie.com | password | Gestion complète |
| **Pharmacien** | pharmacien@pharmacie.com | password | Médicaments, Stock, Ventes, Clients |
| **Caissier** | caissier@pharmacie.com | password | Ventes uniquement |

---

## ⚙️ Fonctionnalités détaillées

### 1. Gestion des médicaments
- Ajouter, modifier, supprimer un médicament
- Rechercher par nom, DCI ou catégorie
- Importer/Exporter des médicaments depuis Excel
- Gérer les formes pharmaceutiques (30+ options)

### 2. Gestion des stocks
- Suivi des quantités en temps réel
- Alertes automatiques : stock < seuil ou péremption < 30 jours
- Entrées de stock par lot (fournisseur, quantité, prix, date péremption)
- Historique des mouvements

### 3. Gestion des ventes
- Panier d'achat dynamique
- Vente avec ou sans ordonnance
- Modes de paiement : Espèces, Orange Money, Wave, Carte
- Calcul automatique de la monnaie
- Génération de facture PDF
- Envoi de facture par email

### 4. Gestion des clients
- Fiche client complète (nom, prénom, téléphone, email, adresse)
- Historique des achats
- Suivi des médicaments chroniques

### 5. Gestion des fournisseurs et commandes
- CRUD fournisseurs
- Création de bons de commande
- Réception de commande avec mise à jour automatique du stock

### 6. Tableau de bord et rapports
- Chiffre d'affaires par période (semaine/mois/année)
- Graphique d'évolution des ventes
- Top médicaments vendus
- Répartition des modes de paiement
- Performance des caissiers
- Alertes stock en temps réel

---

## 🗄️ Base de données

### Tables (10 tables)

| Table | Description |
|-------|-------------|
| users | Utilisateurs (admin, pharmacien, caissier) |
| categories | Catégories de médicaments |
| medicaments | Catalogue des médicaments |
| stock_lots | Lots de stock avec dates de péremption |
| clients | Clients de la pharmacie |
| ventes | Transactions de vente |
| ligne_ventes | Détail des ventes (produits, quantités) |
| fournisseurs | Fournisseurs |
| commandes | Commandes fournisseurs |
| ligne_commandes | Détail des commandes |

### 🔗 Relations

- Categorie (1) → Medicament (n)
- Medicament (1) → StockLot (n)

- Medicament (1) → LigneVente (n)
- LigneVente (n) → Vente (n)
- Vente (n) → Client (1)

- Vente (n) → User (1)

- Medicament (1) → LigneCommande (n)
- LigneCommande (n) → Commande (n)
- Commande (n) → Fournisseur (1)


---

## 🚀 Installation

### Prérequis
- PHP ≥ 8.2
- Composer
- Node.js ≥ 18
- MySQL ≥ 8.0

### Backend (Laravel)

```bash
cd pharmacie-intelligente
composer install
cp .env.example .env
# Configurer la base de données dans .env
php artisan key:generate
php artisan migrate --seed
php artisan serve

cd pharmacie-frontend
npm install
npm run dev

## 🐛 Problèmes rencontrés et solutions

| Problème | Solution |
|----------|----------|
| CORS bloquant les requêtes API | Configuration du proxy dans Vite et ajustement des headers CORS dans Laravel |
| Heure des ventes affichée à 00:00 | Modification de la migration pour utiliser `datetime` au lieu de `date` |
| Stock non mis à jour après vente | Création d'un Observer Laravel pour décrémenter automatiquement le stock |
| Doublons lors de l'import Excel | Vérification par nom + DCI avant insertion en base de données |


## 📦 Migrations + Seeders (données de démo)

---

### 🗄️ Migrations (12 fichiers)

```bash
# Vérifier l'état des migrations
php artisan migrate:status

| Migration                                             | Table                  | Description      |
| ----------------------------------------------------- | ---------------------- | ---------------- |
| 2025_01_01_000001_create_users_table                  | users                  | Utilisateurs     |
| 2025_01_01_000002_create_categories_table             | categories             | Catégories       |
| 2025_01_01_000003_create_medicaments_table            | medicaments            | Médicaments      |
| 2025_01_01_000004_create_clients_table                | clients                | Clients          |
| 2025_01_01_000005_create_ventes_table                 | ventes                 | Ventes           |
| 2025_01_01_000006_create_ligne_ventes_table           | ligne_ventes           | Détail ventes    |
| 2025_01_01_000007_create_fournisseurs_table           | fournisseurs           | Fournisseurs     |
| 2025_01_01_000008_create_commandes_table              | commandes              | Commandes        |
| 2025_01_01_000009_create_ligne_commandes_table        | ligne_commandes        | Détail commandes |
| 2025_01_01_000010_create_stock_lots_table             | stock_lots             | Lots de stock    |
| 2025_01_01_000011_create_personal_access_tokens_table | personal_access_tokens | Tokens Sanctum   |
| 2025_01_01_000012_add_pharmacie_id_to_tables          | diverses               | Multi-pharmacies |


# Exécuter tous les seeders
php artisan db:seed
| Seeder            | Données créées                                |
| ----------------- | --------------------------------------------- |
| RoleSeeder        | 3 utilisateurs (admin, pharmacien, caissier)  |
| CategorieSeeder   | 7 catégories (Antibiotique, Antalgique, etc.) |
| FournisseurSeeder | 4 fournisseurs                                |
| MedicamentSeeder  | 7 médicaments + lots de stock                 |
| ClientSeeder      | 5 clients                                     |
| VenteSeeder       | 4 ventes + lignes de vente                    |
| CommandeSeeder    | 4 commandes + lignes de commande              |


| Table           | Nombre d'enregistrements |
| --------------- | ------------------------ |
| users           | 3                        |
| categories      | 7                        |
| fournisseurs    | 4                        |
| medicaments     | 7                        |
| stock_lots      | 7                        |
| clients         | 5                        |
| ventes          | 4                        |
| ligne_ventes    | 7                        |
| commandes       | 4                        |
| ligne_commandes | 8                        |







## 📘 Documentation API (Postman)

La collection Postman contenant tous les endpoints de l’API est disponible dans :

docs/Pharmacie_API.postman_collection.json

### 📂 Structure de la collection

- Authentification
- Médicaments
- Ventes
- Clients
- Stock
- Fournisseurs
- Commandes
- Rapports
- Utilisateurs

### 🌐 Variables d’environnement

| Variable | Valeur |
|----------|--------|
| base_url | http://127.0.0.1:8000/api/v1 |
| token | Généré automatiquement après login |

### 📥 Import dans Postman

1. Ouvrir Postman
2. Cliquer sur Import
3. Sélectionner :
   docs/Pharmacie_API.postman_collection.json
4. Cliquer sur Import
