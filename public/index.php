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

// Exercice 2
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

// Exercice 3
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

    $ecritureUuid = Uuid::uuid4()->toString();

    $database = New App\Database;

    $pdo = $database->getConnection();

    $stmt = $pdo->prepare("INSERT INTO ecritures (uuid, compte_uuid, label, date, amount) VALUES (:uuid, :compte_uuid, :label, :date, :amount)");
    $stmt->bindParam(':uuid', $ecritureUuid);
    $stmt->bindParam(':compte_uuid', $args['uuid']);
    $stmt->bindParam(':label', $data['label']);
    $stmt->bindParam(':date', $data['date']);
    $stmt->bindParam(':amount', $data['montant']);
    $stmt->execute();

    $db = null;

    $response->getBody()->write(json_encode(['uuid' => $ecritureUuid]));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
});

//Exercice 4
$app->put('/comptes/{compte_uuid}/ecritures/{ecriture_uuid}', function (Request $request, Response $response, array $args) {
    $data = $request->getParsedBody();

    if (!isset($data['uuid']) || !isset($data['label']) || !isset($data['date']) || !isset($data['montant'])) {
        $response->getBody()->write(json_encode(['error' => 'Données invalides']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    if ($data['montant'] < 0) {
        $response->getBody()->write(json_encode(['error' => 'Le montant de l\'écriture ne doit pas être négatif']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    try {
        $database = New App\Database;
    
        $pdo = $database->getConnection();    
    } catch (PDOException $e) {
        $response->getBody()->write(json_encode(['error' => 'Erreur de connexion à la base de données']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }

    $stmt = $pdo->prepare("SELECT * FROM ecritures WHERE uuid = :uuid AND compte_uuid = :compte_uuid");
    $stmt->bindParam(':uuid', $data['uuid']);
    $stmt->bindParam(':compte_uuid', $args['compte_uuid']);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        $response->getBody()->write(json_encode(['error' => 'Écriture non trouvée']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
    }

    $stmt = $pdo->prepare("UPDATE ecritures SET label = :label, date = :date, amount = :montant WHERE uuid = :uuid");
    $stmt->bindParam(':uuid', $data['uuid']);
    $stmt->bindParam(':label', $data['label']);
    $stmt->bindParam(':date', $data['date']);
    $stmt->bindParam(':montant', $data['montant']);
    var_dump($data['montant'], $data['date'], $data['label'], $data['uuid']);
    $stmt->execute(); 

    return $response->withStatus(204);
});

// Exercice 5
$app->delete('/comptes/{compte_uuid}/ecritures/{ecriture_uuid}', function (Request $request, Response $response, array $args) {
    $ecritureUuid = $args['ecriture_uuid'];

    $database = New App\Database;
    $pdo = $database->getConnection();   
    $stmt = $pdo->prepare("DELETE FROM ecritures WHERE uuid = :uuid");
    $stmt->bindParam(':uuid', $ecritureUuid);
    $stmt->execute(); 

    return $response->withStatus(204);
});

$app->run(); 