<?php
session_start();
require_once "pdo.php";
if(!isset($_SESSION['account'])){
  die('ACCESS DENIED');
}else{

  if ( isset($_POST['cancel']) ) {
      header('Location: index.php');
      return;
  }

  if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])  ) {
    if(isset($_POST['year1']) && isset($_POST['desc1'])){
      for($i=1; $i<=9; $i++) {
        if ( ! isset($_POST['year'.$i]) ) continue;
        if ( ! isset($_POST['desc'.$i]) ) continue;
        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];
        if ( strlen($year) == 0 || strlen($desc) == 0 ) {
          $_SESSION['error'] = "All fields are required";
          header("Location: add.php");
          return;
        }
        if ( ! is_numeric($year) ) {
          $_SESSION['error'] = "Position year must be numeric";
          header("Location: add.php");
          return;
        }
      }
    }
      if ( strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1 ) {
          $_SESSION['error'] = "All fields are required";
          header("Location: add.php");
          return;
      } elseif ( strpos($_POST['email'], '@') === false ) {
          $_SESSION['error'] = "Email address must contain @";
          header("Location: add.php");
          return;
      } else {
          $stmt = $pdo->prepare('INSERT INTO profile (user_id, first_name, last_name, email, headline, summary) VALUES ( :uid, :fn, :ln, :em, :he, :su)');
          $stmt->execute(array(
              ':uid' => $_SESSION['user_id'],
              ':fn' => $_POST['first_name'],
              ':ln' => $_POST['last_name'],
              ':em' => $_POST['email'],
              ':he' => $_POST['headline'],
              ':su' => $_POST['summary'])
          );
          $rank = 1;
          for($i=1; $i<=9; $i++) {
              if ( ! isset($_POST['year'.$i]) ) continue;
              if ( ! isset($_POST['desc'.$i]) ) continue;
              $year = $_POST['year'.$i];
              $desc = $_POST['desc'.$i];
              $stmt = $pdo->prepare('INSERT INTO position (profile_id, rank, year, description) VALUES ( :pid, :rank, :year, :desc)');
              $stmt->execute(array(
                  ':pid' => $pdo->lastInsertId(),
                  ':rank' => $rank,
                  ':year' => $year,
                  ':desc' => $desc)
              );
              $rank++;
          }
          $_SESSION['success'] = "Profile added";
          header("Location: index.php");
          return;
      }
  }
}

?>
<html>
<head>
  <title>25ae6246</title>
   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
</head>
<body>
  <h1>Adding Profile for UMSI</h1>
  <?php
  if ( isset( $_SESSION["error"] ) ) {
    echo('<p style="color: red;">'.$_SESSION['error']."</p>\n");
    unset($_SESSION["error"]);
  }
?>
  <form method="post">
    <p>First Name:<input type="text" name="first_name" size="60"></p>
    <p>Last Name:<input type="text" name="last_name" size="60"></p>
    <p>Email:<input type="text" name="email" size="60"></p>
    <p>Headline: <br/><input type="text" name="headline" size="60"></p>
    <p>Summary: <br/>
    <textarea name="summary" rows="8" cols="80"></textarea>
    </p>
    <p>Position: <button id="position_button">+</button><br></p>
    <div id='position_fields'></div>
    <p><input type="submit" value="Add"/><input type="submit" name='cancel'value="cancel"/></p>
  </form>
  
  <script>
    var contador = 1;
    $("#position_button").click((e) => {
      e.preventDefault()
      if (contador < 10) {
      const position = document.createElement("div")
      position.innerHTML = `
        <p>Year: <input type="text" name="year${contador}" value="" />
        <input type="button" value="-" onclick="$(this).closest('p').remove(); return false;"><br><br>
        <textarea name="desc${contador}" rows="8" cols="80"></textarea></p>
      `;
      contador++
      const position_fields = $("#position_fields")
      position_fields.append(position)
    } else {
      alert("Maximum of nine position entries exceeded")
    }
    })
  
  </script>
</body>
</html>

