document.addEventListener("DOMContentLoaded", () => {
  const usersTbody = document.getElementById("usersTbody");
  const addModal = document.getElementById("addUserModal");
  const addForm = document.getElementById("addUserForm");
  const editModal = document.getElementById("editUserModal");
  const editForm = document.getElementById("editUserForm");

  function escapeHtml(s) {
    if (s === null || s === undefined) return "";
    return String(s)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;");
  }

  // Fetch users and populate table
  async function fetchUsers() {
    try {
      const res = await fetch("fetch_users.php");
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const users = await res.json();
      if (!Array.isArray(users)) return console.error("Invalid response", users);

      usersTbody.innerHTML = "";
      users.forEach(u => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td>${escapeHtml(u.full_name)}</td>
          <td>${escapeHtml(u.email)}</td>
          <td>${escapeHtml(u.rank)}</td>
          <td>${escapeHtml(u.status)}</td>
          <td>${escapeHtml(u.role)}</td>
          <td>${escapeHtml(u.joined_date)}</td>
          <td>${escapeHtml(u.last_active)}</td>
          <td>
            <button class="btn btn-sm btn-primary edit-btn" data-id="${u.id}">Edit</button>
            <button class="btn btn-sm btn-danger delete-btn" data-id="${u.id}">Delete</button>
          </td>
        `;
        usersTbody.appendChild(tr);
      });
    } catch (err) {
      console.error(err);
      alert("Error fetching users. Check console.");
    }
  }

  // Edit & Delete Delegation
  usersTbody.addEventListener("click", async e => {
    const editBtn = e.target.closest(".edit-btn");
    const deleteBtn = e.target.closest(".delete-btn");

    if (editBtn) {
      const id = editBtn.dataset.id;
      try {
        const res = await fetch(`get_user.php?id=${encodeURIComponent(id)}`);
        const user = await res.json();
        if (!user.id) return alert(user.message || "Cannot load user");

        // Safely populate fields if they exist
        ['id','full_name','email','rank','role','status'].forEach(f => {
          if (editForm[f]) editForm[f].value = user[f] || '';
        });

        editModal.classList.add("show");
      } catch (err) {
        console.error(err);
        alert("Network error while fetching user");
      }
    }

    if (deleteBtn) {
      const id = deleteBtn.dataset.id;
      if (confirm("Delete this user?")) {
        const fd = new FormData();
        fd.append("id", id);
        try {
          const res = await fetch("delete_user.php", { method: "POST", body: fd });
          const json = await res.json();
          alert(json.message || (json.success ? "Deleted!" : "Delete failed"));
          if (json.success) fetchUsers();
        } catch (err) {
          console.error(err);
          alert("Network error while deleting user");
        }
      }
    }
  });

  // Edit form submit
  editForm.addEventListener("submit", async e => {
    e.preventDefault();
    try {
      const formData = new FormData(editForm);
      const res = await fetch("edit_user.php", { method: "POST", body: formData });
      const json = await res.json();
      alert(json.message);
      if (json.status === "success") {
        editModal.classList.remove("show");
        fetchUsers();
      }
    } catch (err) {
      console.error(err);
      alert("Network error while updating user");
    }
  });

  // Modal close buttons
  ['closeEditModal','cancelEditBtn','closeAddModal','cancelAddBtn'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.onclick = () => {
      if (id.includes('Edit')) editModal.classList.remove("show");
      else addModal.classList.remove("show");
    };
  });

  fetchUsers();
});
