document.addEventListener("DOMContentLoaded", () => {
  /* ---- Logout modal ---- */
  const logoutLink = document.getElementById("logout-link");
  if (logoutLink) {
    logoutLink.addEventListener("click", (ev) => {
      ev.preventDefault();
      const modal = document.getElementById("confirmModal");
      const msgEl = document.getElementById("confirmMessage");
      const yes = document.getElementById("confirmYes");
      const no = document.getElementById("confirmNo");

      msgEl.textContent = "Are you sure you want to log out?";
      modal.classList.add("show");

      yes.onclick = () => { window.location.href = logoutLink.href; };
      no.onclick = () => { modal.classList.remove("show"); };
    });
  }

  const visitorsTbody = document.querySelector("#visitorsTable tbody");

  function escapeHtml(s) {
    if (!s) return "";
    return String(s)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;");
  }

  /* ---- Load Visitors ---- */
  async function loadVisitors() {
    try {
      const res = await fetch("fetch_visitors.php");
      const data = await res.json();

      visitorsTbody.innerHTML = "";
      if (!Array.isArray(data) || data.length === 0) {
        visitorsTbody.innerHTML =
          `<tr><td colspan="8" class="text-center">No visitors found</td></tr>`;
        return;
      }

      data.forEach(v => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td>${escapeHtml(v.full_name)}</td>
          <td>${escapeHtml(v.contact_number || "")}</td>
          <td>${escapeHtml(v.date)}</td>
          <td>${escapeHtml(v.time_in || "")}</td>
          <td>${v.time_out ? escapeHtml(v.time_out) : "Still Inside"}</td>
          <td>${escapeHtml(v.status)}</td>
          <td>
            <button class="btn btn-info btn-sm view-btn" data-id="${v.id}">View</button>
            ${v.status === "Inside" 
              ? `<button class="btn btn-warning btn-sm edit-btn" data-id="${v.id}">Edit</button>
                 <button class="btn btn-danger btn-sm exit-btn" data-id="${v.id}">Mark Exit</button>`
              : ""}
          </td>
        `;
        visitorsTbody.appendChild(tr);
      });
    } catch (err) {
      console.error("Error loading visitors:", err);
      visitorsTbody.innerHTML =
        `<tr><td colspan="8" class="text-center text-danger">Failed to load visitors</td></tr>`;
    }
  }

  loadVisitors();
  setInterval(loadVisitors, 30000); // refresh every 30s

  /* ---- Handle Action Buttons ---- */
  visitorsTbody.addEventListener("click", async (e) => {
    const btn = e.target.closest("button");
    if (!btn) return;

    const visitorId = btn.dataset.id;

    // ---- View ----
    if (btn.classList.contains("view-btn")) {
      try {
        const res = await fetch(`fetch_visitor_details.php?id=${encodeURIComponent(visitorId)}`);
        const visitor = await res.json();
        if (!visitor.success) return alert("Visitor data not found");

        document.getElementById("visitorName").textContent = visitor.data.full_name;
        document.getElementById("visitorContact").textContent = visitor.data.contact_number;
        document.getElementById("visitorEmail").textContent = visitor.data.email;
        document.getElementById("visitorAddress").textContent = visitor.data.home_address;
        document.getElementById("visitorReason").textContent = visitor.data.reason;
        document.getElementById("visitorIDPhoto").src = visitor.data.valid_id_path;
        document.getElementById("visitorSelfie").src = visitor.data.selfie_photo_path;
        document.getElementById("visitorTimeIn").textContent = visitor.data.time_in;
        document.getElementById("visitorTimeOut").textContent = visitor.data.time_out || "Still Inside";

        new bootstrap.Modal(document.getElementById("visitorDetailsModal")).show();
      } catch (err) {
        console.error(err);
        alert("Failed to fetch visitor details.");
      }
    }

    // ---- Edit ----
    else if (btn.classList.contains("edit-btn")) {
      try {
        const res = await fetch(`fetch_visitor_details.php?id=${encodeURIComponent(visitorId)}`);
        const visitor = await res.json();
        if (!visitor.success) return alert("Visitor data not found");

        document.getElementById("editVisitorId").value = visitor.data.id;
        document.getElementById("editTimeOut").value = visitor.data.time_out || "";

        new bootstrap.Modal(document.getElementById("editTimeModal")).show();
      } catch (err) {
        console.error(err);
        alert("Failed to load visitor data for editing.");
      }
    }

    // ---- Mark Exit ----
    else if (btn.classList.contains("exit-btn")) {
      if (!confirm("Mark this visitor as exited?")) return;

      try {
        const formData = new URLSearchParams();
        formData.append("visitor_id", visitorId);

        const res = await fetch("mark_exit_visitor.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: formData
        });

        const result = await res.json();
        if (result.success) {
          alert("Visitor exit marked and synced with vehicles!");
          loadVisitors();
        } else {
          alert("Error: " + result.message);
        }
      } catch (err) {
        console.error(err);
        alert("Request failed.");
      }
    }
  });

  /* ---- Edit Visitor Time Form ---- */
  const editTimeForm = document.getElementById("editTimeForm");
  if (editTimeForm) {
    editTimeForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      const visitorId = document.getElementById("editVisitorId").value;
      const timeOut = document.getElementById("editTimeOut").value;

      if (!timeOut) return alert("Please enter a valid time");

      try {
        const res = await fetch("update_visitor_time_out.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: `visitor_id=${encodeURIComponent(visitorId)}&time_out=${encodeURIComponent(timeOut)}`
        });
        const result = await res.json();
        if (result.success) {
          alert(result.message);
          bootstrap.Modal.getInstance(document.getElementById("editTimeModal")).hide();
          loadVisitors();
        } else {
          alert("Error: " + result.message);
        }
      } catch (err) {
        console.error(err);
        alert("Request failed.");
      }
    });
  }
});
