# POS-SYS - Système de Point de Vente Laravel Livewire

Un système de Point de Vente (POS) moderne, rapide et réactif construit avec Laravel, Livewire et Tailwind CSS.

## 🌟 Fonctionnalités Principales

- **Terminal de Caisse Fluide :** Interface intuitive pour la prise de commande avec recherche par nom et scanner de codes-barres intégré.
- **Gestion des Sessions de Caisse :** Ouverture et fermeture de caisse obligatoires avec suivi des montants initiaux et finaux.
- **Impression Thermique Directe (ESC/POS) :** Intégration de QZ Tray pour une impression silencieuse, instantanée et sans boîte de dialogue sur les imprimantes thermiques locales. Ouvre automatiquement le tiroir-caisse.
- **Gestion Avancée des Stocks :** Décrémentation automatique lors des ventes et empêchement de vendre des produits hors stock.
- **Génération de Codes-Barres :** Création automatique et impression (SVG) de codes EAN-13 uniques pour chaque produit.
- **Tableau de Bord et Rapports :** Suivi des ventes en temps réel, par mode de paiement (Espèces, Carte, Mobile Money) et par employé.
- **Système de Rôles :** Accès sécurisé différencié pour les Administrateurs, les Managers et les Caissiers (avec connexion rapide par code PIN).

## 🚀 Installation & Prérequis

### Prérequis
- PHP 8.2 ou supérieur
- Node.js & NPM
- Composer
- QZ Tray (Optionnel, requis uniquement pour l'impression thermique directe)

### Étapes d'installation

1. **Cloner le projet ou extraire les sources**
2. **Installer les dépendances PHP :**
   ```bash
   composer install
   ```
3. **Installer les dépendances Frontend :**
   ```bash
   npm install
   ```
4. **Configuration de l'environnement :**
   - Copiez le fichier d'exemple : `cp .env.example .env` (ou `copy .env.example .env` sur Windows).
   - Générez la clé d'application : `php artisan key:generate`.
   - Configurez votre base de données dans le fichier `.env` (par défaut SQLite).
5. **Base de données :**
   - Créez le fichier SQLite (optionnel) : `touch database/database.sqlite`
   - Lancez les migrations (et les seeders si nécessaire) : `php artisan migrate`
6. **Compiler les ressources :**
   ```bash
   npm run build
   ```
7. **Lancer l'application :**
   ```bash
   php artisan serve
   # Dans un autre terminal :
   npm run dev
   ```

## 🖨️ Configuration de l'Impression de Caisse (QZ Tray)

Pour que les tickets s'impriment automatiquement sur l'imprimante thermique de la caisse :

1. Téléchargez et installez **[QZ Tray](https://qz.io/download/)** sur l'ordinateur qui servira de caisse.
2. Branchez et installez les pilotes de votre imprimante thermique (ex: Epson TM-T20, Xprinter, etc.).
3. Définissez cette imprimante comme **Imprimante par défaut** dans les paramètres de votre système d'exploitation.
4. Lancez QZ Tray (il doit tourner en arrière-plan).
5. Lors de la première vente sur le POS, acceptez la demande de connexion de votre navigateur en cochant **"Always Allow"** (Toujours Autoriser).
6. Le tiroir-caisse s'ouvrira et le ticket sortira instantanément à chaque validation de commande ou réimpression !

## 📚 Guide Développeur

Un guide détaillé de l'architecture, du workflow et de la structure de la base de données est disponible dans le fichier `GUIDE_DEVELOPPEUR.md`.

## 🛡️ Licence
Ce projet est propriétaire. Réservé à l'usage interne.
