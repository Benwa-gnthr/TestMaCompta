# MaCompta - Test Back-End

Bonjour! Voici ce que j'ai produis pour le test que vous m'avez fourni. 

## Description

Cette API permet de gérer les écritures comptables et les comptes selon le principe CRUD (Create, Read, Update, Delete). Elle a été développée en utilisant PHP 7 et SlimPHP, et utilise MySQL comme base de données.

## Installation de l'API 

1-Clonez ce répertoire sur votre machine locale.
2-Créez une base de données MySQL vide.
3-Importez le fichier macompta.sql dans la base de données.
4-Modifiez le fichier Database.php avec les informations de connexion à la base de données.
4-Lancez l'API en utilisant la commande php -S localhost:8000 -t public.

##  Utilisation

L'API peut être testée en utilisant le logiciel Insomnia ou tout autre outil équivalent. Comme demander un fichier d'export 'MaComptaTest.json' est fourni dans le répertoire.

Les endpoints disponibles sont les suivants :

### Écritures

- 'GET /comptes/{uuid}/ecritures' : récupère la liste des écritures pour un compte donné.
- 'POST /comptes/{uuid}/ecritures' : ajoute une nouvelle écriture pour un compte donné.
- 'PUT /comptes/{uuid}/ecritures/{ecriture_uuid}' : modifie une écriture existante pour un compte donné.
- 'DELETE /comptes/{uuid}/ecritures/{ecriture_uuid}' : supprime une écriture existante pour un compte donné.

### Comptes     

- GET /comptes/{uuid} : récupère les informations d'un compte donné.
- POST /comptes : crée un nouveau compte.
- PUT /comptes/{uuid} : modifie les informations d'un compte existant.
- DELETE /comptes/{uuid} : supprime un compte existant.

## Exemple de test 

### Création d'un compte

1. Envoyer une requête POST à l'endpoint '/comptes' avec les données suivantes :

'''json 
{
"login": "JeanPiPa",
"password": "Papin123",
"name": "JeanPierrePapin"
}
'''

2. Vérifier que la réponse a le code HTTP 201 et contient son UUID.

### Ajout d'une écriture

1. Envoyer une requête POST à l'endpoint '/comptes/{uuid}/ecritures' où {uuid} est l'UUID du compte créé précédemment avec ces données par exemple :

'''json 
{
    "label": "Nouveau ballon Bleu",
    "date": "2024/01/01",
    "type": "D",
    "amount": 120.00
}
'''

2. Vérifier que la réponse a le code HTTP 201 et contient son UUID.

### Modification de l'écriture 

1. Envoyer une requête PUT à l'endpoint '/comptes/{uuid}/ecritures/{ecriture_uuid}' où {uuid} est l'UUID du compte créé précédemment et {ecriture_uuid} est l'UUID de l'écriture crée précédemment.

2. Dans le corps de la requête, ajouter des informations au format JSON :

'''json
{
    "label": "Nouveau ballon Rouge",
    "date": "2024-01-05",
    "type": "D",
    "amount": 125.00
}
'''

3. Vérifier que la reponse a le code HTTP 204 avec No Content.

### Suppression de l'écriture 

1. Envoyer une requête DELETE à l'endpoint '/comptes/{uuid}/ecritures/{ecriture_uuid}' où {uuid} est l'UUID du compte utilisée précédemment et {ecriture_uuid} est l'UUID de l'écriture utilisée précédemment.
2. Vérifier que la reponse a le code HTTP 204 avec No Content.

### Récupération de la liste des écritures du compte

1. Envoyer une requête GET à l'endpoint '/comptes/{uuid}/ecritures' où {uuid} est l'UUID du compte utilisée précédemment.
2. Vérifier que la reponse a le code HTTP 200 et qui contient la liste des ecritures du compte, au format suivant: 

'''json
{
    "items": [
        {
            "uuid": "89a8dd1a-95ce-4515-b7bd-eb441aa156b7",
			"compte_uuid": "b32fdc03-0ed8-11ef-9598-d843ae552c7d",
            "label": "Nouveau ballon Jaune",
            "date": "2024-01-03",
            "type": "D",
            "amount": 115.00,
            "created_at": "2022-01-03 10:30:00",
            "updated_at": "2022-01-03 10:35:00"
        }
    ]
}
'''