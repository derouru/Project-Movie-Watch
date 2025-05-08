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
        <a class="btn btn-primary" href="/Project-Movie-Watch/mymovies/create.php" role="button">Add Movie</a>
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
                // php variables
                $servername = "localhost";
                $username = "root";
                $password = "";
                $database = "mymovies";

                // Get the user_id
                $user_id = $_SESSION['user_id'];
                
                // creating connection
                $connection = new mysqli($servername, $username, $password, $database);
                
                // check connection, display error message if fail
                if ($connection->connect_error) {
                    die("Connection failed: " . $connection->connect_error);
                }

                // read all rows from database table
                $sql = "SELECT * FROM movies WHERE user_id=$user_id";
                $result = $connection->query($sql);

                // check if query worked
                if (!$result) {
                    die("Invalid query: " . $connection->error);
                }

                // reading data of each row
                while($row = $result->fetch_assoc()) {
                    echo "
                    <tr>
                        <td>$row[movie_id]</td>
                        <td>$row[name]</td>
                        <th>$row[watched]</th>
                        <td>
                            <a class='btn btn-primary btn-sm' href='/Project-Movie-Watch/mymovies/edit.php?movie_id=$row[movie_id]'>Edit</a>
                            <a class='btn btn-danger btn-sm' href='/Project-Movie-Watch/mymovies/delete.php?movie_id=$row[movie_id]'>Delete</a>
                        </td>
                    </tr>
                    ";
                }
                
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
