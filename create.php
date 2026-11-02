<?php
// =========================================================
// create.php - CREATE: insert a new student record
// Called by the "Add New Student" form (method POST)
// =========================================================
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

[$data, $error] = readStudentForm($GRADE_LEVELS, $GENDERS, $STATUSES);

if ($error === null) {
    // StudentID is the primary key, so it must not exist yet
    $check = $pdo->prepare('SELECT COUNT(*) FROM student WHERE StudentID = ?');
    $check->execute([$data['StudentID']]);
    if ($check->fetchColumn() > 0) {
        $error = "Student ID {$data['StudentID']} already exists.";
    }
}

if ($error !== null) {
    flash('error', $error);
} else {
    $sql = 'INSERT INTO student (StudentID, FirstName, LastName, BirthDate, Gender, GradeLevel, Status)
            VALUES (:StudentID, :FirstName, :LastName, :BirthDate, :Gender, :GradeLevel, :Status)';
    $stmt = $pdo->prepare($sql);   // prepared statement = safe from SQL injection
    $stmt->execute($data);
    flash('success', "{$data['FirstName']} {$data['LastName']} was added.");
}

header('Location: index.php');
exit;
