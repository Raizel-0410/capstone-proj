// personnelaccount.js

/* ---------- helpers ---------- */
function escapeHtml(s) {
  if (s === null || s === undefined) return "";
  return String(s)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;");
}

/* ---------- fetch & render users ---------- */
async function fetchUsers() {
  try {
    const res = await fetch("fetch_users.php");
    const users = await res.json();
    const tbody = document.querySelector("#userTable tbody");
    if (!tbody) return;
    tbody.innerHTML = "";
    users.forEach(user => {
      tbody.insertAdjacentHTML("beforeend", `
        <tr>
          <td>${escapeHtml(user.full_name)}</td>
          <td>${escapeHtml(user.email)}</td>
          <td>${escapeHtml(user.rank)}</td>
          <td>${escapeHtml(user.status)}</td>
          <td>${escapeHtml(user.role)}</td>
          <td>${escapeHtml(user.joined_date)}</td>
          <td>${escapeHtml(user.last_active)}</td>
          <td>  <button type="button" class="btn btn-danger btn-sm" onclick="deleteUser('${user.id}')"> Delete</button></td>
        </tr>
      `);
    });
  } catch (err) {
    console.error("fetchUsers error:", err);
  }
}

/* ---------- confirm & notify ---------- */
function showConfirm(message, cb) {
  const modal = document.getElementById("confirmModal");
  if (modal) {
    document.getElementById("confirmMessage").textContent = message;
    modal.classList.add("show");
    document.getElementById("confirmYes").onclick = () => { modal.classList.remove("show"); cb(true); };
    document.getElementById("confirmNo").onclick  = () => { modal.classList.remove("show"); cb(false); };
  } else {
    cb(window.confirm(message));
  }
}

function showNotify(message, cb) {
  const modal = document.getElementById("notifyModal");
  if (modal) {
    document.getElementById("notifyMessage").textContent = message;
    modal.classList.add("show");
    document.getElementById("notifyOk").onclick = () => { modal.classList.remove("show"); if (cb) cb(); };
  } else {
    alert(message);
    if (cb) cb();
  }
}

/* ---------- delete user ---------- */
async function deleteUser(id) {
  showConfirm("Are you sure you want to delete this personnel?", async (ok) => {
    if (!ok) return;
    try {
      const resp = await fetch("delete_user.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id })
      });
      const result = await resp.json();
      if (result.success) {
        showNotify(result.message || "Deleted", fetchUsers);
      } else {
        showNotify("Error: " + (result.message || "Unknown"));
      }
    } catch (err) {
      console.error(err);
      showNotify("Network/server error while deleting.");
    }
  });
}

/* ---------- modal helpers ---------- */
function showAddUserForm() {
  const m = document.getElementById("addUserModal");
  if (m) m.classList.add("show");
}
function closeAddUserForm() {
  const m = document.getElementById("addUserModal");
  if (m) m.classList.remove("show");
}

/* ---------- DOM ready ---------- */
document.addEventListener("DOMContentLoaded", () => {
  const addBtn = document.getElementById("addPersonnelBtn");
  const addUserModal = document.getElementById("addUserModal");
  const addUserForm = document.getElementById("addUserForm");
  const closeBtn = document.querySelector(".close-modal");

  if (addBtn) addBtn.addEventListener("click", showAddUserForm);
  if (closeBtn) closeBtn.addEventListener("click", closeAddUserForm);
  if (addUserModal) addUserModal.addEventListener("click", e => { if (e.target === addUserModal) closeAddUserForm(); });

  if (addUserForm) {
    addUserForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      const data = Object.fromEntries(new FormData(addUserForm).entries());
      if (!data.full_name || !data.email || !data.password) {
        return showNotify("Please fill required fields.");
      }
      if (data.password !== data.password_confirm) {
        return showNotify("Passwords do not match.");
      }
      try {
        const resp = await fetch("add_user.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(data)
        });
        const result = await resp.json();
        if (result.success) {
          showNotify(result.message || "Account created", () => {
            addUserForm.reset();
            closeAddUserForm();
            fetchUsers();
          });
        } else {
          showNotify("Error: " + (result.message || "Unknown"));
        }
      } catch (err) {
        console.error(err);
        showNotify("Network/server error while adding user.");
      }
    });
  }

  fetchUsers();
});

document.addEventListener("DOMContentLoaded", () => {
  const logoutLink = document.getElementById("logout-link");
  const confirmModal = document.getElementById("confirmModal");
  const confirmMessage = document.getElementById("confirmMessage");
  const confirmYes = document.getElementById("confirmYes");
  const confirmNo = document.getElementById("confirmNo");

  // Logout confirmation
  if (logoutLink) {
    logoutLink.addEventListener("click", function (e) {
      e.preventDefault();
      confirmMessage.textContent = "Are you sure you want to logout?";
      confirmModal.classList.add("show");

      confirmYes.onclick = () => {
        window.location.href = "logout.php";
      };
      confirmNo.onclick = () => {
        confirmModal.classList.remove("show");
      };
    });
  }
});


