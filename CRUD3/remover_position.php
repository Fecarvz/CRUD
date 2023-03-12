<?php
require_once "pdo.php";

// Verifica se o id da posição foi passado como parâmetro
if (!isset($_POST['position_id'])) {
  die("Position ID não fornecido.");
}
// Obtém o id da posição e remove a posição correspondente do banco de dados

$position_id = $_POST['position_id'];
$stmt = $pdo->prepare("DELETE FROM position WHERE position_id = :position_id");
$stmt->execute(array(":position_id" => $position_id));

