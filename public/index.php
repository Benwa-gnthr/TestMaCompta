<?php

declare(strict_types=1);

use Ramsey\Uuid\Uuid;
use App\Models\Ecriture;
use Slim\Factory\AppFactory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require dirname(__DIR__) . '/vendor/autoload.php';

$app = AppFactory::create();

$app->addBodyParsingMiddleware();

// Exercice 1
$app->get('/comptes/{uuid}/ecritures', function (Request $request, Response $response, $args) {

    $uuid = $args['uuid'];

    $ecritures = [
        ['label' => 'Libellé de test 2'],
        ['Date' => '2 janvier 2015']
    ];

    $responseData = ['items' => $ecritures];
    $response->getBody()->write(json_encode($responseData));

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});

// Exercice 2
$app->post('/comptes/{uuid}/ecritures', function (Request $request, Response $response, array $args) {
    $data = $request->getParsedBody();
    
    if (!isset($data['label']) || !isset($data['date']) || !isset($data['montant'])) {
        $response->getBody()->write(json_encode(['error' => 'Données invalides']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }
    
    if ($data['montant'] < 0) {
        $response->getBody()->write(json_encode(['error' => 'Le montant de l\'écriture ne doit pas être négatif']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    $uuid = Uuid::uuid4()->toString();

    require dirname(__DIR__) . '/src/App/Database.php'; 

    $database = New App\Database;

    $pdo = $database->getConnection();

    $stmt = $pdo->prepare("INSERT INTO ecritures (uuid, compte_uuid, label, date, amount) VALUES (:uuid, :compte_uuid, :label, :date, :amount)");
    $stmt->bindParam(':uuid', $uuid);
    $stmt->bindParam(':compte_uuid', $args['uuid']);
    $stmt->bindParam(':label', $data['label']);
    $stmt->bindParam(':date', $data['date']);
    $stmt->bindParam(':amount', $data['montant']);
    $stmt->execute();

    $db = null;

    $response->getBody()->write(json_encode(['uuid' => $uuid]));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
});

$app->run(); 