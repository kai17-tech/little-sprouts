<?php
// =========================================================
// update.php - UPDATE: save changes to an existing student
// Called by the "Edit Student Details" form (method POST)
// =========================================================
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

[$data, $error] = readStudentForm($GRADE_LEVELS, $GENDERS, $STATUSES);

if ($error !== null) {
    flash('error', $error);
} else {
    // StudentID is the primary key, so it is used to find the row (it is read-only in the form)
    $sql = 'UPDATE student
               SET FirstName = :FirstName,
                   LastName  = :LastName,
                   BirthDate = :BirthDate,
                   Gender    = :Gender,
                   GradeLevel = :GradeLevel,
                   Status    = :Status
             WHERE StudentID = :StudentID';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);
    flash('success', "Student {$data['StudentID']} was updated.");
}

// Go back to the same page / filters the user was on
$back = $_POST['back'] ?? '';
header('Location: index.php' . ($back !== '' ? '?' . $back : ''));
exit;
