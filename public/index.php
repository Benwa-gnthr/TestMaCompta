<?php

declare(strict_types=1);

use Ramsey\Uuid\Uuid;
use App\Models\Ecriture;
use Slim\Factory\AppFactory;
use App\Controller\EcritureController;
use App\Controller\CompteController;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require dirname(__DIR__) . '/vendor/autoload.php';

$app = AppFactory::create();

$app->addBodyParsingMiddleware();

require dirname(__DIR__) . '/src/Controller/ecritureController.php';
require dirname(__DIR__) . '/src/Controller/compteController.php';

$ecritureController = new EcritureController();
$compteController = new compteController();

// Ecriture Routes
$app->get('/comptes/{uuid}/ecritures', $ecritureController->getEcriture(...));

$app->post('/comptes/{uuid}/ecritures', $ecritureController->postEcriture(...));

$app->put('/comptes/{compte_uuid}/ecritures/{ecriture_uuid}', $ecritureController->putEcriture(...));

$app->delete('/comptes/{compte_uuid}/ecritures/{ecriture_uuid}', $ecritureController->deleteEcriture(...));

//Compte Routes

$app->get('/comptes/{uuid}', $compteController->getCompte(...));


$app->run(); 