<!-- Modal Error -->
<div class="modal fade" id="alertModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="alertModalTitle">Modal title</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="alertModalBody">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
</div>

<script>
  function showAlert(title, message) {
    document.getElementById('alertModalBody').innerText = message;
    document.getElementById('alertModalTitle').innerText = title;
    var myModal = new bootstrap.Modal(document.getElementById('alertModal'));
    myModal.show();
  }
</script>


<!-- Student Modal (Add & Edit) -->
<div class="modal fade" id="studentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="studentForm">
        <input type="hidden" name="studentId" id="studentId" value="">
        <div class="modal-header">
          <h5 class="modal-title" id="modalTitle">Add Student</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>NIM</label>
            <input type="text" name="nim" id="nim" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Name</label>
            <input type="text" name="full_name" id="full_name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Major</label>
            <input type="text" name="major" id="major" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Sub Major</label>
            <input type="text" name="sub_major" id="sub_major" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="department_name">Departments</label>
            <select name="department_name" id="department_name" class="form-control" required>
            </select>
          </div>
          <div class="mb-3">
            <label for="internship_id">Internship Name</label>
            <select name="internship_id" id="internship_id" class="form-control" required>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Internship Modal (Add & Edit) -->
<div class="modal fade" id="internshipModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="internshipForm">
        <input type="hidden" name="internshipId" id="internshipId" value="">
        <div class="modal-header">
          <h5 class="modal-title" id="modalTitle">Add Internship</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" id="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Department</label>
            <input type="text" name="department" id="department" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Head of Department</label>
            <input type="text" name="head_department" id="head_department" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Start Date</label>
            <input type="date" name="start_date" id="start_date" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>End Date</label>
            <input type="date" name="end_date" id="end_date" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- User Modal (Add & Edit) -->
<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="userForm">
        <input type="hidden" name="userId" id="userId" value="">
        <div class="modal-header">
          <h5 class="modal-title" id="modalTitle">Add User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Name</label>
            <input type="text" name="full_name" id="full_name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" id="username" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" id="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Phone Number</label>
            <input type="tel" name="phone_number" id="phone_number" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" id="password" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="deleteModalBody">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
      </div>
    </div>
  </div>
</div>

