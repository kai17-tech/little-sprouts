// =========================================================
// app.js - Opens/closes the modal screens and fills them
// with the clicked student's data. No database work here:
// the forms send the data to create.php / update.php / delete.php
// =========================================================

function openModal(id) {
    document.getElementById(id).classList.add('show');
}

function closeModal(id) {
    document.getElementById(id).classList.remove('show');
}

// ---------- VIEW ----------
function openView(s) {
    document.getElementById('vName').textContent   = s.FirstName + ' ' + s.LastName;
    document.getElementById('vId').textContent     = s.StudentID;
    document.getElementById('vBirth').textContent  = s.BirthDate;
    document.getElementById('vGender').textContent = s.Gender;
    document.getElementById('vGrade').textContent  = s.GradeLevel;

    const badge = document.getElementById('vStatus');
    badge.textContent = s.Status;
    badge.className = 'badge ' + s.Status.toLowerCase();

    openModal('viewModal');
}

// ---------- ADD (mode = 'create') or EDIT (mode = 'edit') ----------
function openForm(mode, s) {
    const form   = document.getElementById('studentForm');
    const idBox  = document.getElementById('fId');
    const icon   = document.getElementById('formIcon');
    const isEdit = mode === 'edit';

    form.reset();
    form.action = isEdit ? 'update.php' : 'create.php';
    document.getElementById('formTitle').textContent  = isEdit ? 'Edit Student Details' : 'Add New Student';
    document.getElementById('formSubmit').textContent = isEdit ? 'Update Record' : 'Save Record';
    icon.className = 'modal-icon ' + (isEdit ? 'orange' : 'green');
    icon.innerHTML = isEdit ? '<i class="fa-solid fa-user-pen"></i>' : '<i class="fa-solid fa-user-plus"></i>';

    // The Student ID is the primary key, so it cannot be changed when editing
    idBox.readOnly = isEdit;

    if (isEdit) {
        idBox.value = s.StudentID;
        document.getElementById('fFirst').value  = s.FirstName;
        document.getElementById('fLast').value   = s.LastName;
        document.getElementById('fBirth').value  = s.BirthDate;
        document.getElementById('fGender').value = s.Gender;
        document.getElementById('fGrade').value  = s.GradeLevel;
        document.getElementById('fStatus').value = s.Status;
    } else {
        document.getElementById('fStatus').value = 'Pending';
    }

    openModal('formModal');
}

// ---------- DELETE ----------
function openDelete(s) {
    document.getElementById('dId').value           = s.StudentID;
    document.getElementById('dIdText').textContent = s.StudentID;
    document.getElementById('dName').textContent   = s.FirstName + ' ' + s.LastName;
    openModal('deleteModal');
}

// Close a modal when clicking the dark background or pressing Esc
document.querySelectorAll('.modal-backdrop').forEach(function (backdrop) {
    backdrop.addEventListener('click', function (event) {
        if (event.target === backdrop) backdrop.classList.remove('show');
    });
});
document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
        document.querySelectorAll('.modal-backdrop.show').forEach(m => m.classList.remove('show'));
    }
});

// Hide the success/error message after 4 seconds
const flashBox = document.getElementById('flash');
if (flashBox) setTimeout(() => flashBox.classList.add('hide'), 4000);
