// personnelaccount.js (robust version with debug logging + custom modals)

/* ---------- helpers ---------- */
function escapeHtml(s) {
  if (s === null || s === undefined) return "";
  return String(s)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;");
}

/* ---------- custom confirm & notify (use modals if present, fallback to native) ---------- */
function showConfirm(message, cb) {
  const modal = document.getElementById("confirmModal");
  const msgEl = document.getElementById("confirmMessage");
  const yes = document.getElementById("confirmYes");
  const no = document.getElementById("confirmNo");

  if (modal && msgEl && yes && no) {
    msgEl.textContent = message;
    modal.classList.add("show");
    // cleanup previous handlers
    yes.onclick = () => { modal.classList.remove("show"); cb(true); };
    no.onclick  = () => { modal.classList.remove("show"); cb(false); };
    return;
  }
  // fallback
  const ok = window.confirm(message);
  cb(ok);
}

function showNotify(message, callback) {
  const modal = document.getElementById("notifyModal");
  const msgEl = document.getElementById("notifyMessage");
  const ok = document.getElementById("notifyOk");

  if (modal && msgEl && ok) {
    msgEl.textContent = message;
    modal.classList.add("show");
    ok.onclick = () => { modal.classList.remove("show"); if (callback) callback(); };
    return;
  }
  // fallback
  alert(message);
  if (callback) callback();
}


/* ---------- DOM ready ---------- */
document.addEventListener("DOMContentLoaded", () => {
  // elements

  // logout confirmation (if logout-link exists)
  const logoutLink = document.getElementById("logout-link");
  if (logoutLink) {
    logoutLink.addEventListener("click", (ev) => {
      ev.preventDefault();
      const url = logoutLink.href;
      showConfirm("Are you sure you want to log out?", (ok) => { if (ok) window.location.href = url; });
    });
  }

  fetchUsers();
});
