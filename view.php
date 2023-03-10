<?php
  session_start();
  require_once "pdo.php";
 

  $stmt = $pdo->prepare("SELECT * FROM profile WHERE profile_id = :profile_id");
  $stmt->execute(array(
    ':profile_id' => $_GET['profile_id']
  ));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if($row === false){
    $_SESSION['error'] = "Bad value for profile_id";
    header('Location: index.php');
    return;
  }
  $f = htmlentities($row['first_name']);
  $l = htmlentities($row['last_name']);
  $e = htmlentities($row['email']);
  $h = htmlentities($row['headline']);
  $s = htmlentities($row['summary']);
  $profile_id = $row['profile_id'];
?>
<html>
<head>
  <title>87f9384a</title>
</head>
<body>
  <h1>Profile information</h1>
  <?php
  if ( isset( $_SESSION["error"] ) ) {
    echo('<p style="color: red;">'.$_SESSION['error']."</p>\n");
    unset($_SESSION["error"]);
  }
?>
  <form method="post">
    <p>First Name: <?php echo $f ?></p>
    <p>Last Name: <?php echo $l ?></p>
    <p>Email: <?php echo $e ?></p>
    <p>Headline:<br/> <?php echo $h ?></p>
    <p>Summary: <br/>
    <?php echo $s ?>
    </p>
    <a href="index.php">Done</a>
  </form>
</body>
</html>

