# StageConnect avec Docker Desktop — Windows

Ce projet exécute **Apache + PHP 8.3**, **MySQL 8.4** et, en option, **phpMyAdmin**. Le CSS est inclus dans l'image PHP : aucune installation XAMPP n'est nécessaire. Les données MySQL et CV sont enregistrés dans deux volumes persistants.

## Démarrage

1. Installer **Docker Desktop**, le démarrer et utiliser des conteneurs Linux.
2. Ouvrir PowerShell dans ce dossier (celui qui contient `compose.yaml`).
3. Créer votre fichier d'environnement :

   ```powershell
   Copy-Item .env.example .env
   notepad .env
   ```

   **Changer les deux mots de passe** `MYSQL_PASSWORD` et `MYSQL_ROOT_PASSWORD` avant de démarrer. Ne pas publier `.env` sur Git.
4. Construire et démarrer :

   ```powershell
   docker compose up -d --build
   docker compose ps
   ```

5. Ouvrir **http://localhost:8080**. Le CSS se trouve à **http://localhost:8080/assets/style.css**.

Le script `database.sql` est importé automatiquement par MySQL **uniquement lors de la création du volume de données vide**. Ne pas réimporter une deuxième fois dans la même base.

## Compte administrateur

Le compte admin n'est pas inscrit depuis l'interface. Créer un administrateur par commande interactive, en entrant le mot de passe dans le terminal sans l'enregistrer dans l'historique :

```powershell
docker compose exec web bash
```

Puis **à l'intérieur du conteneur** :

```bash
read -r -s -p 'Mot de passe administrateur (12 caractères minimum) : ' ADMIN_PASS; echo
php scripts/create_admin.php admin@exemple.fr "$ADMIN_PASS" "Administrateur"
unset ADMIN_PASS
exit
```

Changer `admin@exemple.fr` par votre adresse e-mail. Vous pouvez ensuite vous connecter via la page de connexion de StageConnect.

## phpMyAdmin (optionnel)

```powershell
docker compose --profile tools up -d
```

Ouvrir **http://localhost:8081**. Serveur `db`, utilisateur `stageconnect`, mot de passe `MYSQL_PASSWORD` du fichier `.env`. La base est initialisée automatiquement ; l'import manuel est inutile.

## Commandes utiles

```powershell
docker compose logs -f web
docker compose logs -f db
docker compose down
```

`docker compose down` conserve la base et les CV. **Attention :** `docker compose down -v` supprime définitivement les volumes MySQL et les CV. Faire une sauvegarde avant.

Après avoir modifié le code PHP ou le CSS, relancer :

```powershell
docker compose up -d --build
```

## Points importants

- Le projet est accessible **uniquement depuis le PC local** par défaut (ports liés à `127.0.0.1`). Pour le rendre accessible depuis Internet, il faut une infrastructure supplémentaire : domaine, HTTPS, reverse proxy, durcissement, sauvegardes et contrôles de sécurité.
- Si le port `8080` est occupé, changer `APP_PORT=8082` dans `.env` et utiliser `http://localhost:8082`.
- Si MySQL refuse les identifiants après un changement de `.env`, les identifiants existants ont été créés avec l'ancien volume : la modification de `.env` **ne modifie pas** les comptes MySQL déjà créés.
- `database.sql` importe une base appelée `stageconnect`. Conserver `MYSQL_DATABASE=stageconnect` pour cette version.
- Le code PHP lit les paramètres MySQL depuis les variables d'environnement ; inutile de modifier `config/config.php`.
- Cette application reste un prototype pédagogique et n'est **pas prête pour une mise en production publique** en l'état.
