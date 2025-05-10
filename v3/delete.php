<?php
// php debugging block

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
?>

<?php
session_start();

if ( isset($_GET["movie_id"]) ) {             // if id of the movie exists
    $movie_id = $_GET["movie_id"];                  // read id

    $servername = "database-1.cvau6aysoqkt.ap-southeast-2.rds.amazonaws.com";
    $username = ""; // MODIFY IN EC2
    $password = ""; // MODIFY IN EC2
    $database = "mymovies";

    // creating connection
    $connection = new mysqli($servername, $username, $password, $database);

    // delete movie with specified ID 
    $sql = "DELETE FROM movies WHERE movie_id=$movie_id";
    $connection->query($sql);
}

// redirecting user to index file (list of movies), and exit execution of this file
header("location: index.php");
exit;

?>
