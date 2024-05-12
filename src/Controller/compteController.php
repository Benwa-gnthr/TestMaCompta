<?php

declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ramsey\Uuid\Uuid;
use App\Database;
use PDO;

class CompteController
{
    // Exercice 6
    public function getCompte(Request $request, Response $response, array $args): Response
    {
        $uuid = $args['uuid'];

        $database = new Database();
        $pdo = $database->getConnection();

        $stmt = $pdo->prepare("SELECT * FROM comptes WHERE uuid = :uuid");
        $stmt->bindParam(':uuid', $uuid);
        $stmt->execute();

        $compte = $stmt->fetch();

        if (!$compte) {
            $response->getBody()->write(json_encode(['error' => 'Compte non trouvé']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $response->getBody()->write(json_encode($compte));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    // Exercice 7
    public function postCompte(Request $request, Response $response, array $args): Response
    {
        $data = $request->getParsedBody();

        if (!isset($data['login']) || !isset($data['password'])) {
            $response->getBody()->write(json_encode(['error' => 'Données invalides']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        
        $uuid = Uuid::uuid4()->toString();
        
        $database = new Database();
        $pdo = $database->getConnection();

        $stmt = $pdo->prepare("INSERT INTO comptes (uuid, login, password) VALUES (:uuid, :login, :password)");
        $stmt->bindParam(':uuid', $uuid);
        $stmt->bindParam(':login', $data['login']);
        $stmt->bindParam(':password', password_hash($data['password'], PASSWORD_DEFAULT));
        
        $stmt->execute();
        
        $response->getBody()->write(json_encode(['uuid' => $uuid]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
        

    // Exercice 8
    public function putCompte(Request $request, Response $response, $args): Response
    {
        $uuid = $args['uuid'];
        $data = $request->getParsedBody();

        if (!isset($data['uuid'])) {
            $response->getBody()->write(json_encode(['error' => 'Données invalides']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        if (!isset($data['login']) || !isset($data['password'])) {
            $response->getBody()->write(json_encode(['error' => 'Données invalides']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        if ($data['uuid'] != $uuid) {
            $response->getBody()->write(json_encode(['error' => 'Données invalides']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $database = new Database();
        $pdo = $database->getConnection();

        $stmt = $pdo->prepare('SELECT * FROM comptes WHERE uuid = :uuid');
        $stmt->bindParam(':uuid', $uuid);
        $stmt->execute();
        $compte = $stmt->fetch();

        if ($compte['login'] != $data['login']) {
            $response->getBody()->write(json_encode(['error' => 'Le login ne peut pas être modifié']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $stmt = $pdo->prepare("UPDATE comptes SET password = :password WHERE uuid = :uuid");
        $stmt->bindParam(':password', password_hash($data['password'], PASSWORD_DEFAULT));
        $stmt->bindParam(':uuid', $uuid);
        $stmt->execute();

        return $response->withStatus(204);
    }

    // Exercice 9
    public function deleteCompte(Request $request, Response $response, $args): Response
    {
        $uuid = $args['uuid'];

        $database = new Database();
        $pdo = $database->getConnection();

        $stmt = $pdo->prepare('SELECT * FROM ecritures WHERE compte_uuid = :uuid');
        $stmt->bindParam(':uuid', $uuid);
        $stmt->execute();
        $ecritures = $stmt->fetchAll();
        if ($ecritures) {
            $response->getBody()->write(json_encode(['error' => 'Un compte avec des écritures ne peut pas être supprimé']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $stmt = $pdo->prepare('DELETE FROM comptes WHERE uuid = :uuid');
        $stmt->bindParam(':uuid', $uuid);
        $stmt->execute();

        return $response->withStatus(204);
    }

    //Exercice 10
    public function getAllCompteAndEcriture(Request $request, Response $response, $args): Response
    {
        $database = new Database();
        $pdo = $database->getConnection();
        // Exécuter la requête SQL
        $stmt = $pdo->prepare("SELECT comptes.uuid AS compte_uuid, comptes.login, comptes.name,
        ecritures.uuid AS ecriture_uuid, ecritures.label, ecritures.date, ecritures.amount
        FROM comptes
        LEFT JOIN ecritures ON comptes.uuid = ecritures.compte_uuid");
        $stmt->execute();

        // Récupérer les résultats de la requête
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Parcourir les résultats et regrouper les écritures par compte
        $comptes = [];
        foreach ($results as $result) {
            $compte_uuid = $result['compte_uuid'];
            if (!isset($comptes[$compte_uuid])) {
                $comptes[$compte_uuid] = [
                    'uuid' => $compte_uuid,
                    'login' => $result['login'],
                    'name' => $result['name'],
                    'ecritures' => []
                ];
            }
            if ($result['ecriture_uuid'] !== null) {
                $comptes[$compte_uuid]['ecritures'][] = [
                    'uuid' => $result['ecriture_uuid'],
                    'label' => $result['label'],
                    'date' => $result['date'],
                    'amount' => $result['amount']
                ];
            }
        }
        $response->getBody()->write(json_encode($comptes));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}