# 💊 Pharmacie Intelligente - Application de Gestion Pharmaceutique

## 📋 Présentation du projet

**Pharmacie Intelligente** est une application web complète de gestion de pharmacie développée dans le cadre du module **Technologies Web Backend** (Licence 3 - ISI Groupe, Année académique 2025-2026) sous l'encadrement de **Robert DIASSÉ**.

L'application permet la gestion complète d'une pharmacie : catalogue de médicaments, gestion des stocks avec alertes automatiques, ventes avec ou sans ordonnance, gestion des clients, fournisseurs et commandes, ainsi que des rapports détaillés.

---

## 👥 Acteurs et leurs rôles

| Rôle | Droits | Identifiants |
|------|--------|--------------|
| **Admin** | Gestion globale : catalogue, stocks, fournisseurs, commandes, rapports, utilisateurs | admin@pharmacie.com / password |
| **Pharmacien** | Gestion : médicaments, stocks, ventes, clients | pharmacien@pharmacie.com / password |
| **Caissier** | Enregistrement des ventes uniquement + consultation clients | caissier@pharmacie.com / password |

### Détail des permissions

| Fonctionnalité | Admin | Pharmacien | Caissier |
|----------------|-------|------------|----------|
| Dashboard | ✅ | ❌ | ❌ |
| Médicaments (CRUD) | ✅ | ✅ | ❌ |
| Stock (alertes, entrées) | ✅ | ✅ | ❌ |
| Ventes (consultation) | ✅ (toutes) | ✅ (toutes) | ✅ (ses ventes) |
| Ventes (création) | ✅ | ✅ | ✅ |
| Clients | ✅ | ✅ (création) | ✅ (lecture) |
| Fournisseurs | ✅ | ❌ | ❌ |
| Commandes | ✅ | ❌ | ❌ |
| Rapports | ✅ | ❌ | ❌ |
| Utilisateurs | ✅ | ❌ | ❌ |

---

## ⚙️ Fonctionnalités détaillées

### 1. Gestion des Médicaments & Stock
- Catalogue complet (nom, DCI, forme, dosage, catégorie)
- Stock avec seuil d'alerte et date de péremption
- **Alerte automatique** : stock < seuil OU péremption < 30 jours
- Entrées de stock par lot (fournisseur, date, quantité, prix achat)
- Soft delete pour l'archivage des médicaments

### 2. Gestion des Ventes & Ordonnances
- Vente avec ou sans ordonnance
- Enregistrement des médicaments d'une ordonnance
- Reçu de vente avec réduction automatique du stock (Observer)

### 3. Gestion des Clients
- Fiche client complète (coordonnées, historique achats)
- Suivi des médicaments chroniques

### 4. Gestion des Fournisseurs & Commandes
- Fiche fournisseur complète
- Bon de commande
- Réception de commande avec mise à jour automatique du stock

### 5. Reporting & Dashboard
- Chiffre d'affaires par période (semaine/mois/année)
- Top médicaments vendus
- Produits en rupture ou bientôt périmés
- Graphiques d'évolution des ventes
- Export Excel

---

## 🗄️ Modèles (Models) et Relations

### Liste des modèles

| Modèle | Description | Champs principaux |
|--------|-------------|-------------------|
| **User** | Utilisateurs | name, email, password, role |
| **Medicament** | Médicaments | nom, dci, forme, dosage, prix_vente, stock_actuel, seuil_alerte |
| **Categorie** | Catégories | nom, description |
| **StockLot** | Lots de stock | lot_number, quantite_initiale, quantite_restante, date_peremption |
| **Vente** | Ventes | numero_facture, montant_total, mode_paiement, type_vente |
| **LigneVente** | Détail des ventes | quantite, prix_unitaire, sous_total |
| **Client** | Clients | nom, prenom, telephone, email, adresse |
| **Fournisseur** | Fournisseurs | nom, contact, telephone, email, adresse |
| **Commande** | Commandes | numero_commande, statut, montant_total |
| **LigneCommande** | Détail des commandes | quantite_commandee, quantite_recue, prix_unitaire |

### Diagramme des relations
Categorie (1) ──────< (n) Medicament
Medicament (1) ──────< (n) StockLot
Medicament (1) ──────< (n) LigneVente ──────< (n) Vente (n) ────── (1) Client
Medicament (1) ──────< (n) LigneCommande ──────< (n) Commande (n) ────── (1) Fournisseur   
User (1) ──────< (n) Vente 