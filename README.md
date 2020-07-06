# Scanup Back

![Scan'up logo](http://scanup.fr/wp-content/uploads/2017/01/ScanUp_logo_2.1_small.png)

## Getting started

Before installing the project, be sure to have [Docker](https://docs.docker.com/install/) and [docker-compose](https://docs.docker.com/compose/install/)

Copy/paste your `.env.example` and create the file `.env`
```bash
cp .env.example .env
```

Run the containers
```bash
make start
```

## To do
* Modifier le schema de base de donne comme telle : Supprimer le champ `link_question` dans la table `responses` et le remplacer par le champ `parent_answer` dans la table `questions`

## Urls

* [Application](http://localhost/)
* [Adminer](http://localhost:8080/) You can get the credentials in the file docker-compose file

## Commands

Run the containers
```bash
make start
```

Run the container in the background
```bash
make start -d
```

Use bash inside the container `scanup-php`
```bash
make bash
```

Stop/Remove the containers
```bash
make down
```