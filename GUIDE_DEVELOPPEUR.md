# Guide Développeur - Système de Point de Vente (POS System)

Ce document fournit une vue d'ensemble complète de l'architecture, de la base de données, des fonctionnalités et du workflow du projet POS. Il sert de point de repère pour tout nouveau développeur rejoignant le projet ou souhaitant y contribuer.

---

## 🛠 1. Stack Technique

- **Backend** : Laravel 12.x
- **Frontend** : Laravel Livewire 4.x
- **Styling** : Tailwind CSS 4.x via Vite
- **Base de données** : SQLite (par défaut pour le développement)

---

## 🚀 2. Prérequis & Installation

### Prérequis
- PHP 8.2+
- Composer
- Node.js & NPM
- Extension SQLite pour PHP (ou MySQL/PostgreSQL si modification du `.env`)

### Installation Rapide
1. Installer les dépendances PHP : `composer install`
2. Installer les dépendances NPM : `npm install`
3. Copier le fichier d'environnement : `cp .env.example .env` (sur Windows : `copy .env.example .env`)
4. Générer la clé de l'application : `php artisan key:generate`
5. Créer la base de données SQLite : `touch database/database.sqlite` (ou via l'explorateur Windows)
6. Lancer les migrations : `php artisan migrate`
7. Compiler les assets frontend : `npm run build`
8. Démarrer le serveur local : `php artisan serve` et `npm run dev`

---

## 🔐 3. Gestion des Rôles et Permissions

L'application sécurise l'accès à ses différentes parties via le middleware personnalisé `RoleMiddleware` situé dans `app/Http/Middleware/RoleMiddleware.php`.
Il existe trois rôles distincts :

- **Admin** : Accès logiciel complet. Gère les utilisateurs, le catalogue (produits/catégories) et accède aux rapports de vente.
- **Manager** : Accès limité au back-office. Gère le catalogue et accède aux rapports. Il ne peut pas créer, éditer ou bannir des utilisateurs.
- **Cashier (Caissier)** : Accès exclusif à la section Point de Vente (Terminal). Interagit avec le panier, les encaissements et les sessions de caisse.

---

## 🗄 4. Architecture de la Base de Données

Le modèle de données s'appuie sur Eloquent ORM. L'intégrité du catalogue et des transactions financières est assurée par les modèles suivants (`app/Models/`) :

1. **User**
   - Employés de l'établissement (Admin, Manager, Caissier).
   - Possède un rôle (`role`), un statut actif/inactif (`is_active`) et un code `pin` pour la connexion rapide (Optionnel).
2. **Category**
   - Catégories de classement pour les produits (Ex: Boissons, Plats, Desserts).
3. **Product**
   - Articles vendus, liés à une catégorie (`category_id`).
   - Gère le stock (`stock`) et peut être mis hors ligne (`is_active`). Possède un système de recherche par `barcode` (code-barres).
4. **CashSession (Session de Caisse)**
   - Indispensable pour réaliser des transactions. L'employé déclare un fond de caisse (`opening_amount`) à l'ouverture et un montant final (`closing_amount`) à la fermeture.
   - Suit les totaux par mode de paiement (espèces, cartes, mobile money).
5. **Sale (Vente)**
   - Représente un ticket de caisse validé.
   - Contient le montant total (`total_amount`), la remise (`discount_amount`) et le mode de paiement.
6. **SaleItem (Ligne de Vente)**
   - Représente chaque produit acheté dans le cadre d'une Vente (`Sale`), incluant sa quantité et son prix figé au moment de l'achat.

---

## 🧩 5. Structure des Composants Livewire

L'intégralité de la logique d'interface utilisateur est gérée par Livewire dans le répertoire `app/Livewire/`.

### 🛡 Authentification (`app/Livewire/Auth/`)
- `Login.php` : Authentification standard avec email et mot de passe.
- `CashierLogin.php` : Authentification (potentiellement par PIN) pour un accès plus fluide des caissiers en salle/caisse.

### ⚙️ Administration & Backoffice (`app/Livewire/Admin/`)
- `AdminDashboard.php` : Tableau de bord des métriques (ventes du jour, sessions ouvertes, alertes de stock faible).
- `UserManagement.php` : Interface CRUD pour gérer les employés, leur rôle et leur accès.
- `ProductManagement.php` : Interface CRUD du catalogue et de l'inventaire.
- `CategoryManagement.php` : Architecture des familles de produits.

### 🛒 Point de Vente (POS) (`app/Livewire/Pos/`)
- `SessionManager.php` : Force le caissier à ouvrir une `CashSession` avant d'accéder au terminal. Gère aussi la reddition des comptes en fin de service.
- **`Terminal.php`** : Le composant master de l'application. Fonctionnalités clés :
  - **Panier virtuel** : La variable `$cart` est un tableau persistant en mémoire, aucune requête DB n'est faire avant le paiement.
  - **Filtres** : Recherche instantanée et filtre par catégories pour trouver les produits.
  - **Encaissement sécurisé** : La validation (`completeSale()`) s'exécute entièrement dans une `DB::transaction()` pour assurer qu'aucune donnée ne soit corrompue si une erreur survient (création de la vente, génération des lignes, et décrémentation des stocks des produits).

---

## 🔄 6. Scénario Utilisateur : Parcours Caissier (POS Flow)

Il est important de comprendre le parcours strict implémenté pour l'encaissement :
1. Le caissier saisit ses identifiants.
2. Si le caissier tente d'aller sur `/pos/terminal` sans session ouverte (`Auth::user()->activeSession`), il est bloqué et redirigé vers `/pos/session`.
3. Il inscrit le **Fond de caisse** de départ. La date `opened_at` est enregistrée.
4. Dans le **Terminal**, il ajoute dynamiquement des articles au panier (en gérant les problèmes de stock insuffisant).
5. Au moment de payer, une Modale (fenêtre popup) s'ouvre, lui permettant de définir la méthode de paiement (Cash, Carte...) et la remise potentielle.
6. Lors de la finalisation, la transaction est commitée, et le résumé ou la facture apparaît (`lastSale`).
7. À la fin de la journée, le caissier ferme la session en confirmant le comptage physique des espèces (`closing_amount`). La méthode `recalculateTotals()` regroupe le bilan financier de la journée de ce compte.

---

## 🗺 7. Fichiers Stratégiques et Routage (`routes/web.php`)

Le fichier principal de définition des URL route les utilisateurs en utilisant les middlewares `role:*` :
- **Guest** (`guest`) : `/login`, `/cashier-login`
- **Dashboards & Vues Manager/Admins** (`role:admin,manager`) : `/`, `/reports`, `/products`, `/categories`
- **Gestion des employés** (`role:admin`) : `/users` uniquement accessible aux Administrateurs suprêmes.
- **Terminaux POS** (`role:cashier`) : Groupe de route encapsulé sous `/pos` accédant au terminal et aux sessions de caisse.

---

## 💡 8. Bonnes pratiques de contribution

Lors de vos développements sur ce système :
- Modifiez l'interface grâce à **Tailwind CSS**. Le design système est épuré, n'utilisez pas de Javascript vanilla si Livewire/Alpine.js peut s'en charger côté composant.
- Ne retirez jamais la logique de transaction de base de données (`DB::transaction`) présente dans `Terminal.php` : un plantage serveur pendant une vente entraînerait un paiement enregistré mais aucun stock décrémenté, ou inversement.
- Consultez régulièrement `storage/logs/laravel.log` en développement pour le débogage.

---

## 🖨️ 9. Impression Thermique & QZ Tray

L'application intègre un système d'impression directe (silencieuse) vers les imprimantes thermiques locales via le standard **ESC/POS** en utilisant **QZ Tray**.

### Fonctionnement Technique
1. **Les dépendances JS** (`qz-tray.min.js` et `sha256.min.js`) sont chargées localement depuis le dossier `public/js/` afin de ne pas être bloquées par les bloqueurs de publicités ou les protections anti-pistage des navigateurs stricts (Brave, Firefox Strict, Edge). (Elles sont injectées dans `pos.blade.php`).
2. **Dispatch Livewire** : Dans `Terminal.php`, lorsque la méthode `completeSale()` s'exécute avec succès, ou quand l'utilisateur clique sur le bouton de réimpression (`reprintReceipt()`), un événement nommé `print-receipt` est dispatché au navigateur contenant le "payload" formaté du ticket.
3. **Le Listener JavaScript** (situé à la fin de `terminal.blade.php`) capte cet événement.
4. **Connexion & Envoi** : JavaScript se connecte localement au service QZ Tray web-socket (`wss://localhost:8181`), trouve l'imprimante système par défaut, et lui envoie un flux de commandes hexadécimales **ESC/POS** pures.
   - Initialisation (`\x1B\x40`)
   - Centrage texte, formatage (Gros, etc.)
   - Itération sur les produits
   - Commandes finales : Coupe du papier thermique (`\x1D\x56\x41\x10`) et Ouverture impulsionnelle du tiroir-caisse connecté (`\x1B\x70\x00\x19\xFA`).

### Maintenance
Si un modèle d'imprimante ne coupe pas le papier ou n'ouvre pas le tiroir, il faut généralement ajuster les codes hexadécimaux à la fin du listener JavaScript dans `terminal.blade.php`, car certaines marques chinoises non-Epson utilisent des pins de déclenchement différents pour le RJ11 du tiroir-caisse.