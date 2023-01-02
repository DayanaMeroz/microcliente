<?php
include "config.php";
include "utils.php";

$dbConn =  connect($db);

/*
  listar todos los posts o solo uno
 */
if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
    if (isset($_GET['rep_cedula']))
    {
      //Mostrar un post
      $sql = $dbConn->prepare("SELECT * FROM tb_representante where rep_cedula=:rep_cedula");
      $sql->bindValue(':rep_cedula', $_GET['rep_cedula']);
      $sql->execute();
      header("HTTP/1.1 200 OK");
      echo json_encode(  $sql->fetch(PDO::FETCH_ASSOC)  );
      exit();
	  }
    else {
      //Mostrar lista de post
      $sql = $dbConn->prepare("SELECT * FROM tb_representante");
      $sql->execute();
      $sql->setFetchMode(PDO::FETCH_ASSOC);
      header("HTTP/1.1 200 OK");
      echo json_encode( $sql->fetchAll()  );
      exit();
	}
}

// Crear un nuevo post
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $input = $_POST;
    $sql = "INSERT INTO tb_representante
          (rep_cedula, rep_direccion, rep_nombres, rep_telefono)
          VALUES
          (:rep_cedula, :rep_direccion, :rep_nombres, :rep_telefono)";
    $statement = $dbConn->prepare($sql);
    bindAllValues($statement, $input);
    $statement->execute();
    $postId = $dbConn->lastInsertId();
    if($postId)
    {
      $input['rep_cedula'] = $postId;
      header("HTTP/1.1 200 OK");
      echo json_encode($input);
      exit();
	 }
}

//Borrar
if ($_SERVER['REQUEST_METHOD'] == 'DELETE')
{
	$id = $_GET['rep_cedula'];
  $statement = $dbConn->prepare("DELETE FROM tb_representante where rep_cedula=:rep_cedula");
  $statement->bindValue(':rep_cedula', $id);
  $statement->execute();
	header("HTTP/1.1 200 OK");
	exit();
}

//Actualizar
if ($_SERVER['REQUEST_METHOD'] == 'PUT')
{
    $input = $_GET;
    $postId = $input['rep_cedula'];
    $fields = getParams($input);

    $sql = "
          UPDATE rep_clientes
          SET $fields
          WHERE rep_cedula='$postId'
           ";

    $statement = $dbConn->prepare($sql);
    bindAllValues($statement, $input);

    $statement->execute();
    header("HTTP/1.1 200 OK");
    exit();
}


//En caso de que ninguna de las opciones anteriores se haya ejecutado
header("HTTP/1.1 400 Bad Request");

?>