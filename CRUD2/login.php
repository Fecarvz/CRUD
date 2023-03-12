<?php 
require_once "pdo.php";
session_start();
if ( isset($_POST['cancel'] ) ) {
    header("Location: index.php");
    return;
}

$salt = 'XyZzy12*_';

// Check to see if we have some POST data, if we do process it
if ( isset($_POST['email']) && isset($_POST['pass']) ) {
  $check = hash('md5', $salt.$_POST['pass']);
  $stmt = $pdo->prepare("SELECT user_id, password, name FROM users WHERE email = :em");
$stmt->execute(array(
  ':em' => $_POST['email']
));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row === false) {
  $_SESSION['error'] = "Email not found";
} else {
  $stored_hash = $row['password'];
  $user_id = $row['user_id'];
  $name = $row['name'];
  if ($check == $stored_hash) {
    $_SESSION['account'] = $name;
    if (!empty($user_id)) {
      $_SESSION['user_id'] = $user_id;
    }
    header("Location: index.php");
    return;
  } else {
    $_SESSION['error'] = "Incorrect password";
    error_log("Login fail ".$_POST['email']." $check");
    header("Location: login.php");
    return;
  }
}

  if ( strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1 ) {
    $_SESSION['error'] = "Email and password are required";
  } elseif ( !strpos($_POST['email'], '@' ) ) {
    $_SESSION['error']  = "Email must have an at-sign (@)";
  }  elseif($stored_hash === false){
     $_SESSION['error'] = "Email not found";
  }
  else {
      if ( $check == $stored_hash ) {
          $_SESSION['account'] = $_POST['email'];
          error_log("Login success ".$_POST['email']);
          header("Location: index.php");
          return;
      } else {
          $_SESSION['error'] = "Incorrect password";
          error_log("Login fail ".$_POST['email']." $check");
          header("Location: login.php");
          return;
      }
  }
}

?>
<!DOCTYPE html>
<html>
<head>
  <title>25ae6246</title>
</head>
<body>
<div class="container">
<h1>Please Log In</h1>
<?php
if ( isset( $_SESSION["error"] ) ) {
    echo('<p style="color: red;">'.$_SESSION['error']."</p>\n");
    unset($_SESSION["error"]);
}
?>
<form method="POST">
<label for="nam">Email</label>
<input type="text" name="email" id="nam"><br/>
<label for="id_1723">Password</label>
<input type="text" name="pass" id="id_1723"><br/>
<input type="submit" value="Log In">
<input type="submit" name="cancel" value="Cancel">
</form>
</div>
<script>
  const form = document.querySelector('form');

form.addEventListener('submit', (event) => {
  event.preventDefault();

 
  const email = form.elements['email'].value;
  const password = form.elements['pass'].value;


  if (!email || !password) {
    alert('Por favor, preencha todos os campos obrigatórios!');
    return;
  }

  // valida o campo de e-mail
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    alert('Por favor, forneça um endereço de e-mail válido!');
    return;
  }

  // envia o formulário se tudo estiver válido
  form.submit();
});
</script>
</body>
</html>
