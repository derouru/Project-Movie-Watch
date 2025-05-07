<?php
session_start();

// redirect the users to the login page if they are not yet logged in
if (!isset($_SESSION['user_name'])) {
    header('Location: login.php');
}

// use external AWS RDS connection
require_once 'database.php';
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
                // use $connection from database.php
                $sql = "SELECT * FROM movies";
                $result = mysqli_query($connection, $sql);

                if (!$result) {
                    die("Invalid query: " . mysqli_error($connection));
                }

                while ($row = mysqli_fetch_assoc($result)) {
                    echo "
                    <tr>
                        <td>{$row['id']}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['watched']}</td>
                        <td>
                            <a class='btn btn-primary btn-sm' href='Project-Movie-Watch/mymovies/edit.php?id={$row['id']}'>Edit</a>
                            <a class='btn btn-danger btn-sm' href='Project-Movie-Watch/mymovies/delete.php?id={$row['id']}'>Delete</a>
                        </td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
