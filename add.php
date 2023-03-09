<?php
require_once "pdo.php";
session_start();
if ( isset( $_POST['name'] )  && isset( $_POST['email'] ) && isset( $_POST['password'] ) ) {
  $sql = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(
    ':name' => $_POST['name'],
    ':email' => $_POST['email'],
    ':password' => $_POST['password']
  ));
  $_SESSION['success'] = 'Record Added';
  header( 'Location: index.php' ) ;
  return;
}
?>
<p>Add a New User</p>
<form method="post">
<p>Name:<input type="text" name="name" size="40"></p>
<p>Email:<input type="text" name="email"></p>
<p>Password:<input type="password" name="password"></p>
<p><input type="submit" value="Add New"/>
<a href="index.php">Cancel</a>