<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test API Rest</title>
</head>
<body>
<h1>Test d'une API REST</h1>
<table class="blueTable">
    <thead>
        <tr>
            <th style="width:30%"><center>Route</center></th>
            <th style="width:10%"><center>Méthode</center></th>
            <th style="width:10%"><center>Type</center></th>
            <th style="width:50%"><center>Description</center></th>
        </tr>
    </thead>
    <tbody>
        <tr><td>http://127.0.0.1/apiRest/produits</td><td>GET</td><td>JSON</td><td>Récupérer toutes les produits.</td></tr>
        <tr><td>http://127.0.0.1/apiRest/produits/{id}</td><td>GET</td><td>JSON</td><td>Récupérer les données d’un seul produit</td></tr>
        <tr><td>http://127.0.0.1/apiRest/produits</td><td>POST</td><td>JSON</td><td>Insérer un nouveau produit dans la base de données.</td></tr>
        <tr><td>http://127.0.0.1/apiRest/produits/{id}</td><td>PUT</td><td>JSON</td><td>Mettre à jour un produit dans la base de données.</td></tr>
        <tr><td>http://127.0.0.1/apiRest/produits/{id}</td><td>DELETE</td><td>JSON</td><td>Supprimer un produit de la base de données.</td></tr>
    </tbody>
</table>
</body>
</html>