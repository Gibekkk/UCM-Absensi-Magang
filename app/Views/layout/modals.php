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
            <label for="internship_id">Internship Assignment</label>
            <select name="internship_id" id="internship_id" class="form-control" required>
            </select>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" value="" id="is_active_student" name="is_active">
            <label class="form-check-label" for="is_active_student">
              Is Active
            </label>
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
          <div class="form-check">
            <input class="form-check-input" type="checkbox" value="" id="is_active_internship" name="is_active">
            <label class="form-check-label" for="is_active_internship">
              Is Active
            </label>
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

<!-- Modal Container -->
<div id="notifModal" class="modal-overlay" style="display: none;">
    <div class="glass-card modal-content">
        <!-- Icon Container -->
        <div id="modalIcon" class="icon-box">
            <!-- SVG akan diisi via JS -->
        </div>
        <h3 id="modalTitle">Success</h3>
        <p id="modalMessage">Attendance recorded successfully!</p>
        
        <!-- Duration Bar -->
        <div class="progress-container">
            <div id="progressBar" class="progress-bar"></div>
        </div>
    </div>
</div>

<style>
    .modal-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.6); display: flex; align-items: center; justify-content: center;
        z-index: 9999; backdrop-filter: blur(5px);
    }
    .modal-content {
        padding: 2rem; text-align: center; color: white; width: 300px;
        animation: fadeIn 0.3s ease-out;
    }
    .icon-box { font-size: 4rem; margin-bottom: 1rem; }
    .progress-container { width: 100%; height: 6px; background: rgba(255,255,255,0.1); border-radius: 3px; margin-top: 15px; overflow: hidden; }
    .progress-bar { height: 100%; width: 100%; background: #00ff88; animation: shrink 3s linear forwards; }
    
    @keyframes fadeIn { from { opacity: 0; transform: scale(0.9); } to { opacity: 1; transform: scale(1); } }
    @keyframes shrink { from { width: 100%; } to { width: 0%; } }
</style>