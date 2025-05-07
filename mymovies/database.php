<?php
  // this file establishes a connection to the database

  $db_server = "localhost";
  $db_user = "root";
  $db_password = "";
  $db_name = "mymovies";
  $connection = "";

  try {
    $connection = mysqli_connect($db_server, $db_user, $db_password, $db_name);
  }
  catch (mysqli_sql_exception) {
    // echo"Connection Failed!";
  }

  if ($connection) {
    // echo"Connection Successful!";
  }

?>