<?php
  require_once "pdo.php";
  session_start();
?>
<html>
  <head>
    <title>25ae6246</title>
  </head>
  <body>
    <h1>Felipe Carvalho Resume Registry</h1>
    
    <?php
    if( isset( $_SESSION['error'] ) ) { 
      echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
      unset($_SESSION['error']);
    }
    if( isset( $_SESSION['success'] ) ) {
      echo('<p style="color: green;">'.htmlentities($_SESSION['success'])."</p>\n");
      unset($_SESSION['success']);
    }
      if( ! isset( $_SESSION['account'] ) && ! isset( $_SESSION['user_id']))  {
        echo('<p><a href="login.php">Please log in</a></p>');
      } elseif( isset( $_SESSION['account'] ) && isset( $_SESSION['user_id'])) {
        echo('<p><a href="logout.php">Logout</a></p>');
        echo('<p><a href="add.php">Add New Entry</a></p>');
      }
    ?>
    <table border="1">
      <tr>
        <th>Name</th>
        <th>Headline</th>
        <?php
          if( isset( $_SESSION['account'] ) ) {
            echo('<th>Action</th>');
          }
        ?>
      </tr>
      <?php
        $stmt = $pdo->query("SELECT * FROM profile");
        while( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
          echo('<tr><td>');
          echo('<a href="view.php?profile_id='.$row['profile_id'].'">');
          echo(htmlentities($row['first_name']).' '.htmlentities($row['last_name']));
          echo('</a>');
          echo('</td><td>');
          echo(htmlentities($row['headline']));
          echo('</td>');
          if( isset( $_SESSION['account'] ) ) {
            echo('<td>');
            echo('<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a> / ');
            echo('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>');
          }
          echo('</td></tr>');
        }
      ?>
    </table>
