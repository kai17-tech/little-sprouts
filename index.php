<?php
// =========================================================
// index.php - READ: dashboard, search, filters, table, pagination
// Also holds the View / Add-Edit / Delete modal screens.
// =========================================================
require 'config.php';

// ---------- 1. Read the search + filter values from the URL ----------
$search  = trim($_GET['search'] ?? '');
$grade   = $_GET['grade'] ?? '';
$status  = $_GET['status'] ?? '';
$page    = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 15;

// ---------- 2. Build the WHERE part of the query ----------
$where  = [];
$params = [];

if ($search !== '') {
    $where[] = '(CAST(StudentID AS CHAR) LIKE ? OR FirstName LIKE ? OR LastName LIKE ?)';
    $like = "%$search%";
    array_push($params, $like, $like, $like);
}
if (in_array($grade, $GRADE_LEVELS, true)) {
    $where[]  = 'GradeLevel = ?';
    $params[] = $grade;
}
if (in_array($status, $STATUSES, true)) {
    $where[]  = 'Status = ?';
    $params[] = $status;
}
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// ---------- 3. Count matching rows (for pagination) ----------
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM student $whereSql");
$countStmt->execute($params);
$filteredTotal = (int) $countStmt->fetchColumn();

$totalPages = max(1, (int) ceil($filteredTotal / $perPage));
$page       = min($page, $totalPages);
$offset     = ($page - 1) * $perPage;

// ---------- 4. Get the rows for this page ----------
$stmt = $pdo->prepare("SELECT * FROM student $whereSql ORDER BY StudentID LIMIT $perPage OFFSET $offset");
$stmt->execute($params);
$students = $stmt->fetchAll();

// ---------- 5. Dashboard numbers (whole table, not filtered) ----------
$counts = ['Active' => 0, 'Pending' => 0, 'Inactive' => 0];
foreach ($pdo->query('SELECT Status, COUNT(*) AS total FROM student GROUP BY Status') as $row) {
    $counts[$row['Status']] = (int) $row['total'];
}
$totalStudents = array_sum($counts);

// ---------- 6. One-time message from create/update/delete ----------
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// Builds a link that keeps the current filters but changes the page number
function pageUrl($pageNumber)
{
    $query = $_GET;
    $query['page'] = $pageNumber;
    return 'index.php?' . http_build_query($query);
}

$firstShown = $filteredTotal ? $offset + 1 : 0;
$lastShown  = $offset + count($students);
$statusClass = ['Active' => 'active', 'Pending' => 'pending', 'Inactive' => 'inactive'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard · Little Sprouts</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- ================= SIDEBAR ================= -->
<aside class="sidebar">
    <div class="brand">
        <div class="brand-logo"><i class="fa-solid fa-seedling"></i></div>
        <div>
            <div class="brand-name">Little Sprouts</div>
            <div class="brand-sub">LEARNING CENTER</div>
        </div>
    </div>

    <div class="nav-label">CORE MODULE</div>
    <a href="index.php" class="nav-item active">
        <span class="nav-icon"><i class="fa-solid fa-user-graduate"></i></span>
        Student Directory
    </a>

    <div class="entity-box">
        <span class="entity-icon"><i class="fa-solid fa-table-cells-large"></i></span>
        <div>
            <div class="entity-label">ENTITY TABLE</div>
            <div class="entity-name">Student (<?= $totalStudents ?> Records)</div>
        </div>
    </div>
</aside>

<!-- ================= MAIN ================= -->
<div class="main">
    <header class="topbar">
        <div class="page-title">
            <span class="title-icon"><i class="fa-solid fa-school"></i></span>
            <div>
                <h1>Student Dashboard</h1>
                <p>Manage student enrollment records and attributes (CRUD)</p>
            </div>
        </div>
        <div class="avatar">AD</div>
    </header>

    <main class="content">

        <?php if ($flash): ?>
            <div class="flash <?= e($flash['type']) ?>" id="flash">
                <i class="fa-solid <?= $flash['type'] === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"></i>
                <?= e($flash['message']) ?>
            </div>
        <?php endif; ?>

        <!-- ---------- Stat cards ---------- -->
        <section class="stats">
            <div class="stat-card">
                <div>
                    <div class="stat-label">TOTAL ENROLLED</div>
                    <div class="stat-value"><?= $totalStudents ?></div>
                    <div class="stat-note">Students in database</div>
                </div>
                <span class="stat-icon blue"><i class="fa-solid fa-users"></i></span>
            </div>
            <div class="stat-card">
                <div>
                    <div class="stat-label">ACTIVE STATUS</div>
                    <div class="stat-value green"><?= $counts['Active'] ?></div>
                    <div class="stat-note green">Currently attending</div>
                </div>
                <span class="stat-icon green"><i class="fa-solid fa-user-check"></i></span>
            </div>
            <div class="stat-card">
                <div>
                    <div class="stat-label">PENDING REVIEW</div>
                    <div class="stat-value orange"><?= $counts['Pending'] ?></div>
                    <div class="stat-note orange">Application under review</div>
                </div>
                <span class="stat-icon orange"><i class="fa-solid fa-user-clock"></i></span>
            </div>
            <div class="stat-card">
                <div>
                    <div class="stat-label">INACTIVE STATUS</div>
                    <div class="stat-value gray"><?= $counts['Inactive'] ?></div>
                    <div class="stat-note gray">Withdrawn / Inactive</div>
                </div>
                <span class="stat-icon gray"><i class="fa-solid fa-user-minus"></i></span>
            </div>
        </section>

        <!-- ---------- Table card ---------- -->
        <section class="table-card">
            <form class="toolbar" method="get" action="index.php" id="filterForm">
                <div class="search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="search" value="<?= e($search) ?>"
                           placeholder="Search Student ID, First Name, Last Name...">
                </div>
                <select name="grade" class="filter" onchange="this.form.submit()">
                    <option value="">All Grade Levels</option>
                    <?php foreach ($GRADE_LEVELS as $g): ?>
                        <option value="<?= e($g) ?>" <?= $grade === $g ? 'selected' : '' ?>><?= e($g) ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="status" class="filter" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <?php foreach ($STATUSES as $s): ?>
                        <option value="<?= e($s) ?>" <?= $status === $s ? 'selected' : '' ?>><?= e($s) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="button" class="btn btn-primary add-btn" onclick="openForm('create')">
                    <span class="plus"><i class="fa-solid fa-plus"></i></span> Add New Student
                </button>
            </form>

            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Birth Date</th>
                        <th>Gender</th>
                        <th>Grade Level</th>
                        <th>Status</th>
                        <th class="center">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!$students): ?>
                        <tr><td colspan="8" class="empty">No students match your search.</td></tr>
                    <?php endif; ?>

                    <?php foreach ($students as $s): ?>
                        <?php
                        // The row's data is stored as JSON so the modals can read it with JavaScript
                        $json = e(json_encode($s));
                        ?>
                        <tr>
                            <td class="mono"><?= e($s['StudentID']) ?></td>
                            <td><?= e($s['FirstName']) ?></td>
                            <td><?= e($s['LastName']) ?></td>
                            <td><?= e($s['BirthDate']) ?></td>
                            <td><?= e($s['Gender']) ?></td>
                            <td><?= e($s['GradeLevel']) ?></td>
                            <td><span class="badge <?= $statusClass[$s['Status']] ?>"><?= e($s['Status']) ?></span></td>
                            <td class="actions">
                                <button class="icon-btn view" title="View" onclick='openView(<?= $json ?>)'>
                                    <i class="fa-solid fa-eye"></i></button>
                                <button class="icon-btn edit" title="Edit" onclick='openForm("edit", <?= $json ?>)'>
                                    <i class="fa-solid fa-pen"></i></button>
                                <button class="icon-btn delete" title="Delete" onclick='openDelete(<?= $json ?>)'>
                                    <i class="fa-solid fa-trash-can"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="table-footer">
                <span>Showing <?= $firstShown ?> to <?= $lastShown ?> of <?= $filteredTotal ?> records</span>
                <div class="pager">
                    <a class="page-btn <?= $page <= 1 ? 'disabled' : '' ?>" href="<?= e(pageUrl($page - 1)) ?>">
                        <i class="fa-solid fa-chevron-left"></i> Previous</a>
                    <span class="page-info">Page <?= $page ?> of <?= $totalPages ?></span>
                    <a class="page-btn <?= $page >= $totalPages ? 'disabled' : '' ?>" href="<?= e(pageUrl($page + 1)) ?>">
                        Next <i class="fa-solid fa-chevron-right"></i></a>
                </div>
            </div>
        </section>
    </main>
</div>

<!-- ================= MODAL: VIEW (Read one record) ================= -->
<div class="modal-backdrop" id="viewModal">
    <div class="modal">
        <div class="modal-head">
            <span class="modal-icon blue"><i class="fa-solid fa-id-card"></i></span>
            <h2>Student Profile Details</h2>
            <button class="close-x" onclick="closeModal('viewModal')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <div class="profile-name">
                <div id="vName"></div>
                <code>StudentID: <span id="vId"></span></code>
            </div>
            <div class="detail-grid">
                <div class="detail"><span>BIRTH DATE</span><strong id="vBirth"></strong></div>
                <div class="detail"><span>GENDER</span><strong id="vGender"></strong></div>
                <div class="detail"><span>GRADE LEVEL</span><strong id="vGrade"></strong></div>
                <div class="detail"><span>STATUS</span><strong><span class="badge" id="vStatus"></span></strong></div>
            </div>
        </div>
        <div class="modal-foot">
            <button class="btn btn-soft" onclick="closeModal('viewModal')">Close Profile</button>
        </div>
    </div>
</div>

<!-- ================= MODAL: ADD / EDIT (Create + Update) ================= -->
<div class="modal-backdrop" id="formModal">
    <div class="modal">
        <form method="post" id="studentForm">
            <div class="modal-head">
                <span class="modal-icon" id="formIcon"><i class="fa-solid fa-user-pen"></i></span>
                <div>
                    <h2 id="formTitle">Edit Student Details</h2>
                    <p>Input student details into the database table</p>
                </div>
                <button type="button" class="close-x" onclick="closeModal('formModal')"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body form-grid">
                <input type="hidden" name="back" value="<?= e($_SERVER['QUERY_STRING'] ?? '') ?>">
                <label><span>STUDENT ID (INT) <b>*</b></span>
                    <div class="input-icon">
                        <i class="fa-solid fa-hashtag"></i>
                        <input type="number" name="StudentID" id="fId" min="1" required>
                    </div>
                </label>
                <label><span>GENDER <b>*</b></span>
                    <select name="Gender" id="fGender" required>
                        <option value="">Select gender</option>
                        <?php foreach ($GENDERS as $g): ?><option><?= e($g) ?></option><?php endforeach; ?>
                    </select>
                </label>
                <label><span>FIRST NAME <b>*</b></span>
                    <input type="text" name="FirstName" id="fFirst" maxlength="50" required>
                </label>
                <label><span>LAST NAME <b>*</b></span>
                    <input type="text" name="LastName" id="fLast" maxlength="50" required>
                </label>
                <label><span>BIRTH DATE <b>*</b></span>
                    <input type="date" name="BirthDate" id="fBirth" required>
                </label>
                <label><span>GRADE LEVEL <b>*</b></span>
                    <select name="GradeLevel" id="fGrade" required>
                        <option value="">Select grade</option>
                        <?php foreach ($GRADE_LEVELS as $g): ?><option><?= e($g) ?></option><?php endforeach; ?>
                    </select>
                </label>
                <label class="full"><span>ENROLLMENT STATUS <b>*</b></span>
                    <select name="Status" id="fStatus" required>
                        <option value="Active">Active (Currently Enrolled)</option>
                        <option value="Pending">Pending (Application Under Review)</option>
                        <option value="Inactive">Inactive (Withdrawn)</option>
                    </select>
                </label>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn btn-outline" onclick="closeModal('formModal')">Cancel</button>
                <button type="submit" class="btn btn-primary" id="formSubmit">Update Record</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL: DELETE confirmation ================= -->
<div class="modal-backdrop" id="deleteModal">
    <div class="modal small">
        <form method="post" action="delete.php" class="confirm">
            <input type="hidden" name="StudentID" id="dId">
            <span class="confirm-icon"><i class="fa-solid fa-trash-can"></i></span>
            <h2>Delete Student Record?</h2>
            <p>Are you sure you want to delete <strong id="dName"></strong>
               (ID: <code id="dIdText"></code>) from the database?</p>
            <div class="confirm-actions">
                <button type="button" class="btn btn-outline" onclick="closeModal('deleteModal')">Cancel</button>
                <button type="submit" class="btn btn-danger">Yes, Delete Record</button>
            </div>
        </form>
    </div>
</div>

<script src="app.js"></script>
</body>
</html>
