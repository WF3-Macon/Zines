# Zines

Cloner le projet et executer les lignes de commandes suivantes :

> Bien se placer dans le dossier du projet avant d'utiliser ces commandes !

```bash
composer install
```

```bash
symfony console doctrine:database:create
symfony console make:migration
symfony console doctrine:migration:migrate
symfony console doctrine:fixtures:load
```

```bash
npm install
npm run build
```
