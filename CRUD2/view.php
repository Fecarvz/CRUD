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
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
  <title>25ae6246</title>
  
</head>
<body>
  <h1>Profile information</h1>
  <?php
  if ( isset( $_SESSION["error"] ) ) {
    echo('<p style="color: red;">'.$_SESSION['error']."</p>\n");
    unset($_SESSION["error"]);
  }
?>
  <p>First Name: <?php echo $f ?></p>
  <p>Last Name: <?php echo $l ?></p>
  <p>Email: <?php echo $e ?></p>
  <p>Headline:<br/> <?php echo $h ?></p>
  <p>Summary: <br/>
  <?php echo $s ?>
  </p>

  <?php 
  $stmt = $pdo->prepare("SELECT * FROM position WHERE profile_id = :profile_id");
  $stmt->execute(array(
    ':profile_id' => $_GET['profile_id']
  ));
  $positions = $stmt->fetchAll(PDO::FETCH_ASSOC);
  if(!empty($positions)){
    echo "<p>Position: <br/>";
    echo "<ul>";
    foreach($positions as $position){
      echo "<li>";
      echo htmlentities($position['year']).": ".htmlentities($position['description']);
      echo "</li>";
    }
    echo "</ul>";
    echo "</p>";
  }
  ?>
  <a href="index.php">Done</a>
</body>
</html>

