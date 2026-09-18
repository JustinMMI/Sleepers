# 🌙💤 Sleepers &mdash; Le Tinder de la Sieste

**Sleepers** est une plateforme de rencontre innovante et décalée conçue pour les adeptes des après-midis calmes et du repos réparateur. Son objectif : **ne plus jamais faire la sieste tout(e) seul(e)**.

Reprenant la mécanique fluide et intuitive de Tinder dans un design moderne, sobre et épuré (*flat design*), Sleepers permet de découvrir des siesteurs compatibles, de swiper, de matcher et d'échanger des avis d'expérience.

---

## ✨ Fonctionnalités Clés

### 🎴 Le Deck de Swipes (Inspiration Tinder)
* **Cartes immersives** : photo en grand format, prénom, âge, description de la literie, type de sommeil (*Grand rêveur* ou *Sommeil profond*), durée de sieste idéale et tags d'habitudes (*Plaid douillet, Bruit de pluie, Tisane, etc.*).
* **Contrôles tactiles et boutons épurés** :
  * **↺ Annuler (Rewind)** : revient sur le dernier profil swipé en cas d'erreur.
  * **✕ Passer (Nope)** : rejette le profil et passe au suivant.
  * **💤 Dormir ensemble (Like)** : manifeste l'envie de partager une sieste.
* **Support tactile et gestuel** : glisser-déposer de la carte avec rotation et tampons visuels (*DORMIR* / *PASSER*).

### 💤 Matchs Réciproques & Avis Partenaires
* **Détection automatique du Match** : lorsqu'un like est réciproque entre deux utilisateurs, un match est instantanément enregistré et célébré par une modale de félicitations.
* **Onglet « Mes Matchs »** :
  * Liste l'ensemble des partenaires avec qui vous avez un match réciproque confirmé.
  * Affiche les avis et commentaires reçus par ce profil.
  * Permet de **déposer un avis certifié** sur votre partenaire de sieste.
  * **Sécurité stricte** : un utilisateur ne peut commenter qu'une personne avec qui il a un match réel validé (refus `403 Forbidden` dans le cas contraire).
* **Absence de messagerie privée** : pour préserver la quiétude de la plateforme, les échanges se font exclusivement sous la forme d'avis d'expérience laissés sur les profils matchés.

### ❤️ Profils Likés & Fonctionnalité Unlike
* **Onglet « Profils Likés »** :
  * Affiche l'historique complet des personnes à qui vous avez envoyé un like.
  * Précise le statut de chaque profil (*Match réciproque* ou *En attente*).
  * **Bouton 💔 Unlike** : permet de retirer un like à tout moment en un clic. Cette action supprime l'enregistrement dans la table des likes et dissout automatiquement le match s'il avait été conclu.

### 📸 Profil Personnalisé & Choix de Photo
* **Gestion de la photo de profil** :
  * Importation de photos depuis votre appareil (formats JPG, PNG, WebP).
  * Sélection parmi une galerie d'avatars de siesteurs (*Zen, Douillet, Rêveur, Calme, Pluvieux, Paisible*).
* **Préférences de sieste sur-mesure** :
  * Description de votre lit / literie (ex. *« Lit King Size, couette en duvet et 4 oreillers moelleux »*).
  * Durée de sieste préférée (*Flash 15 min, Express 30 min, Cycle 1h30, Marathon 2h+*).
  * Typologie de sommeil (*Grand rêveur* / *Sommeil sans rêve*).
  * Ambiance sonore (*Bruit de pluie, silence total, ronronnement de chat, vagues, etc.*).
  * Habitudes et tags personnalisés.

### ⚙️ Panneau d'Administration & Mode Démo
* **Tableau de bord administrateur** (`/views/backend/dashboard.php`) :
  * Statistiques en direct : nombre de membres, likes émis, matchs réciproques et avis publiés.
  * Gestion complète des données (CRUD pour Utilisateurs, Genres, Commentaires/Avis, Likes, Matchs).
* **Sélecteur "Mode Démo"** :
  * Désactivé par défaut pour les utilisateurs.
  * Peut être activé en un clic par l'administrateur depuis le dashboard pour tester facilement le site en switchant entre les différents profils de la base.

---

## 🏗️ Architecture & Technologies

* **Backend** : PHP 8.x (Architecture modulaire MVC légère avec PDO prepared statements natifs).
* **Base de données relationnelle** : MySQL `TINDER22`.
  * `USER` : comptes utilisateurs, identité, âge, biographie, photo.
  * `GENRE` : genres associés.
  * `LIKES` : table de jointure réflexive stockant les likes (`likeL1 = 1`).
  * `MATCHS` : table de jointure réflexive enregistrant les correspondances mutuelles.
  * `COMMENTS` : avis et commentaires déposés entre membres matchés.
* **Données applicatives et profils étendus** :
  * Stockées au format JSON directement dans le dossier `BDD/` (`BDD/sleep_profiles.json` et `BDD/app_settings.json`).
  * Permet d'enrichir l'application sans altérer le schéma SQL de la base de données.
* **Frontend** :
  * Design 100% *Flat Design* épuré (palette sombre mate `#0e1117`, corail `#fe3c72`, indigo nuit `#6366f1`).
  * Plein écran moderne (*Full Viewport Height* `100vh`/`100dvh`).
  * Responsive Mobile First : navigation fluide entre la pile de cartes et le panneau Matchs & Likes.

---

## 📁 Structure du Projet

```text
Sleepers/
├── api/                        # Endpoints d'API backend (JSON / Actions)
│   ├── admin/                  # Actions réservées à l'administrateur
│   │   └── toggle_test_mode.php
│   ├── security/               # APIs sécurisées membres
│   │   ├── add_match_comment.php # Dépôt d'avis sur profil matché
│   │   ├── matched_users.php     # Récupération des matchs et de leurs avis
│   │   ├── liked_users.php       # Liste des profils likés
│   │   ├── unlike.php            # Annulation d'un like et du match
│   │   ├── save_profile.php      # Mise à jour photo et préférences
│   │   ├── swipe.php             # Enregistrement des swipes et détection match
│   │   ├── quick_switch.php      # Switch de compte pour mode démo
│   │   └── login.php / disconnect.php
│   └── bootstrap.php           # Initialisation, fonctions helpers & JSON
├── BDD/                        # Base de données & Stockage
│   ├── CreateDbTinder22.sql    # Schéma SQL de création de la base TINDER22
│   ├── app_settings.json       # Configuration de l'application (ex: mode démo)
│   └── sleep_profiles.json     # Profils étendus de sieste des membres
├── config/                     # Configuration globale et sécurité
│   ├── db.php
│   └── security.php
├── functions/                  # Fonctions métiers & utilitaires
│   ├── sleep_traits.php        # Gestion des traits de sieste JSON
│   └── global.inc.php
├── src/                        # Feuilles de style et médias
│   ├── css/
│   │   └── style.css           # Feuille de style principale Flat Design
│   └── images/
│       └── profiles/           # Dossier de téléversement des photos de profil
├── views/                      # Vues de l'application
│   └── backend/
│       ├── dashboard.php       # Dashboard d'administration
│       ├── security/           # Login, Inscription, Profil & Photo
│       └── [users|genres|comments|likes|matchs]/ # Vues CRUD
├── header.php                  # Balises HTML <head> et dépendances
├── footer.php                  # Scripts JS et fermeture du document
├── index.php                   # Page principale (Application Web plein écran)
└── README.md                   # Documentation du projet
```

---

## 🚀 Installation & Démarrage

### 1. Prérequis
* Un serveur local tel que **WampServer** (ou XAMPP / Laragon) sous Windows.
* **PHP 8.0+** (testé et certifié sur PHP 8.3).
* **MySQL 5.7+** ou **MariaDB 10+**.

### 2. Cloner ou placer le projet
Déposez le dossier du projet dans votre répertoire web :
```text
C:\wamp64\www\Sleepers
```

### 3. Importer la Base de Données
1. Lancez votre serveur MySQL via phpMyAdmin (`http://localhost/phpmyadmin`).
2. Créez la base de données ou exécutez directement le script fourni :
   * Fichier : `BDD/CreateDbTinder22.sql`
   * Base cible : `TINDER22`

### 4. Configuration de l'environnement (`.env`)
À la racine du projet, configurez votre fichier `.env` :
```ini
SQL_HOST=localhost
SQL_USER=root
SQL_PWD=
SQL_DB=TINDER22
ADMIN_PASSWORD_HASH=$2y$10$vN0WwG3k4ZJzYJjXv.aBae6d1cIuUvWw0G8z7e4h2y6q9r3t1y5u
```
*(Le mot de passe administrateur par défaut correspond à : `Admin123!`)*

### 5. Accéder à l'application
Ouvrez votre navigateur sur l'adresse :
```text
http://localhost/Sleepers/
```

---

## 👥 Comptes de Test & Démonstration

Pour explorer immédiatement les fonctionnalités de swipe et de match réciproque :
* **Louise** (ID `#1`) &mdash; Aime les grands lits et les couettes moelleuses.
* **Justine** (ID `#2`) &mdash; Adepte des siestes de 30 minutes au calme.
* **Pierre** (ID `#3`) &mdash; Sieste marathon le dimanche.

*Astuce* : Rendez-vous dans le **Panneau d'administration** (`/views/backend/dashboard.php`), activez le **Sélecteur de Mode Démo**, et basculez en 1 clic d'un membre à l'autre sur la page d'accueil pour tester les interactions.

---

## 🔒 Sécurité & Robustesse

* **Protection contre les injections SQL** : 100% des requêtes utilisent des requêtes préparées PDO avec paramètres typés.
* **Authentification et contrôle d'accès** : vérification des sessions sur toutes les actions modifiantes, contrôle des permissions sur les APIs CRUD (`403 Forbidden` sur accès non autorisé).
* **Intégrité relationnelle** : interdiction absolue de commenter un utilisateur qui ne figure pas dans la liste des matchs réciproques.
* **Régression automatisée** : suite complète de tests end-to-end garantissant la pérennité des fonctionnalités et de la sécurité admin.