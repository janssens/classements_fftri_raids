# classements_fftri_raids

Outil pour calculer et afficher les classements FFTRI raid 
à partir des licences et des résultats chargés dans l'application.

running at : https://classements.raidsaventure.fr/championship/

## Requis
* PHP (version 7+)
* [Composer](https://getcomposer.org/)
* Mysql (ou mariadb)
* php-mysql (ou php-pdo_mysql)

## Installation

``git clone https://github.com/janssens/classements_fftri_raids``

``cd classements_fftri_raids/``

``composer install ``

Edit ``.env`` to setup the database connexion information

``php bin/console doctrine:database:create``

``php bin/console doctrine:migrations:migrate``

## Load data

### Athletes data

``php bin/console app:import:registrations PATH/TO/FILE.csv``

In case too heavy to handle (out of memory error)

``for VAR in {0..58000..1000}; do php bin/console app:import:registrations PATH/TO/FILE.csv --map "2,3,4,5,6,17,30,31,32,33,37,38" -l 1000 -s $VAR; done``
``for VAR in {0..59000..500}; do docker-compose exec php php bin/console app:import:registrations ./data/2022/licences-2022.csv --map 1,2,3,5,6,18,27,29,30,31,35,36  -l 500 -s $VAR; done``

### Race data

``php bin/console app:import:race_ranking PATH/TO/FILE.csv``

### Usefull SQL

get all firstname,lastname and email of racers

``SELECT a.firstname, a.lastname , a.email FROM `view_racer` AS r JOIN athlete AS a ON r.parent_id = a.id WHERE outsider = 0``


new calc


point = min((nb_of_team > 150) ? 150 : nb_of_team ) - (rank - 1) , 1) 