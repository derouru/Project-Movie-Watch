<?php
// php debugging block

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
?>

<?php
session_start();

// redirect the users to the login page if they are not yet logged in
if (!isset($_SESSION['user_name'])) {
    header('Location: login.php');
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
</head>
<body>
    <div class="container my-5">
        <?php
            if (isset($_SESSION['user_name'])) {
                // User is logged in, show the logout button
                echo "<h2>Welcome, " . $_SESSION['user_name'] . "</h2>";
                echo "<a href='logout.php' class='btn btn-danger'>Logout</a>";
            }
        ?>
        <h2>List of Movies</h2>
        <a class="btn btn-primary" href="/Project-Movie-Watch/v3/create.php" role="button">Add Movie</a>
        <br>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Watched</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Get the user_id
                $user_id = $_SESSION['user_id'];

                // creating connection
                $api_url = "https://cgtyjpqli6.execute-api.ap-southeast-2.amazonaws.com/dev?user_id=" . $user_id;

                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => $api_url,
                    CURLOPT_RETURNTRANSFER => true,
                ]);

                // output of the URL
                $response = curl_exec($curl);

                // if error, throw error message
                if (curl_errno($curl)) {
                    die("cURL error: " . curl_error($curl));
                }

                // close url connection
                curl_close($curl);

                // First decode the outer response
                $apiResponse = json_decode($response, true);

                // Check if decoding was successful
                if (json_last_error() !== JSON_ERROR_NONE) {
                    die("Failed to decode API response: " . json_last_error_msg());
                }

                $movies = []; // Initialize as empty array

                try {
                    $apiResponse = json_decode($response, true);
                    
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new Exception("JSON decode error: " . json_last_error_msg());
                    }

                    if (isset($apiResponse['body'])) {
                        $movies = json_decode($apiResponse['body'], true);
                        if (json_last_error() !== JSON_ERROR_NONE) {
                            throw new Exception("Body JSON decode error: " . json_last_error_msg());
                        }
                    }
                } catch (Exception $e) {
                    error_log($e->getMessage());
                    $movies = []; // Ensure it's always an array
                }

                // Now check if $movies is an array before looping
                if (!is_array($movies)) {
                    die("Unexpected movies format: " . print_r($movies, true));
                }

                // reading data of each row
                foreach ($movies as $row) {
                    echo "
                    <tr>
                        <td>{$row['movie_id']}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['watched']}</td>
                        <td>
                            <a class='btn btn-primary btn-sm' href='/Project-Movie-Watch/v3/edit.php?movie_id={$row['movie_id']}'>Edit</a>
                            <a class='btn btn-danger btn-sm' href='/Project-Movie-Watch/v3/delete.php?movie_id={$row['movie_id']}'>Delete</a>
                        </td>
                    </tr>
                    ";
                }
                
                // If you want to handle empty results:
                if (empty($movies)) {
                    echo "<tr><td colspan='4'>No movies found</td></tr>";
                }
                
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
