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
    if(isset($_POST['edu_year1']) && isset($_POST['edu_school1'])){
      for($i=1; $i<=9; $i++) {
        if ( ! isset($_POST['edu_year'.$i]) ) continue;
        if ( ! isset($_POST['edu_school'.$i]) ) continue;
        $edu_year = $_POST['edu_year'.$i];
        $edu_school = $_POST['edu_school'.$i];
        if ( strlen($edu_year) == 0 || strlen($edu_school) == 0 ) {
          $_SESSION['error'] = "All fields are required";
          header("Location: add.php");
          return;
        }
        if ( ! is_numeric($edu_year) ) {
          $_SESSION['error'] = "Education year must be numeric";
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
          $profile_idc = $pdo->lastInsertId();
          $rank = 1;
          for($i=1; $i<=9; $i++) {
              if ( ! isset($_POST['year'.$i]) ) continue;
              if ( ! isset($_POST['desc'.$i]) ) continue;
              $year = $_POST['year'.$i];
              $desc = $_POST['desc'.$i];
              $stmt = $pdo->prepare('INSERT INTO position (profile_id, rank, year, description) VALUES ( :pid, :rank, :year, :desc)');
              $stmt->execute(array(
                  ':pid' => $profile_idc,
                  ':rank' => $rank,
                  ':year' => $year,
                  ':desc' => $desc)
              );
              $rank++;
          }
          $rank = 1;
          for($i=1; $i<=9; $i++) {
              if ( ! isset($_POST['edu_year'.$i]) ) continue;
              if ( ! isset($_POST['edu_school'.$i]) ) continue;
              $edu_year = $_POST['edu_year'.$i];
              $edu_school = $_POST['edu_school'.$i];
              $stmt = $pdo->prepare('SELECT institution_id FROM institution WHERE name = :name');
              $stmt->execute(array(':name' => $edu_school));
              $row = $stmt->fetch(PDO::FETCH_ASSOC);
              if($row !== false){
                $institution_id = $row['institution_id'];
              }else{
                $stmt = $pdo->prepare('INSERT INTO institution (name) VALUES (:name)');
                $stmt->execute(array(':name' => $edu_school));
                $institution_id = $pdo->lastInsertId();
              }
              $stmt = $pdo->prepare('INSERT INTO education (profile_id, institution_id,rank, year) VALUES ( :pid, :iid, :rank, :year)');
              $stmt->execute(array(
                  ':pid' => $profile_idc,
                  ':iid' => $institution_id,
                  ':rank' => $rank,
                  ':year' => $edu_year
              ));
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
  <?php require_once "head.php"; ?>

  <title>c591470e</title>
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
    <p>Education: <button id = 'education_button'>+</button><br></p>
    <div id='education_fields'></div>
    <p>Position: <button id="position_button">+</button><br></p>
    <div id='position_fields'></div>
    <p><input type="submit" value="Add"/><input type="submit" name='cancel'value="cancel"/></p>
  </form>
  
  <script>
    var contador_position = 1;
    var contador_education = 1;
    $("#position_button").click((e) => {
      e.preventDefault()
      if (contador_position < 10) {
      const position = document.createElement("div")
      position.innerHTML = `
        <p>Year: <input type="text" name="year${contador_position}" class="school" value="" />
        <input type="button" value="-" onclick="$(this).closest('p').remove(); return false;"><br><br>
        <textarea name="desc${contador_position}" rows="8" cols="80"></textarea></p>
      `;
      contador_position++
      const position_fields = $("#position_fields")
      position_fields.append(position)
    } else {
      alert("Maximum of nine position entries exceeded")
    }
    })
    $("#education_button").click((e) => {

      e.preventDefault()
      if (contador_education < 10) {
      const education = document.createElement("div")
      education.innerHTML = `
        <p>Year: <input type="text" name="edu_year${contador_education}" value="" />
        <input type="button" value="-" onclick="$(this).closest('p').remove(); return false;"><br><br>School: 
        <input type=text name="edu_school${contador_education}" class="school"></p>
      `;
      contador_education++
      const education_fields = $("#education_fields")
      education_fields.append(education)
    } else {
      alert("Maximum of nine education entries exceeded")
    }
    })
    $("#education_fields").on("focus", ".school", function() {
      $(this).autocomplete({
    source: "school.php"
  });
});

 


  
  </script>
</body>
</html>

