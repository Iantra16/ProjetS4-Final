## etape 1
1. modifier le config/database.php sur le nom.db avec le nom de la base
2. s'assurer que .env contient developpement
3. tester avec

```bash
php spark serve
```
## etape 2
4. Migrations
- cree les migrations
```bash
php spark make:migration CreateUsers
php spark make:migration CreateProduits
```
5. remplir ces fichiers 
6. lancer la migration
```bash
php spark migrate
```
7. Seeder
- cree les seeders
```bash
php spark make:seeder DemoSeeder
```
- lancer le seeder
```bash
php spark db:seed DemoSeeder
```
8. le monprojet.db est generer automatiquement

## fonctionnalites generales
- authentification 
- back / front office
- pagination
- recherche / filtre
- export pdf / csv
- import csv

