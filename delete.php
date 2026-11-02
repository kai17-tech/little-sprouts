<?php
// =========================================================
// delete.php - DELETE: remove a student record
// Called by the "Yes, Delete Record" button (method POST)
// =========================================================
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id = (int) ($_POST['StudentID'] ?? 0);

$stmt = $pdo->prepare('DELETE FROM student WHERE StudentID = ?');
$stmt->execute([$id]);

if ($stmt->rowCount() > 0) {
    flash('success', "Student $id was deleted.");
} else {
    flash('error', "Student $id was not found.");
}

header('Location: index.php');
exit;
