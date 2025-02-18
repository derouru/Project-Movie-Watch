<?php
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

// submitting POST requests
if ( $_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST["name"];
    $watched = $_POST["watched"];

    // checking if fields are complete 
    do {
        if ( empty($name) || empty($watched) ) {
            $errorMessage = "All the fields are required.";
            break;
        }
        
        // add new movie to database
        $sql = "INSERT INTO movies (name, watched) " .
                "VALUES ('$name', '$watched')";
        $result = $connection->query($sql);

        // check if query is successful
        if (!$result) {
            $errorMessage = "Invalid query: " . $connection->error;
            break;
        }

        $name = "";
        $watched = "";

        $successMessage = "Movie added correctly.";
        
        // redirecting user to index file (list of movies), and exit execution of this file
        header("location: /mymovies/index.php");
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
                    <a class="btn btn-outline-primary" href="/myshop/index.php" role="button">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</body>
</html>