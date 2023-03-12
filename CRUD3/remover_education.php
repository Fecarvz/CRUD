<?php
require_once "pdo.php";

// Verifica se o id da posição foi passado como parâmetro
if (!isset($_POST['education_id'])) {
  die("Education ID não fornecido.");
}
// Obtém o id da posição e remove a posição correspondente do banco de dados

$profile_id = $_POST['profile_id'];
$institution_id = $_POST['institution_id'];
$rank = $_POST['rank'];

$stmt = $pdo->prepare("DELETE FROM education WHERE rank = :rank and profile_id = :profile_id and institution_id = :institution_id");

$stmt->execute(array(":rank" => $rank, ":profile_id" => $profile_id, ":institution_id" => $institution_id));
