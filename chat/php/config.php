<?php
  $hostname = "localhost";
  $username = "skerikme";
  $password = "Skerik123";
  $dbname = "inter";

  $conn = mysqli_connect($hostname, $username, $password, $dbname);
  if(!$conn){
    echo "Database connection error".mysqli_connect_error();
  }
?>
