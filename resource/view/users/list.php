<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Danh sách người dùng - Bootstrap 5 (CRUD - Frontend)</title>
  <!-- Bootstrap 5 CSS (CDN) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h3">Danh sách người dùng</h1>
      <div>
        <button id="btnAdd" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal">Thêm người dùng</button>
      </div>
    </div>

    <!-- Table -->
    <div class="card shadow-sm">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th style="width:70px">ID</th>
                <th>Họ tên</th>
                <th>Email</th>
                <th style="width:160px">Mật khẩu</th>
                <th style="width:160px">Hành động</th>
              </tr>
            </thead>
            <tbody id="usersTableBody">
              <!-- Rendered by JS -->
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <p class="text-muted mt-3 small">Lưu ý: mật khẩu chỉ được <strong>hiển thị dạng che</strong> ở bảng. Khi sửa/tao mới, bạn có thể nhập mật khẩu (ở ví dụ frontend này sẽ lưu tạm trong bộ nhớ trang).</p>
  </div>

  <!-- Modal: Add / Edit User -->
  <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="userForm" class="needs-validation" novalidate>
          <div class="modal-header">
            <h5 class="modal-title" id="userModalLabel">Thêm người dùng</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="userId">

            <div class="mb-3">
              <label for="userName" class="form-label">Họ tên</label>
              <input type="text" class="form-control" id="userName" required>
              <div class="invalid-feedback">Vui lòng nhập họ tên.</div>
            </div>

            <div class="mb-3">
              <label for="userEmail" class="form-label">Email</label>
              <input type="email" class="form-control" id="userEmail" required>
              <div class="invalid-feedback">Vui lòng nhập email hợp lệ.</div>
            </div>

            <div class="mb-3">
              <label for="userPassword" class="form-label">Mật khẩu</label>
              <div class="input-group">
                <input type="password" class="form-control" id="userPassword" placeholder="(để trống nếu không đổi khi sửa)">
                <button class="btn btn-outline-secondary" type="button" id="togglePassword">Hiện</button>
              </div>
              <div class="form-text">Mật khẩu không hiển thị trong bảng để bảo mật.</div>
            </div>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            <button type="submit" class="btn btn-primary">Lưu</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Bootstrap 5 JS (bundle includes Popper) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // References
    let tbody, userModalElement, userModal, userForm, userModalLabel;
    let inputId, inputName, inputEmail, inputPassword, togglePasswordBtn;
    
    // Initialize after DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
      tbody = document.getElementById('usersTableBody');
      userModalElement = document.getElementById('userModal');
      userModal = new bootstrap.Modal(userModalElement);
      userForm = document.getElementById('userForm');
      userModalLabel = document.getElementById('userModalLabel');
      inputId = document.getElementById('userId');
      inputName = document.getElementById('userName');
      inputEmail = document.getElementById('userEmail');
      inputPassword = document.getElementById('userPassword');
      togglePasswordBtn = document.getElementById('togglePassword');
      
      console.log('Modal element:', userModalElement);
      console.log('Modal instance:', userModal);
      
      // Load users after DOM is ready
      loadUsers();
      
      // Add event listeners
      document.getElementById('btnAdd').addEventListener('click', () => {
        userModalLabel.textContent = 'Thêm người dùng';
        inputId.value = '';
        userForm.classList.remove('was-validated');
        inputName.value = '';
        inputEmail.value = '';
        inputPassword.value = '';
      });

      togglePasswordBtn.addEventListener('click', () => {
        if (inputPassword.type === 'password') {
          inputPassword.type = 'text';
          togglePasswordBtn.textContent = 'Ẩn';
        } else {
          inputPassword.type = 'password';
          togglePasswordBtn.textContent = 'Hiện';
        }
      });

      userForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        e.stopPropagation();
        userForm.classList.add('was-validated');
        if (!userForm.checkValidity()) return;

        const id = inputId.value ? Number(inputId.value) : null;
        const payload = {
          name: inputName.value.trim(),
          email: inputEmail.value.trim(),
          password: inputPassword.value
        };

        if (id) {
          if (!payload.password) delete payload.password; // keep existing if empty
          await updateUser(id, payload);
        } else {
          if (!payload.password) {
            alert('Vui lòng nhập mật khẩu khi tạo người dùng mới.');
            return;
          }
          await addUser(payload);
        }

        userModal.hide();
      });
    });

    const API_BASE = '<?= getBaseUrl() ?>api/users';
    let users = [];

    function maskPassword() {
      return '•'.repeat(8);
    }

    function renderUsers() {
      console.log('Rendering users:', users);
      tbody.innerHTML = '';
      
      if (users.length === 0) {
        console.log('No users to render');
        return;
      }
      
      users.forEach((u, index) => {
        console.log(`Rendering user ${index + 1}:`, u);
        const tr = document.createElement('tr');

        const tdId = document.createElement('td');
        tdId.textContent = u.id;
        tr.appendChild(tdId);

        const tdName = document.createElement('td');
        tdName.textContent = u.name;
        tr.appendChild(tdName);

        const tdEmail = document.createElement('td');
        tdEmail.textContent = u.email;
        tr.appendChild(tdEmail);

        const tdPassword = document.createElement('td');
        tdPassword.textContent = maskPassword();
        tr.appendChild(tdPassword);

        const tdActions = document.createElement('td');
        tdActions.innerHTML = `
          <div class="btn-group" role="group">
            <button class="btn btn-sm btn-outline-primary btn-edit" data-id="${u.id}">Sửa</button>
            <button class="btn btn-sm btn-outline-danger btn-delete" data-id="${u.id}">Xóa</button>
          </div>
        `;
        tr.appendChild(tdActions);

        tbody.appendChild(tr);
      });

      // Gắn event listener cho nút sửa và xóa
      tbody.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', e => {
          e.preventDefault();
          const id = e.currentTarget.dataset.id;
          console.log('Edit button clicked for user ID:', id, 'Type:', typeof id);
          openEditModal(id);
        });
      });
      tbody.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', e => {
          e.preventDefault();
          const id = e.currentTarget.dataset.id;
          console.log('Delete button clicked for user ID:', id, 'Type:', typeof id);
          deleteUser(id);
        });
      });
    }

    async function loadUsers() {
      try {
        console.log('Loading users from:', API_BASE);
        const res = await fetch(API_BASE, { headers: { 'Accept': 'application/json' } });
        console.log('Response status:', res.status);
        const json = await res.json();
        console.log('Response data:', json);
        users = Array.isArray(json.data) ? json.data : [];
        console.log('Users loaded:', users);
        renderUsers();
      } catch (e) {
        console.error('Error loading users:', e);
        alert('Không tải được danh sách người dùng.');
      }
    }

    async function addUser(data) {
      try {
        const res = await fetch(API_BASE, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
          body: JSON.stringify(data)
        });
        const json = await res.json();
        
        if (json.code === 422) {
          alert(json.error || 'Dữ liệu không hợp lệ');
          return;
        }
        
        if (json.code === 201) {
          await loadUsers();
          return;
        }
        
        alert('Tạo người dùng thất bại');
      } catch (e) {
        console.error(e);
        alert('Tạo người dùng thất bại');
      }
    }

    async function updateUser(id, data) {
      try {
        console.log('Updating user with ID:', id, 'Type:', typeof id);
        const res = await fetch(`${API_BASE}/${id}`, {
          method: 'PUT',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
          body: JSON.stringify(data)
        });
        const json = await res.json();
        console.log('Update response:', json);
        
        if (json.code === 404) {
          alert('Không tìm thấy người dùng: ' + (json.error || ''));
          return false;
        }
        
        if (json.code === 422) {
          alert('Dữ liệu không hợp lệ: ' + (json.error || ''));
          return false;
        }
        
        if (json.code === 500) {
          alert('Lỗi server: ' + (json.error || ''));
          return false;
        }
        
        if (json.code === 200 && json.updated) {
          alert('Cập nhật thành công!');
          await loadUsers();
          return true;
        }
        
        alert('Cập nhật thất bại: ' + (json.error || 'Không xác định được nguyên nhân'));
        return false;
      } catch (e) {
        console.error(e);
        alert('Cập nhật thất bại');
        return false;
      }
    }

    async function deleteUser(id) {
      if (!confirm('Bạn có chắc muốn xóa user này?')) return;
      try {
        console.log('Deleting user with ID:', id, 'Type:', typeof id);
        const res = await fetch(`${API_BASE}/${id}`, {
          method: 'DELETE',
          headers: { 'Accept': 'application/json' }
        });
        const json = await res.json();
        console.log('Delete response:', json);
        
        if (json.code === 404) {
          alert('Không tìm thấy người dùng: ' + (json.error || ''));
          return;
        }
        
        if (json.code === 200 && json.deleted) {
          alert('Xóa thành công!');
          await loadUsers();
          return;
        }
        
        if (json.code === 500) {
          alert('Lỗi server: ' + (json.error || ''));
          return;
        }
        
        alert('Xóa thất bại: ' + (json.error || 'Không xác định được nguyên nhân'));
      } catch (e) {
        console.error(e);
        alert('Xóa thất bại');
      }
    }

    function openEditModal(id) {
      console.log('openEditModal called with ID:', id, 'Type:', typeof id);
      console.log('Available users:', users);
      
      // Convert both to numbers for comparison
      const userId = Number(id);
      const user = users.find(u => Number(u.id) === userId);
      if (!user) {
        console.error('User not found with ID:', id);
        console.error('Available user IDs:', users.map(u => u.id + ' (' + typeof u.id + ')'));
        alert('Không tìm thấy người dùng với ID: ' + id);
        return;
      }
      
      console.log('Found user:', user);
      
      userModalLabel.textContent = 'Sửa người dùng';
      inputId.value = user.id;
      userForm.classList.remove('was-validated');
      inputName.value = user.name;
      inputEmail.value = user.email;
      inputPassword.value = '';
      
      userModal.show();
    }

  </script>
</body>
</html>
