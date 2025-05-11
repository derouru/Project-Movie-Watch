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
    $user_id = $_SESSION['user_id'];

    $servername = "database-1.cvau6aysoqkt.ap-southeast-2.rds.amazonaws.com";
    $username = ""; // MODIFY IN EC2
    $password = ""; // MODIFY IN EC2
    $database = "mymovies";

    // creating connection
    $connection = new mysqli($servername, $username, $password, $database);

    // // delete movie with specified ID 
    // $sql = "DELETE FROM movies WHERE movie_id=$movie_id";
    // $connection->query($sql);

    if ( empty($name) || empty($watched) ) {
        $errorMessage = "All the fields are required.";
        break;
    }

    // Prepare the data to send to Lambda
    $data = [
        'movie_id' => $movie_id,
        'user_id' => $user_id
    ];

    // Add error logging for the API request
    error_log("Sending to Lambda: " . print_r($data, true));

    $ch = curl_init('https://cgtyjpqli6.execute-api.ap-southeast-2.amazonaws.com/dev');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'DELETE',  // Changed to DELETE method
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

}

// redirecting user to index file (list of movies), and exit execution of this file
header("location: index.php");
exit;

?>
