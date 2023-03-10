<?php
session_start();
unset($_SESSION['account']);
unset($_SESSION['user_id']);
header('Location: index.php');
return;
?>