<?php
	// Connect to database
	include("db_connect.php");
	$request_method = $_SERVER["REQUEST_METHOD"];
    function getCategorie($id=0)
	{
		global $conn;
		$query = "SELECT * FROM produit";
		if($id != 0)
		{
			$query .= " WHERE category_id=".$id;
		}
		$response = array();
		$result = mysqli_query($conn, $query);
		while($row = mysqli_fetch_array($result))
		{
			$response[] = $row;
		}
		header('Content-Type: application/json');
		echo json_encode($response, JSON_PRETTY_PRINT);
	}

    switch($request_method)
	{
		
		case 'GET':
			// Retrive Products
			if(!empty($_GET["id"]))
			{
				//$id=intval($_GET["id"]);
				$id=htmlspecialchars($_GET["id"]);
				//$id=$_GET["id"];
				if ($_GET["id"]!="'")
				{
					getCategorie($id);
				}
			}
			break;
    }