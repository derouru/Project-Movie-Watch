<?php
// php debugging block

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
?>

<?php
session_start();

$servername = "database-1.cvau6aysoqkt.ap-southeast-2.rds.amazonaws.com";
$username = ""; // MODIFY IN EC2
$password = ""; // MODIFY IN EC2
$database = "mymovies";

// creating connection
$connection = new mysqli($servername, $username, $password, $database);

$name = "";
$watched = "";
$user_id = $_SESSION['user_id'];

$errorMessage = "";
$successMessage = "";

// submitting POST requests
if ( $_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST["name"];
    $watched = $_POST["watched"];
    $user_id = $_SESSION['user_id'];

    // checking if fields are complete 
    do {
        if ( empty($name) || empty($watched) ) {
            $errorMessage = "All the fields are required.";
            break;
        }
        

        // add new movie to database
        $sql = "INSERT INTO movies (name, watched, user_id) " .
                "VALUES ('$name', '$watched', '$user_id')";
        // $result = $connection->query($sql);


        // check if query is successful
        // if (!$result) {
        //     $errorMessage = "Invalid query: " . $connection->error;
        //     break;
        // }

        // Prepare the data to send to Lambda
        $data = [
            'name' => $name,
            'watched' => $watched,
            'user_id' => $user_id
        ];

        $ch = curl_init('https://cgtyjpqli6.execute-api.ap-southeast-2.amazonaws.com/dev');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Decode the response
        $result = json_decode($response, true);

        if ($httpcode !== 200 || (isset($result['statusCode']) && $result['statusCode'] != 200)) {
            $errorMessage = "API Error: " . ($result['body'] ?? 'Unknown error');
            break;
        }

        $name = "";
        $watched = "";
        $user_id = $_SESSION['user_id'];

        $successMessage = "Movie added correctly.";
        
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
