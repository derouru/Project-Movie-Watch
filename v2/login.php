<?php
// Start the session
session_start();
include('database.php');

// Initialize variables
$input_username = '';
$input_password = '';
$failed = 0;
$fail_message = '';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the inputted username and password from the form
    $input_username = $_POST['username'] ?? '';  // if walang laman just use ''
    $input_password = $_POST['password'] ?? '';  // if walang laman just use ''

    // Prepare and execute the query
    $query = mysqli_prepare($connection, "SELECT * FROM users WHERE user_name = ?");
    if (!$query) {
        die("Prepare failed: " . mysqli_error($connection)); // for error handling
    }

    if (!mysqli_stmt_bind_param($query, "s", $input_username)) {
        die("Bind failed: " . mysqli_stmt_error($query)); // for error handling
    }

    if (!mysqli_stmt_execute($query)) {
        die("Execute failed: " . mysqli_stmt_error($query)); // for error handling
    }

    // Get the result
    $result = mysqli_stmt_get_result($query);

    // Access the row matched from the result
    $row = mysqli_fetch_assoc($result);

    // Check if the user exists
    $fetched_username = '';
    $fetched_password = '';
    $fetched_user_id = '';

    if ($row) {
        // User exists
        $fetched_username = $row['user_name'];
        $fetched_password = $row['password'];
        $fetched_user_id = $row['user_id'];

        // Check if the inputted username and password match the fetched data
        if (($input_username === $fetched_username) && password_verify($input_password, $fetched_password)) {
            $_SESSION['user_name'] = $fetched_username;
            $_SESSION['user_id'] = $fetched_user_id;
            header("Location: index.php");
            exit();
        } else {
          $failed = 1;
          $fail_message = 'Login failed: Invalid password.';
        }
    } else {
        // User does not exist
        $failed = 1;
        $fail_message = 'Login failed: User does not exist.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <style>
    body {
      background-color: #f4f7fc;
      height: 100vh;
    }
    .login-card {
      border-radius: 10px;
      padding: 30px;
      background-color: #ffffff;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
    .card-header {
      color: #007bff;
      font-size: 24px;
      font-weight: bold;
      text-align: center;
      border-radius: 8px 8px 0 0;
    }
    .form-control {
      border-radius: 5px;
      height: 40px;
    }
    .btn-primary {
      width: 100%;
      padding: 10px;
      border-radius: 5px;
    }
    .login-container {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
  </style>
</head>

<body>

  <div class="login-container">
    <form class="login-card" method="POST" action="">
      <div class="card-header">
        Movie Watch
      </div>
      <div class="mb-4">
        <label class="form-label" for="username">Username</label>
        <input class="form-control" type="text" id="username" name="username" required />
      </div>
      <div class="mb-4">
        <label class="form-label" for="password">Password</label>
        <input class="form-control" type="password" id="password" name="password" required />
      </div>
      <button class="btn btn-primary mb-3" type="submit">Login</button>
      <?php
      if ($failed) {
        echo"<p class='text-danger'> $fail_message </p>";
      }
    ?>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
