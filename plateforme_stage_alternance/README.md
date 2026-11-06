# StageConnect — MVP PHP / MySQL

Plateforme pour étudiants, recruteurs et administrateurs. Application PHP 8.1+ avec MySQL 8+ (ou MariaDB compatible), PDO, Apache avec `AllowOverride All` pour appliquer les fichiers `.htaccess`.

## Installation Windows (XAMPP)

1. Installer XAMPP, démarrer **Apache** et **MySQL** dans le panneau XAMPP.
2. Copier **le dossier `stageconnect`** (contenu de cette archive) dans `C:\xampp\htdocs\stageconnect`.
3. Ouvrir `http://localhost/phpmyadmin`, sélectionner « Importer » et importer `database.sql`.
4. Adapter les identifiants MySQL dans `config/config.php` (par défaut XAMPP local : `root` sans mot de passe). `BASE_URL` doit correspondre au nom du dossier dans `htdocs`.
5. En invite de commandes, créer le compte administrateur **directement dans la base**, sans page d'inscription publique :
   ```bat
   cd C:\xampp\htdocs\stageconnect
   C:\xampp\php\php.exe scripts\create_admin.php admin@exemple.fr "UnMotDePasseLongEtUnique!" "Administrateur"
   ```
6. Ouvrir `http://localhost/stageconnect/`.
7. Créer un compte étudiant et un compte recruteur depuis la page d'inscription, puis tester la chaîne complète.

## Structure
- `database.sql` : 7 tables et leurs relations
- `includes/` : connexion PDO, sessions, rôles, protection CSRF, mise en page
- `student/` : profil, recherche d'offres, candidature, suivi, notifications
- `recruiter/` : entreprise, création/modification d'offres, examen et décision des candidatures
- `admin/` : vue globale, utilisateurs et journal des activités
- `download_cv.php` : téléchargement de CV contrôlé selon les autorisations
- `scripts/create_admin.php` : création du compte admin depuis le terminal, insertion avec hachage du mot de passe

## Modèle de données
```
users (1) ── (0..1) student_profiles
users (1) ── (0..1) recruiter_profiles
users [recruteur] (1) ── (*) offers
users [étudiant] (1) ── (*) applications (*) ── (1) offers
users (1) ── (*) notifications
users (0..1) ── (*) activity_logs
```

## Important
C'est un **MVP pédagogique** et non une solution prête à exposer sur Internet. Ajouter avant une mise en production : HTTPS, configuration des secrets via variables d'environnement hors du répertoire web, SMTP / e-mails, récupération de mot de passe, validation d'adresse e-mail, limitation des tentatives de connexion, contrôle d'accès plus fin, politique de confidentialité / RGPD, sauvegardes et tests. Les notifications actuelles sont **internes au site**, sans e-mail ni programmation d'entretien. Les journaux répertorient les actions instrumentées, **pas chaque consultation HTTP**.

Pour la sécurité des CV, les fichiers sont stockés avec des noms aléatoires, les téléchargements sont autorisés uniquement aux propriétaires, aux administrateurs et aux recruteurs ayant reçu une candidature ; le répertoire est bloqué par `.htaccess`. Vérifier que la directive Apache `AllowOverride All` est active, ou déplacer les CV dans un stockage privé hors du webroot.

L'installation nécessite PHP avec les extensions `pdo_mysql`, `mbstring`, `fileinfo` et les sessions activées.

## Interface (version redesign)
Design responsive moderne en CSS autonome dans `assets/style.css`, menu mobile accessible dans `assets/app.js`, page d’accueil revisitée, formulaires et tableaux de bord stylisés. Aucune dépendance JavaScript externe nécessaire. Les polices Google sont facultatives et remplacées automatiquement par les polices système si hors connexion.
