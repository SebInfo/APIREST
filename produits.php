<?php
include("db_connect.php");
$request_method = $_SERVER["REQUEST_METHOD"];

function getProducts()
{
    global $conn;
    $query = "SELECT * FROM produit";
    $response = array();

    try 
	{
       	$stmt = $conn->prepare($query);
        $stmt->execute();
        $response = $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupère chaque ligne sous forme de tableau associatif

        header('Content-Type: application/json');
        echo json_encode($response, JSON_PRETTY_PRINT);
    } 
	catch (PDOException $e) 
	{
        http_response_code(500);
        echo json_encode(["error" => "Erreur lors de la récupération des produits : " . $e->getMessage()]);
    }
}
	
function getProduct($id = 0)
{
    global $conn;
    $response = array();

    try {
        if ($id != 0) {
            $query = "SELECT * FROM produit WHERE id = :id LIMIT 1";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        } else {
            getProducts();
        }

        $stmt->execute();
        $response = $stmt->fetchAll(PDO::FETCH_ASSOC);

		if (empty($response)) {
			http_response_code(404);
			echo json_encode(["error" => "Produit avec ID $id introuvable."]);
		} else {
			header('Content-Type: application/json');
			echo json_encode($response, JSON_PRETTY_PRINT);
		}

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Erreur lors de la récupération : " . $e->getMessage()]);
    }
}
	
function AddProduct()
{
    global $conn;

    // Récupération des données POST (avec vérification simple possible)
    $name = $_POST["name"] ?? null;
    $description = $_POST["description"] ?? null;
    $price = $_POST["price"] ?? null;
    $category = $_POST["category"] ?? null;
    $created = date('Y-m-d H:i:s');
    $modified = $created;

    try {
        $query = "INSERT INTO produit (name, description, price, category_id, created, modified)
                  VALUES (:name, :description, :price, :category, :created, :modified)";
        
        $stmt = $conn->prepare($query);

        // Liaison des paramètres
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':category', $category, PDO::PARAM_INT);
        $stmt->bindParam(':created', $created);
        $stmt->bindParam(':modified', $modified);

        if ($stmt->execute()) {
            $response = array(
                'status' => 1,
                'status_message' => 'Produit ajouté avec succès.'
            );
        } else {
            $response = array(
                'status' => 0,
                'status_message' => 'Erreur lors de l\'ajout du produit.'
            );
        }

    } catch (PDOException $e) {
        $response = array(
            'status' => 0,
            'status_message' => 'ERREUR : ' . $e->getMessage()
        );
        http_response_code(500);
    }

    header('Content-Type: application/json');
    echo json_encode($response);
}
	
function updateProduct($id)
{
    global $conn;

    parse_str(file_get_contents("php://input"), $_PUT);

    $fields = [];
    $params = [];

    if (isset($_PUT["name"])) {
        $fields[] = "name = :name";
        $params[':name'] = $_PUT["name"];
    }
    if (isset($_PUT["description"])) {
        $fields[] = "description = :description";
        $params[':description'] = $_PUT["description"];
    }
    if (isset($_PUT["price"])) {
        $fields[] = "price = :price";
        $params[':price'] = $_PUT["price"];
    }
    if (isset($_PUT["category"])) {
        $fields[] = "category_id = :category";
        $params[':category'] = $_PUT["category"];
    }

    // Toujours mettre à jour la date de modification
    $fields[] = "modified = :modified";
    $params[':modified'] = date('Y-m-d H:i:s');

    if (count($fields) === 0) {
        echo json_encode(["error" => "Aucune donnée à mettre à jour."]);
        return;
    }

    $sql = "UPDATE produit SET " . implode(", ", $fields) . " WHERE id = :id";
    $params[':id'] = $id;

    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        echo json_encode(["status" => 1, "message" => "Produit mis à jour avec succès."]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["status" => 0, "error" => $e->getMessage()]);
    }
}
	

function deleteProduct($id)
{
    global $conn;

    try {
        $query = "DELETE FROM produit WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $response = array(
                'status' => 1,
                'status_message' => 'Produit supprimé avec succès.'
            );
        } else {
            $response = array(
                'status' => 0,
                'status_message' => 'Échec de la suppression du produit.'
            );
        }

    } catch (PDOException $e) {
        http_response_code(500);
        $response = array(
            'status' => 0,
            'status_message' => 'Erreur : ' . $e->getMessage()
        );
    }

    header('Content-Type: application/json');
    echo json_encode($response);
}


// Controleur REST
switch ($_SERVER['REQUEST_METHOD']) {

    case 'GET':
        if (!empty($_GET["id"]) && is_numeric($_GET["id"])) {
            $id = intval($_GET["id"]);
            getProduct($id);
        } else {
            getProducts();
        }
        break;

    case 'POST':
        AddProduct();
        break;

    case 'PUT':
        if (!empty($_GET["id"]) && is_numeric($_GET["id"])) {
            $id = intval($_GET["id"]);
            updateProduct($id);
        } else {
            http_response_code(400);
            echo json_encode(["error" => "ID invalide pour la mise à jour."]);
        }
        break;

    case 'DELETE':
        if (!empty($_GET["id"]) && is_numeric($_GET["id"])) {
            $id = intval($_GET["id"]);
            deleteProduct($id);
        } else {
            http_response_code(400);
            echo json_encode(["error" => "ID invalide pour la suppression."]);
        }
        break;

    default:
        header("HTTP/1.0 405 Method Not Allowed");
        echo json_encode(["error" => "Méthode non autorisée"]);
        break;
}
