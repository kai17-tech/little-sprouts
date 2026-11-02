<?php
// =========================================================
// config.php - Database connection (shared by every page)
// =========================================================

session_start(); // used for the success / error messages after an action

// Change these if your MySQL setup is different
// (port: check the "Server: 127.0.0.1:____" line at the top of phpMyAdmin)
$host     = '127.0.0.1';
$port     = 3306;
$dbname   = 'little_sprouts';
$username = 'root';
$password = '';

try {
    // PDO = PHP Data Objects, the safe way to talk to MySQL
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);            // throw errors
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);       // rows as arrays
} catch (PDOException $ex) {
    die('Database connection failed: ' . $ex->getMessage());
}

// Allowed values for the dropdowns (also used to validate input)
$GRADE_LEVELS = ['Kinder', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'];
$GENDERS      = ['Male', 'Female'];
$STATUSES     = ['Active', 'Pending', 'Inactive'];

// Escape text before printing it in HTML (prevents XSS)
function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

// Save a message to show once on the next page load
function flash($type, $message)
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

// Read the student fields from a submitted form and check them.
// Returns [data, errorMessage]. errorMessage is null when everything is valid.
function readStudentForm($GRADE_LEVELS, $GENDERS, $STATUSES)
{
    $data = [
        'StudentID'  => trim($_POST['StudentID'] ?? ''),
        'FirstName'  => trim($_POST['FirstName'] ?? ''),
        'LastName'   => trim($_POST['LastName'] ?? ''),
        'BirthDate'  => trim($_POST['BirthDate'] ?? ''),
        'Gender'     => $_POST['Gender'] ?? '',
        'GradeLevel' => $_POST['GradeLevel'] ?? '',
        'Status'     => $_POST['Status'] ?? '',
    ];

    if (!ctype_digit($data['StudentID']))                     return [$data, 'Student ID must be a whole number.'];
    if ($data['FirstName'] === '' || $data['LastName'] === '') return [$data, 'First and last name are required.'];
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['BirthDate'])) return [$data, 'Please enter a valid birth date.'];
    if (!in_array($data['Gender'], $GENDERS, true))           return [$data, 'Please choose a gender.'];
    if (!in_array($data['GradeLevel'], $GRADE_LEVELS, true))  return [$data, 'Please choose a grade level.'];
    if (!in_array($data['Status'], $STATUSES, true))          return [$data, 'Please choose an enrollment status.'];

    $data['StudentID'] = (int) $data['StudentID'];
    return [$data, null];
}
