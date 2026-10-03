# 🎓 Plateforme Académique ENSI

[![PHP Version]
[![MySQL]
[![JavaScript]
[![CSS]

Une plateforme web moderne et performante conçue pour l'**École Nationale des Sciences de l'Informatique (ENSI)**. Ce projet permet la gestion des ressources pédagogiques, des profils académiques (étudiants et professeurs) et facilite la communication au sein de l'école.

---

##  Fonctionnalités Clés

###  Gestion des Utilisateurs
- **Espaces Dédiés** : Interfaces spécifiques pour les étudiants, les professeurs et les administrateurs.
- **Authentification** : Système de connexion et d'inscription sécurisé (migration vers un stockage local pour la fluidité).
- **Profils Personnalisés** : Gestion des informations personnelles, départements et spécialités.

### Centre de Ressources
- **Dépôt de Documents** : Les professeurs peuvent uploader des cours, TD, TP et devoirs.
- **Téléchargement** : Accès rapide aux supports de cours pour les étudiants.
- **Organisation par Matière** : Classement intuitif par matières et niveaux.

### Vie de l'École
- **Annuaire Académique** : Listes interactives des professeurs et des étudiants.
- **Actualités** : Affichage des derniers événements et annonces de l'école.
- **Contact** : Formulaire de contact intégré avec suivi des messages.

---

## Stack Technique

- **Frontend** : HTML5, CSS3 (Design moderne, Glassmorphism, Responsive), JavaScript Vanilla.
- **Backend** : PHP (Architecture API-first).
- **Base de données** : MySQL.
- **Design** : Google Fonts (Poppins), FontAwesome 6.

---

## Structure du Projet

```text
├── backend/
│   ├── api/            # Endpoints API (auth, resources, professors, etc.)
│   ├── config/         # Configuration DB et constantes
│   ├── database.sql    # Schéma de la base de données
│   ├── includes/       # Fonctions utilitaires et helpers
│   └── uploads/        # Stockage des fichiers de ressources
├── css/                # Feuilles de style (style.css)
├── js/                 # Logique frontend (api.js, main.js)
├── images/             # Assets graphiques
├── data/               # Données statiques additionnelles
└── *.html              # Pages de l'application (index, login, resources, etc.)
```

---

## 🚀 Installation & Configuration

### Prérequis
- Serveur local (XAMPP, WAMP, Laragon ou PHP/MySQL installé nativement).
- Git.

### Étapes
1. **Cloner le projet**
   ```bash
   git clone https://github.com/dallel5-git/projet_web_demo.git
   ```

2. **Configuration de la Base de Données**
   - Importez le fichier `backend/database.sql` dans votre gestionnaire MySQL (phpMyAdmin).
   - La base de données sera créée sous le nom `ensi_db`.

3. **Paramétrage de la Connexion**
   - Modifiez le fichier `backend/config/database.php` si nécessaire (identifiants MySQL).

4. **Lancement**
   - Placez le dossier dans votre `htdocs` ou `www`.
   - Accédez à l'application via `http://localhost/votre-dossier/index.html`.

---

## Identifiants par Défaut (Test)

| Rôle | Email | Mot de passe |
| :--- | :--- | :--- |
| **Administrateur** | `admin@ensi.tn` | `admin` |
| **Professeur** | `a.benali@ensi.tn` | `1234` |
| **Étudiant** | `a.khouaja@ensi.tn` | `1234` |

---

## Licence

Ce projet a été réalisé dans le cadre du module **Projet Web II1** à l'ENSI. Tous droits réservés &copy; 2026.
