<?php
/**
 * Create the database and tables for the login system
 *
 * @author    Joshua Connor <connorj4@southernct.edu>
 * @copyright 2026 
 * @date      2026-02-24
 * @version   1.0
 */

error_reporting(-1);
ini_set('display_errors', 'On');

/* the first connection to the database */
DEFINE('DB_HOST', "localhost");
DEFINE('DB_USER', "root");
DEFINE('DB_PASSWORD', ""); //Note: this should be your root password

// Establish the initial database connection
try {
  $db_connection = new mysqli(DB_HOST, DB_USER, DB_PASSWORD)
    OR die("Connection failed: " . $db_connection->connect_error);
} catch (Exception $e) {
  echo 'Caught exception: ',  $e->getMessage(), nl2br("\r\n");
}

/* Check if database is there or will create it */
$create_stmt = "CREATE DATABASE IF NOT EXISTS login_db";

/* Check if database drop was sucessful */
if(mysqli_query($db_connection, $create_stmt)) {
	echo nl2br("Database was successfully created.\r\n");
} else {
	echo "Error dropping database: " . mysqli_error() . nl2br("\r\n");
}
$prep_stmt = $db_connection -> prepare($create_stmt);
$prep_stmt->execute();
$prep_stmt->close();

/* Change to the created database */
$db_connection->select_db("login_db");

/* Drop all tables for clean install */
$db_connection->query('SET foreign_key_checks = 0');
if ($result = $db_connection->query("SHOW TABLES")) {
  while($row = $result->fetch_array(MYSQLI_NUM)) {
    $db_connection->query('DROP TABLE IF EXISTS '.$row[0]);
	}
	echo "Tables removed successfully." . nl2br("\r\n");
} else {
	echo "No tables were removed." . nl2br("\r\n");
}
$db_connection->query('SET foreign_key_checks = 1');


/* Salt used for seasoning */
$salt = 'authentication';

//-----------------------------------------------------
// Create Database Tables
//-----------------------------------------------------
echo "Table creation started." . nl2br("\r\n");

/* Below Are Tables That Have Only Primary Keys */
/* ------------------------------------------ */

/* Role */
$create_roles = $db_connection->prepare(
	"CREATE OR REPLACE TABLE Roles(
        role_id int NOT NULL AUTO_INCREMENT,
        role_type varchar(255) NOT NULL,
        PRIMARY KEY(role_id));");
$create_roles->execute();
$create_roles->close();

/* Contacts */
$create_contacts = $db_connection->prepare(
	"CREATE OR REPLACE TABLE Contacts(
        contact_id int NOT NULL AUTO_INCREMENT,
        last_name varchar(255) NOT NULL,
        first_name varchar(255) NOT NULL,
        email varchar(255) NOT NULL,
        phone varchar(255) NOT NULL,
        street_1 varchar(255),
        street_2 varchar(255),
        city varchar(255),
        state_code varchar(255),
        post_code int(5),
        updated timestamp,
        PRIMARY KEY(contact_id));");
$create_contacts->execute();
$create_contacts->close();
/* Customers */
$create_customers = $db_connection->prepare(
	"CREATE OR REPLACE TABLE customers(
        customer_id int NOT NULL AUTO_INCREMENT,
        first_name varchar(50) NOT NULL,
        last_name varchar(50) NOT NULL,
        email varchar(100) NOT NULL UNIQUE,
        phone varchar(20) 
        address varchar(255),
        city varchar(100),
        country varchar(100) NOT NULL DEFAULT 'USA',
        created_at TIMESTAMP NOT NULL DEFAULT NOW()
        PRIMARY KEY(customer_id));");
$create_customers->execute();
$create_customers->close();


/* Below Are Tables That Have Foreign Keys */
/* ------------------------------------------ */

/* Users */
$create_users = $db_connection->prepare(
	"CREATE OR REPLACE TABLE Users(
        user_id int NOT NULL AUTO_INCREMENT,
        role_id int NOT NULL,
        contact_id int NOT NULL,
	    creation_date timestamp,
	    PRIMARY KEY(user_id),
        FOREIGN KEY(role_id) REFERENCES Roles(role_id),
        FOREIGN KEY(contact_id) REFERENCES Contacts(contact_id));");
$create_users->execute();
$create_users->close();

/* Credentials */
$create_credentials = $db_connection->prepare(
	"CREATE OR REPLACE TABLE Credentials(
        username varchar(255) NOT NULL,
	    user_id int NOT NULL,
        password_salted varchar(255) NOT NULL,
	    PRIMARY KEY(username),
        FOREIGN KEY(user_id) REFERENCES Users(user_id));");
$create_credentials->execute();
$create_credentials->close();

/* Status Display */
echo nl2br("The database tables were successfully created.\r\n");


//-----------------------------------------------------
// Populate Tables of Database
//-----------------------------------------------------

/* Roles */
$insert_role = $db_connection->prepare(
	"INSERT INTO Roles
    	(role_id, role_type) VALUES(?,?);");
$insert_role->bind_param("is", $role_id, $role_title);

$role_id = 1;
$role_title = "administrator";
$insert_role->execute();

$role_id = 2;
$role_title = "user";
$insert_role->execute();

$role_id = 3;
$role_title = "guest";
$insert_role->execute();

$insert_role->close();

/* Customers */ /*EDIT ARUGMENTS BASED OFF OF TABLE*/
$insert_customers = $db_connection->prepare(
	"INSERT INTO customers
		(customer_id, first_name, last_name, email, phone, address, city, country, created_at) VALUES(?,?,?,?,?,?,?,?,?,?,?);");
$insert_customers->bind_param("isssssssss", $customer_id, $first_name, $last_name, $email, $phone, $address,$city, $country,$created_at);
$first_name = "micheal";
$last_name = "jackson";
$email = "michealjackson@example.com";
$phone = "452-999-4629";
$address = "309 Hehe St";
$city = "Billie";
$country = "CA";
$created_at = date("Y-m-d H:i:s");
$insert_customers->execute();


$customer_id = 2;
$first_name = "Tyler";
$last_name = "johnson";
$email = "TylerJohnson@example.com";
$phone = "694-394-6979";
$address = "123 bone St";
$city = "bridgeport";
$country = "CT";
$created_at = date("Y-m-d H:i:s");
$insert_customers->execute();

$customer_id = 3;
$first_name = "Berry";
$last_name = "jones";
$email = "Berryjones@example.com";
$phone = "403-102-5292";
$address = "432 apple St";
$city = "Hamden";
$country = "CA";
$created_at = date("Y-m-d H:i:s");
$insert_customers->execute();

$customer_id = 4;
$first_name = "Jorge";
$last_name = "Sampedro";
$email = "Jorgesampedro@example.com";
$phone = "083-737-1010";
$address = "452 Jersey St";
$city = "Jetson";
$country = "AK";
$created_at = date("Y-m-d H:i:s");
$insert_customers->execute();

$insert_contacts->close();

/* Users */
$insert_users = $db_connection->prepare(
	"INSERT INTO Users
		(user_id, role_id, contact_id, creation_date) VALUES(?,?,?,?);");
$insert_users->bind_param("iiis", $user_id, $role_id, $contact_id, $creation_date);
$user_id = 1;
$role_id = 1;
$contact_id = 1;
$creation_date = date("Y-m-d H:i:s");
$insert_users->execute();

$user_id = 2;
$role_id = 2;
$contact_id = 2;
$creation_date = date("Y-m-d H:i:s");
$insert_users->execute();

$user_id = 3;
$role_id = 3;
$contact_id = 3;
$creation_date = date("Y-m-d H:i:s");
$insert_users->execute();

$insert_users->close();



/* Credentials */
$insert_credentials = $db_connection->prepare(
	"INSERT INTO Credentials
		(username, user_id, password_salted) VALUES(?,?,?);");
$insert_credentials->bind_param("sis", $username, $user_id, $password_salted);
$username = "johndoe";
$user_id = 1;
$password_salted = crypt("SCSU2024", $salt);
//$password_salted = password_hash("password123", PASSWORD_DEFAULT);
$insert_credentials->execute();

$user_id = 2;
$username = "janesmith";
$password_salted = crypt("SCSU2025", $salt);
//$password_salted = password_hash("mypassword", PASSWORD_DEFAULT);
$insert_credentials->execute();

$user_id = 3;
$username = "charliebrown";
$password_salted = crypt("SCSU2026", $salt);
//$password_salted = password_hash("charlie123", PASSWORD_DEFAULT);
$insert_credentials->execute();

$insert_credentials->close();




/* Status Display */
echo nl2br("The database tables were successfully populated.\r\n");
/* Return to homepage after 5 seconds */
header( "refresh:10;url=/csc235_login" );

/* ALWAYS CLOSE THE DB CONNECTION */
$db_connection->close();

?>
