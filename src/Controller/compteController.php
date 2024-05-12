<?php

declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ramsey\Uuid\Uuid;
use App\Database;

class CompteController
{
    // Exercice 6
    public function getCompte(Request $request, Response $response, array $args): Response
    {
        $uuid = $args['uuid'];

        // Récupérer le compte depuis la base de données
        $database = new Database();
        $pdo = $database->getConnection();

        $stmt = $pdo->prepare("SELECT * FROM comptes WHERE uuid = :uuid");
        $stmt->bindParam(':uuid', $uuid);
        $stmt->execute();

        $compte = $stmt->fetch();

        if (!$compte) {
            // Retourner une erreur 404 si le compte n'existe pas
            $response->getBody()->write(json_encode(['error' => 'Compte non trouvé']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        // Retourner le compte en format JSON
        $response->getBody()->write(json_encode($compte));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    // Exercice 7
    public function postCompte(Request $request, Response $response, array $args): Response
    {

    }
        

    // Exercice 8
    public function putCompte(Request $request, Response $response, $args): Response
    {

    }

    // Exercice 9
    public function deleteCompte(Request $request, Response $response, $args): Response
    {

    }
}