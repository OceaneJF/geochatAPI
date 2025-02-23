# Geochat

1. Cliquez sur le bouton "Fork" en haut de ce dépôt
2. Clonez votre dépôt
3. Lancez: `symfony composer install`
5. Éditez le fichier `.env.local` pour y placer votre [chaîne de connexion](https://gregwar.com/bdd/)
6. Lancez `symfony console doctrine:schema:update --force` pour mettre à jour la base
7. Lancez `symfony console doctrine:fixtures:load` pour charger les données en base
8. Lancez `symfony serve` pour démarrer le serveur, et rendez-vous sur [http://127.0.0.1:8000](http://127.0.0.1:8000).
