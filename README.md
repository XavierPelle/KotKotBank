# KotKotBank

Projet de simulation bancaire et de place boursière.

Pour commencer
Après l'inscription vous pourrez directement vous créer un compte courant et un compte Epargne Action. Ce dernier vous permets d'acceder à la place boursière, et de commencer à trader des actions.

## Pré-requis

1. Docker
2. php
3. symfony
4. mysql

## Installation

 Créer un fichier ```docker-compose.yml``` à la racine du dossier de travail. Il doit comprendre un container nginx, php, mysql, phpmyAdmin.
Il faudra un dockerfile pour la configuration du serveur php. Utiliser la commande ```docker-compose up``` -d. Cloner le répo.

## Démarrage
utiliser la commande ```composer update``` pour mettre à jour les bundles. Lancer le script qui fait tourner la boucle de la place boursière avec la commande ```php bin/console boucle```.

## Fabriqué avec

1. Docker
2. Bootstrap
3. symfony

## Versions
1. Docker 3.8
2. mysql 8
3. php 8.2
4. symfony 5.7

## Auteurs
1. Xavier Pelle /XavierPelle
2. Mathilde Ageron /Mathildeagr
3. Sofian Margoum /sofmgm
4. Dany Dauberton /Danydany140294
