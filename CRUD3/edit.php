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
    if(isset($_POST['edu_year1']) && isset($_POST['edu_school1'])){
      for($i=1; $i<=9; $i++) {
        if ( ! isset($_POST['edu_year'.$i]) ) continue;
        if ( ! isset($_POST['edu_school'.$i]) ) continue;
        $edu_year = $_POST['edu_year'.$i];
        $edu_school = $_POST['edu_school'.$i];
        if ( strlen($edu_year) == 0 || strlen($edu_school) == 0 ) {
          $_SESSION['error'] = "All fields are required";
          header('Location: edit.php?profile_id='.$_POST['profile_id']);
          return;
        }
        if ( ! is_numeric($edu_year) ) {
          $_SESSION['error'] = "Education year must be numeric";
          header('Location: edit.php?profile_id='.$_POST['profile_id']);
          return;
        }
        $stmt = $pdo->prepare(' FROM education WHERE profile_id = :pid');
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
    $rank = 1;
    for($i=1; $i<=9; $i++) {
      if ( ! isset($_POST['edu_year'.$i]) ) continue;
      if ( ! isset($_POST['edu_school'.$i]) ) continue;
      $edu_year = $_POST['edu_year'.$i];
      $edu_school = $_POST['edu_school'.$i];
  
      // Verifica se a posição já existe no banco de dados
      $stmt = $pdo->prepare("SELECT * FROM education WHERE profile_id = :profile_id AND rank = :rank");
      $stmt->execute(array(
          ':profile_id' => $_POST['profile_id'],
          ':rank' => $rank
      ));
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
  
      // Se a posição já existe, atualiza-a
      if ($row !== false) {
          $stmt = $pdo->prepare("SELECT * FROM institution WHERE name = :name");
          $stmt->execute(array(
              ':name' => $edu_school
          ));
          $institution = $stmt->fetch(PDO::FETCH_ASSOC);
          if($instituition === false){
              $stmt = $pdo->prepare("INSERT INTO institution (name) VALUES (:name)");
              $stmt->execute(array(
                  ':name' => $edu_school
              ));

              $stmt = $pdo->prepare("SELECT * FROM institution WHERE name = :name");
              $stmt->execute(array(
                  ':name' => $edu_school
              ));
              $institution = $stmt->fetch(PDO::FETCH_ASSOC);
          }

          $stmt = $pdo->prepare("UPDATE education SET year = :year, institution_id = :iid WHERE profile_id = :profile_id AND rank = :rank");
          $stmt->execute(array(
              ':profile_id' => $_POST['profile_id'],
              ':rank' => $rank,
              ':year' => $edu_year,
              ':iid' => $institution['institution_id']
          ));
      } 
      // Se a posição não existe, insere-a
      else {
          $stmt = $pdo->prepare("SELECT * FROM institution WHERE name = :name");
          $stmt->execute(array(
              ':name' => $edu_school
          ));
          $institution = $stmt->fetch(PDO::FETCH_ASSOC);
          if($institution === false){
              $stmt = $pdo->prepare("INSERT INTO institution (name) VALUES (:name)");
              $stmt->execute(array(
                  ':name' => $edu_school
              ));

              $stmt = $pdo->prepare("SELECT * FROM institution WHERE name = :name");
              $stmt->execute(array(
                  ':name' => $edu_school
              ));
              $institution = $stmt->fetch(PDO::FETCH_ASSOC);
          }
          $stmt = $pdo->prepare("INSERT INTO education (profile_id, institution_id, rank, year) VALUES (:profile_id, :iid, :rank, :year)");
          $stmt->execute(array(
              ':profile_id' => $_POST['profile_id'],
              ':iid' => $institution['institution_id'],
              ':rank' => $rank,
              ':year' => $edu_year
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
<title>c591470e</title>
  <?php require_once "head.php"?>
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
    <p>Education: <button id="education_button">+</button></p>
    <div id="education_fields"></div>
    <?php 
    $stmt = $pdo->prepare("SELECT * FROM education WHERE profile_id = :profile_id");
    $stmt->execute(array(
      ':profile_id' => $_GET['profile_id']
    ));
    $educations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $countEdu = 0;
    foreach ($educations as $education) {
      $stmt = $pdo->prepare("SELECT * FROM institution WHERE institution_id = :institution_id");
      $stmt->execute(array(
        ':institution_id' => $education['institution_id']
      ));
      $institution = $stmt->fetch(PDO::FETCH_ASSOC);
      $countEdu++;
      echo '<div id="edu'.$countEdu.'">';
      echo '<p>Year: <input type="text" name="edu_year'.$countEdu.'" value="'.$education['year'].'">';
      echo '<input type="button" value="-" onclick = "$(this).parent().parent().remove()"; removeEducationFromDB('.$education['institution_id'].', '.$education['rank'].', '.$education['profile_id'].');></p>';
      echo '<p>School: <input type="text" size="80" name="edu_school'.$countEdu.'" class="school" value="'.$institution['name'].'"></p>';
      echo '</div>';
    }
    ?>
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

    function removerEducationFromDB(institutionId, rank, profile_id) {
      $.ajax({
        url: 'remover_education.php',
        method: 'POST',
        data: { education_id: educationId },
        success: function(response) {
          alert("REMOVIDO")
          console.log('Education removido com sucesso!');
        },
        error: function(xhr, status, error) {
          console.error('Erro ao remover education: ' + error);
        }
      })
    }  
  
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

      var lastCountEdu = <?php echo $countEdu ?>;
      $("#education_button").click((event) => {
        event.preventDefault();
        if(lastCountEdu >= 9){
          alert("Maximum of nine education entries exceeded");
          return;
        }
        lastCountEdu++;
        $("#education_fields").append(
          '<div id="edu'+lastCountEdu+'"> \
          <p>Year: <input type="text" name="edu_year'+lastCountEdu+'" value="" /> \
          <input type="button" value="-" onclick="$(\'#edu'+lastCountEdu+'\').remove();return false;"></p> \
          <p>School: <input type="text" size="80" name="edu_school'+lastCountEdu+'" class="school" value="" /> \
          </div>');
          $('.school').autocomplete({
            source: "school.php"
          });
      })



     
      
  </script>
</body>
</html>

