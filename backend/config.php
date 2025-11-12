<?php
// Define the database credentials as variables
//$db_host = "db.fr-pari1.bengt.wasmernet.com";
//$db_port = 10272; // Make sure this is treated as an integer
//$db_name = "webdev";
//$db_user = "290c6a97764b80005ea0e33253b5";
//$db_pass = "0691290c-6a97-78c7-8000-452cc05b6161"; // Use the password provided exactly as is


$db_host = "localhost";
$db_name = "webdev";
$db_user = "root";
$db_pass = "";
// Attempt to establish a connection using the mysqli object-oriented method
// The mysqli constructor takes the following parameters:
// host, username, password, dbname, port
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check the connection status immediately
if ($conn->connect_error) {
    // If the connection fails, stop the script and display a detailed error message
    // The "die()" command prints a message and terminates the script
    die("Connection failed: " . $conn->connect_error);
}

// If the script reaches this point, the connection was successful.
// You can print a success message to the logs here if you want:
// echo "Database connected successfully!"; 
?>
