<?php
// php debugging block

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
?>

<?php
session_start();

$name = "";
$watched = "";

$errorMessage = "";
$successMessage = "";

if ( $_SERVER['REQUEST_METHOD'] == 'GET') {
    // GET method: show data of the movie

    if ( !isset($_GET["movie_id"]) ) {                    // if id of the movie does not exist
        header("location: /v2/index.php");    // we need to redirect user to index file
        exit;                                       // and exit execution of this file
    }

    // otherwise, we can read the ID of the movie from the request
    $movie_id = $_GET["movie_id"];
    $user_id = $_SESSION['user_id'];

    // Call Lambda to get movies for this user
    $ch = curl_init('https://cgtyjpqli6.execute-api.ap-southeast-2.amazonaws.com/dev/?user_id='.$user_id);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json'
        ]
    ]);

    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);


    if ($httpcode !== 200) {
        $errorMessage = "Failed to fetch movies";
    } else {
        // Decode the response properly
        $responseData = json_decode($response, true);
        
        // Handle both direct Lambda response and API Gateway proxy response
        $movies = [];
        if (isset($responseData['body'])) {
            // API Gateway proxy format
            $movies = json_decode($responseData['body'], true);
        } else {
            // Direct Lambda response
            $movies = $responseData;
        }

        // Ensure $movies is an array
        if (!is_array($movies)) {
            $movies = [];
        }

        // Find the specific movie
        $movie = null;
        foreach ($movies as $m) {
            if (isset($m['movie_id']) && $m['movie_id'] == $movie_id) {
                $movie = $m;
                break;
            }
        }

        if (!$movie) {
            header("location: index.php");
            exit;
        }

        $name = $movie['name'] ?? '';
        $watched = $movie['watched'] ?? '';
    }
}
else {
    // POST method: update data of the movie
    // we first read the data from the form
    $movie_id = $_POST["movie_id"]; // Get the hidden POST movie_id from the form
    $name = $_POST["name"];
    $watched = $_POST["watched"];
    $user_id = $_SESSION['user_id']; // Get user_id from session

    // check if we have empty fields
    do {
        if ( empty($name) || empty($watched) ) {
            $errorMessage = "All the fields are required.";
            break;
        }

        // Prepare the data to send to Lambda
        $data = [
            'movie_id' => $movie_id,
            'name' => $name,
            'watched' => $watched,
            'user_id' => $user_id
        ];

        // Add error logging for the API request
        error_log("Sending to Lambda: " . print_r($data, true));

        $ch = curl_init('https://cgtyjpqli6.execute-api.ap-southeast-2.amazonaws.com/dev');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'PATCH', // Changed from POST to PATCH
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json'
            ]
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
