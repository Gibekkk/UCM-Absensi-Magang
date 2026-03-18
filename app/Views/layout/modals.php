<!-- Modal Error -->
<div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="errorModalTitle">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="errorModalBody">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
  function showError(title, message) {
    document.getElementById('errorModalBody').innerText = message;
    document.getElementById('errorModalTitle').innerText = title;
    var myModal = new bootstrap.Modal(document.getElementById('errorModal'));
    myModal.show();
  }
</script>


<!-- Student Modal (Add & Edit) -->
<div class="modal fade" id="studentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="studentForm">
        <div class="modal-header">
          <h5 class="modal-title" id="modalTitle">Add Student</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="studentId">
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
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this student? This action cannot be undone.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
      </div>
    </div>
  </div>
</div>