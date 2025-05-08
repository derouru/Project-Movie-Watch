<?php
// php debugging block

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
?>

<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$database = "mymovies";

// creating connection
$connection = new mysqli($servername, $username, $password, $database);


$name = "";
$watched = "";

$errorMessage = "";
$successMessage = "";

if ( $_SERVER['REQUEST_METHOD'] == 'GET') {
    // GET method: show data of the movie

    if ( !isset($_GET["movie_id"]) ) {                    // if id of the movie does not exist
        header("location: /mymovies/index.php");    // we need to redirect user to index file
        exit;                                       // and exit execution of this file
    }

    // otherwise, we can read the ID of the movie from the request
    $movie_id = $_GET["movie_id"];

    // writing and executing sql query to get specific row of movie to be edited
    $sql = "SELECT * FROM movies WHERE movie_id=$movie_id";
    $result = $connection->query($sql);
    $row = $result->fetch_assoc(); // then we read the data of the movie from the database

    // if we don't have any data in the db, redirect user to index page
    if ( !$row ) {                   
        header("location: /mymovies/index.php");   
        exit;                                       
    }

    // otherwise, we read the data from the database. then display in the form
    $name = $row["name"];
    $watched = $row["watched"];
}
else {
    // POST method: update data of the movie
    // we first read the data from the form
    $movie_id = $_POST["movie_id"]; // Get the hidden POST movie_id from the form
    $name = $_POST["name"];
    $watched = $_POST["watched"];

    // check if we have empty fields
    do {
        if ( empty($name) || empty($watched) ) {
            $errorMessage = "All the fields are required.";
            break;
        }
        
        // update movie in database
        $sql = "UPDATE movies " .
                "SET name = '$name', watched = '$watched' " . 
                "WHERE movie_id = $movie_id";

        $result = $connection->query($sql);

        // check if query is successful
        if (!$result) {
            $errorMessage = "Invalid query: " . $connection->error;
            break;
        }

        $successMessage = "Movie updated correctly.";
        
        // redirecting user to index file (list of movies), and exit execution of this file
        header("location: index.php");
        exit;

    } while (false);

}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE-edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Movies</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container my-5">
        <h2>New Movie</h2>

        <?php
        if ( !empty($errorMessage) ) {
            echo "
                <div class='alert alert-warning alert-dismissible fade show' role='alert'>
                    <strong>$errorMessage</strong>
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>
            ";
        }
        ?>

        <form method="post">
            <input type="hidden" name="movie_id" value="<?php echo $movie_id; ?>">
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Name</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="name" value="<?php echo $name; ?>">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">When watched?</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="watched" value="<?php echo $watched; ?>">
                </div>
            </div>

            <?php
            if ( !empty($successMessage) ) {
                echo "
                <div class='row mb-3'>
                    <div class='offset-sm-3 col-sm-6'>
                        <div class='alert alert-success alert-dismissible fade show' role='alert'>
                            <strong>$successMessage</strong>
                            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                        </div>
                    </div>                
                </div>                    
                ";
            }
            ?>

            <div class="row mb-3">
                <div class="offset-sm-3 col-sm-3 d-grid">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
                <div class="col-sm-3 d-grid">
                    <a class="btn btn-outline-primary" href="index.php" role="button">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
