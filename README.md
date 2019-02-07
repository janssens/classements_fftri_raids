# classements_fftri_raids

Outil pour calculer et afficher les classements FFTRI raid 
à partir des licence et des résultats chargé dans l'application.

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

``php bin/console app:import:registrations PATH/TO/FILE.csv``

``php bin/console app:import:race_ranking PATH/TO/FILE.csv``