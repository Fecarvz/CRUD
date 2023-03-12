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
    if(isset($_POST['year1']) && isset($_POST['desc1'])){
      for($i=1; $i<=9; $i++) {
        if ( ! isset($_POST['year'.$i]) ) continue;
        if ( ! isset($_POST['desc'.$i]) ) continue;
        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];
        if ( strlen($year) == 0 || strlen($desc) == 0 ) {
          $_SESSION['error'] = "All fields are required";
          header('Location: edit.php?profile_id='.$_POST['profile_id']);
          return;
        }
        if ( ! is_numeric($year) ) {
          $_SESSION['error'] = "Position year must be numeric";
          header('Location: edit.php?profile_id='.$_POST['profile_id']);
          return;
        }
        $stmt = $pdo->prepare(' FROM position WHERE profile_id = :pid');

      }
    }
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
 
      $rank = 1;
      for($i=1; $i<=9; $i++) {
        if ( ! isset($_POST['year'.$i]) ) continue;
        if ( ! isset($_POST['desc'.$i]) ) continue;
        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];
    
        // Verifica se a posição já existe no banco de dados
        $stmt = $pdo->prepare("SELECT * FROM position WHERE profile_id = :profile_id AND rank = :rank");
        $stmt->execute(array(
            ':profile_id' => $_POST['profile_id'],
            ':rank' => $rank
        ));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // Se a posição já existe, atualiza-a
        if ($row !== false) {
            $stmt = $pdo->prepare("UPDATE position SET year = :year, description = :description WHERE profile_id = :profile_id AND rank = :rank");
            $stmt->execute(array(
                ':profile_id' => $_POST['profile_id'],
                ':rank' => $rank,
                ':year' => $year,
                ':description' => $desc
            ));
        } 
        // Se a posição não existe, insere-a
        else {
            $stmt = $pdo->prepare("INSERT INTO position (profile_id, rank, year, description) VALUES (:profile_id, :rank, :year, :description)");
            $stmt->execute(array(
                ':profile_id' => $_POST['profile_id'],
                ':rank' => $rank,
                ':year' => $year,
                ':description' => $desc
            ));
        }
        $rank++;
    }
    
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
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
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
    <p>Position: <button id="position_button">+</button><br></p>
    <div id='position_fields'></div>

    <?php
    $stmt = $pdo->prepare("SELECT * FROM position WHERE profile_id = :profile_id");
    $stmt->execute(array(
      ':profile_id' => $_GET['profile_id']
    ));
    $positions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $countPos = 0;
    foreach ($positions as $position) {
      $countPos++;
      echo '<p>Year: <input type="text" name="year'.$countPos.'" value="'.$position['year'].'">';
      echo '<input type="button" value="-" data-position-id="'.$position['position_id'].'" onclick="$(this).parent().remove();removePositionFromDB('.$position['position_id'].');"><br><br>';
      echo '<textarea name="desc'.$countPos.'" rows="8" cols="80">'.$position['description'].'</textarea></p>';
    }
    
    ?>
    <input type="hidden" name="profile_id" value="<?php echo $profile_id ?>">
    <p><input type="submit" name='save' value="Save"/><input type="submit" name='cancel'value="cancel"/></p>
    </form>
    
    <script>
    function removePositionFromDB(positionId) {
      $.ajax({
        url: 'remover_position.php',
        method: 'POST',
        data: { position_id: positionId },
        success: function(response) {
          console.log('Position removido com sucesso!');
        },
        error: function(xhr, status, error) {
          console.error('Erro ao remover position: ' + error);
        }
      });
    }    

      var lastCountPos = <?php echo $countPos ?>;
      $('#position_button').click(function(event){
        event.preventDefault();
        if(lastCountPos >= 9){
          alert("Maximum of nine position entries exceeded");
          return;
        }
        lastCountPos++;
        $('#position_fields').append(
          '<div id="position'+lastCountPos+'"> \
          <p>Year: <input type="text" name="year'+lastCountPos+'" value="" /> \
          <input type="button" value="-" onclick="$(\'#position'+lastCountPos+'\').remove();return false;"></p> \
          <textarea name="desc'+lastCountPos+'" rows="8" cols="80"></textarea> \
          </div>');
      });
  </script>
</body>
</html>

