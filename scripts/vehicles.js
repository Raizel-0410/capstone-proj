document.addEventListener("DOMContentLoaded", () => {
  const expectedTbody = document.querySelector("#expectedVehiclesTable tbody");
  const insideTbody = document.querySelector("#insideVehiclesTable tbody");

  function escapeHtml(s) {
    if (!s) return "";
    return String(s)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;");
  }

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
  
  // ---- Load Expected Vehicles ----
  async function loadExpectedVehicles() {
    try {
      const res = await fetch("fetch_expected_vehicles.php");
      const data = await res.json();
      console.log("Expected vehicles:", data);

      expectedTbody.innerHTML = "";
      if (!Array.isArray(data) || data.length === 0) {
        expectedTbody.innerHTML =
          `<tr><td colspan="7" class="text-center">No expected vehicles</td></tr>`;
        return;
      }

     data.forEach(v => {
    const tr = document.createElement("tr");
    tr.innerHTML = `
    <td>${escapeHtml(v.vehicle_owner)}</td>
    <td>${escapeHtml(v.vehicle_brand)}</td>
    <td>${escapeHtml(v.vehicle_model)}</td>
    <td>${escapeHtml(v.vehicle_color)}</td>
    <td>${escapeHtml(v.plate_number)}</td>
    <td>${escapeHtml(v.status)}</td>
    <td>
      <button class="btn btn-sm btn-primary view-live-btn" data-id="${v.id}">
        Live View
      </button>
    </td>
  `;
  expectedTbody.appendChild(tr);
});

    } catch (err) {
      console.error("Error loading expected vehicles:", err);
    }
  }
  window.loadExpectedVehicles = loadExpectedVehicles;

  // ---- Load Inside Vehicles ----
  async function loadInsideVehicles() {
    try {
      const res = await fetch("fetch_inside_vehicles.php");
      const data = await res.json();
      console.log("Inside vehicles:", data);

      insideTbody.innerHTML = "";
      if (!Array.isArray(data) || data.length === 0) {
        insideTbody.innerHTML =
          `<tr><td colspan="8" class="text-center">No vehicles inside</td></tr>`;
        return;
      }

      data.forEach(v => {
  const tr = document.createElement("tr");
  tr.innerHTML = `
    <td>${escapeHtml(v.vehicle_owner)}</td>
    <td>${escapeHtml(v.vehicle_brand)}</td>
    <td>${escapeHtml(v.vehicle_model)}</td>
    <td>${escapeHtml(v.vehicle_color)}</td>
    <td>${escapeHtml(v.plate_number)}</td>
    <td>${escapeHtml(v.status || "Inside")}</td>
    <td>${escapeHtml(v.entry_time || "")}</td>
    <td>${v.exit_time ? escapeHtml(v.exit_time) : "Still Inside"}</td>
    <td>
      ${!v.exit_time
        ? `<button class="btn btn-sm btn-danger exit-btn" data-id="${v.id}">
             Mark Exit
           </button>`
        : ""}
    </td>
  `;
  insideTbody.appendChild(tr);
});



    } catch (err) {
      console.error("Error loading inside vehicles:", err);
    }
  }
  window.loadInsideVehicles = loadInsideVehicles;

  // ---- Live View Modal ----
  const liveModal = new bootstrap.Modal(document.getElementById("liveViewModal"));
  const liveContainer = document.getElementById("liveStreamContainer");

  expectedTbody.addEventListener("click", e => {
    const btn = e.target.closest(".view-live-btn");
    if (btn) {
      openLiveModal(btn.dataset.id);
    }
  });

  function openLiveModal(vehicleId) {
    liveContainer.innerHTML = `
      <div class="camera-feed">
        <video autoplay muted loop width="100%" height="300" style="background:black;">
          <source src="./demo/demo_feed.mp4" type="video/mp4">
          Your browser does not support video.
        </video>
        <div class="mt-3 d-flex gap-2">
          <button id="captureBtn" class="btn btn-warning">📸 Capture Vehicle</button>
          <button id="confirmEntryBtn" class="btn btn-success">✅ Confirm Entry</button>
        </div>
      </div>
    `;
    liveModal.show();

    // Capture button
    document.getElementById("captureBtn").onclick = () => {
      alert("Vehicle captured for ID: " + vehicleId);
    };

    // Confirm Entry button
    document.getElementById("confirmEntryBtn").onclick = async () => {
      if (confirm("Confirm entry for this vehicle?")) {
        try {
          const res = await fetch("confirm_vehicle_entry.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "vehicle_id=" + encodeURIComponent(vehicleId)
          });
          const result = await res.json();
          if (result.success) {
            alert("✅ Vehicle entry confirmed!");
            liveModal.hide();
            loadExpectedVehicles();
            loadInsideVehicles();
          } else {
            alert("Error: " + result.message);
          }
        } catch (err) {
          console.error(err);
          alert("Request failed.");
        }
      }
    };
  }

  // ---- Handle Exit Button ----
  insideTbody.addEventListener("click", e => {
    const btn = e.target.closest(".exit-btn");
    if (btn) {
      const id = btn.dataset.id;
      if (confirm("Mark this vehicle as exited?")) {
        fetch("mark_exit.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: "vehicle_id=" + encodeURIComponent(id)
        })
          .then(res => res.json())
          .then(result => {
            if (result.success) {
              alert("🚗 Vehicle exit marked!");
              loadInsideVehicles();
            } else {
              alert("Error: " + result.message);
            }
          })
          .catch(err => {
            console.error(err);
            alert("Request failed.");
          });
      }
    }
  });

  // ---- Auto refresh ----
  loadExpectedVehicles();
  loadInsideVehicles();
  setInterval(() => {
    loadExpectedVehicles();
    loadInsideVehicles();
  }, 30000);
});
