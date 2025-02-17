<?php
if ( isset($_GET["id"]) ) {             // if id of the movie exists
    $id = $_GET["id"];                  // read id

    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "mymovies";

    // creating connection
    $connection = new mysqli($servername, $username, $password, $database);

    // delete movie with specified ID 
    $sql = "DELETE FROM movies WHERE id=$id";
    $connection->query($sql);
}

// redirecting user to index file (list of movies), and exit execution of this file
header("location: /mymovies/index.php");
exit;

?>