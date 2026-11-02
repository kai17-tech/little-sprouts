# Little Sprouts – Student CRUD System

PHP + MySQL web app for the **Student** table (7 fields, 30 sample records).

## How to run (XAMPP)

1. Copy the `little-sprouts` folder into `C:\xampp\htdocs\`.
2. Open the XAMPP Control Panel and start **Apache** and **MySQL**.
3. Go to `http://localhost/phpmyadmin` → **Import** → choose `database.sql` → **Go**.
   This creates the `little_sprouts` database, the `student` table and 30 records.
4. Open `http://localhost/little-sprouts/` in the browser.

If your MySQL has a password, change `$password` in `config.php`.

## Table: student

| Field      | Type                                | Notes          |
|------------|-------------------------------------|----------------|
| StudentID  | INT                                 | Primary key    |
| FirstName  | VARCHAR(50)                         | Required       |
| LastName   | VARCHAR(50)                         | Required       |
| BirthDate  | DATE                                | Required       |
| Gender     | ENUM('Male','Female')               | Required       |
| GradeLevel | VARCHAR(20)                         | Kinder–Grade 6 |
| Status     | ENUM('Active','Pending','Inactive') | Default Pending|

## Files and what each one does

| File          | Operation | What it does |
|---------------|-----------|--------------|
| `database.sql`| —         | Creates the database, the table and inserts 30 records. |
| `config.php`  | —         | Connects to MySQL with PDO; shared helper functions (`e()`, `flash()`, `readStudentForm()`). |
| `index.php`   | **Read**  | Dashboard counts, search, grade/status filters, table, pagination (15 per page). Also contains the 3 modal screens. |
| `create.php`  | **Create**| Validates the form, checks the ID is not taken, runs `INSERT`. |
| `update.php`  | **Update**| Validates the form, runs `UPDATE ... WHERE StudentID = ?`. |
| `delete.php`  | **Delete**| Runs `DELETE FROM student WHERE StudentID = ?`. |
| `app.js`      | —         | Opens/closes the modals and fills them with the clicked row's data. |
| `style.css`   | —         | All the design (colors, layout, modals). |

## Demo flow (suggested)

1. **Read** – show the table, the counters, search "Reyes", filter by "Pending", go to page 2, click the eye icon.
2. **Create** – click *Add New Student*, enter ID 1031, save. Counter goes 30 → 31.
3. **Update** – click the pencil on 1031, change Status to Active, *Update Record*. Active count goes up.
4. **Delete** – click the trash on 1031, *Yes, Delete Record*. Back to 30.
5. Optional: refresh phpMyAdmin to prove the data really changed in MySQL.

## Key points to explain

- **PDO prepared statements** (`$pdo->prepare(...)` then `execute(...)`) keep user input separate from SQL, which prevents SQL injection.
- **POST → redirect → GET**: each action file saves to the database, stores a message in the session, then redirects to `index.php`. Refreshing the page will not repeat the action.
- **`e()`** uses `htmlspecialchars` so names are printed safely in HTML.
- **StudentID is read-only when editing** because it is the primary key used in the `WHERE` clause.
- **Search** uses `LIKE '%text%'` on ID, first name and last name; filters add `AND` conditions.
- **Pagination** uses `LIMIT 15 OFFSET (page-1)*15`.
