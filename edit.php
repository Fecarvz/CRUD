<?php
  session_start();
  require_once "pdo.php";
  if(!isset($_SESSION['account'])){
    die('ACCESS DENIED');
  }
  if ( isset($_POST['cancel']) ) {
    header('Location: index.php');
    return;
  }
  if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary']) ) {
    if ( strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1 ) {
      $_SESSION['error'] = "All fields are required";
      header('Location: edit.php?profile_id='.$_POST['profile_id']);
      return;
    }elseif ( strpos($_POST['email'], '@') === false ) {
      $_SESSION['error'] = "Email address must contain @";
      header('Location: edit.php?profile_id='.$_POST['profile_id']);
      return;
    }else{
      $sql = "UPDATE profile SET first_name = :first_name, last_name = :last_name, email = :email, headline = :headline, summary = :summary WHERE profile_id = :profile_id";
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(
        ':first_name' => $_POST['first_name'],
        ':last_name' => $_POST['last_name'],
        ':email' => $_POST['email'],
        ':headline' => $_POST['headline'],
        ':summary' => $_POST['summary'],
        ':profile_id' => $_POST['profile_id']
      ));
      $_SESSION['success'] = "Profile updated";
      header('Location: index.php');
      return;
    }
  }

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
  <h1>Editing Profile for UMSI</h1>
  <?php
  if ( isset( $_SESSION["error"] ) ) {
    echo('<p style="color: red;">'.$_SESSION['error']."</p>\n");
    unset($_SESSION["error"]);
  }
?>
  <form method="post">
    <p>First Name:<input type="text" name="first_name" size="60" value="<?php echo $f ?>"></p>
    <p>Last Name:<input type="text" name="last_name" size="60" value="<?php echo $l ?>"></p>
    <p>Email:<input type="text" name="email" size="60" value="<?php echo $e ?>"></p>
    <p>Headline: <br/><input type="text" name="headline" size="60" value="<?php echo $h ?>"></p>
    <p>Summary: <br/>
    <textarea name="summary" rows="8" cols="80"><?php echo $s ?></textarea>
    </p>
    <input type="hidden" name="profile_id" value="<?php echo $profile_id ?>">
    <p><input type="submit" name='save' value="Save"/><input type="submit" name='cancel'value="cancel"/></p>
  </form>
</body>
</html>

