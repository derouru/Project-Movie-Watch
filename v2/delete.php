<?php
if ( isset($_GET["id"]) ) {             // if id of the movie exists
    $id = $_GET["id"];                  // read id

    $servername = "database-1.cvau6aysoqkt.ap-southeast-2.rds.amazonaws.com";
    $username = ""; // MODIFY IN EC2
    $password = ""; // MODIFY IN EC2
    $database = "mymovies";

    // creating connection
    $connection = new mysqli($servername, $username, $password, $database);

    // delete movie with specified ID 
    $sql = "DELETE FROM movies WHERE id=$id";
    $connection->query($sql);
}

// redirecting user to index file (list of movies), and exit execution of this file
header("location: Project-Movie-Watch/mymovies/index.php");
exit;

?>