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
        <h2>List of Movies</h2>
        <a class="btn btn-primary" href="/mymovies/create.php" role="button">Add Movie</a>
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
                
                // creating connection
                $connection = new mysqli($servername, $username, $password, $database);
                
                // check connection, display error message if fail
                if ($connection->connect_error) {
                    die("Connection failed: " . $connection->connect_error);
                }

                // read all rows from database table
                $sql = "SELECT * FROM movies";
                $result = $connection->query($sql);

                // check if query worked
                if (!$result) {
                    die("Invalid query: " . $connection->error);
                }

                // reading data of each row
                while($row = $result->fetch_assoc()) {
                    echo "
                    <tr>
                        <td>$row[id]</td>
                        <td>$row[name]</td>
                        <th>$row[watched]</th>
                        <td>
                            <a class='btn btn-primary btn-sm' href='/mymovies/edit.php?id=$row[id]'>Edit</a>
                            <a class='btn btn-danger btn-sm' href='/mymovies/delete.php?id=$row[id]'>Delete</a>
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