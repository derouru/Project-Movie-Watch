<?php
  // this file establishes a connection to the database

  $db_server = "database-1.cvau6aysoqkt.ap-southeast-2.rds.amazonaws.com"; // AWS RDS endpoint
  $db_user = ""; // MODIFY IN EC2. AWS RDS master username
  $db_password = ""; // MODIFY IN EC2. AWS RDS master password
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