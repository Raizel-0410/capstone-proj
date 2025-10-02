document.addEventListener("DOMContentLoaded", () => {

  // Next button event listeners for tab navigation
  const nextToVerifyBtn = document.getElementById("nextToVerify");
  const nextToFacialBtn = document.getElementById("nextToFacial");
  const nextToVehicleBtn = document.getElementById("nextToVehicle");
  const nextToIdBtn = document.getElementById("nextToId");
  const skipVehicleBtn = document.getElementById("skipVehicle");
  const rejectBtn = document.getElementById("rejectBtn");

  function showTab(tabId) {
    const tabTrigger = document.querySelector(`#visitorTab button[data-bs-target="#${tabId}"]`);
    if (tabTrigger) {
      const tab = new bootstrap.Tab(tabTrigger);
      tab.show();
    }
  }

  if (nextToVerifyBtn) {
    nextToVerifyBtn.addEventListener("click", () => {
      showTab("verify");
    });
  }

  if (nextToFacialBtn) {
    nextToFacialBtn.addEventListener("click", () => {
      showTab("facial");
    });
  }

  if (nextToVehicleBtn) {
    nextToVehicleBtn.addEventListener("click", () => {
      showTab("vehicle");
    });
  }

  if (nextToIdBtn) {
    nextToIdBtn.addEventListener("click", () => {
      showTab("id");
    });
  }
  
  if (skipVehicleBtn) {
    skipVehicleBtn.addEventListener("click", () => {
      showTab("id");
    });
  }
  
  if (rejectBtn) {
    rejectBtn.addEventListener("click", () => {
      // Close the modal on reject
      const modalEl = document.getElementById("visitorDetailsModal");
      const modalInstance = bootstrap.Modal.getInstance(modalEl);
      if (modalInstance) {
        modalInstance.hide();
      }
      alert("Verification rejected.");
    });
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

  const visitorsTbody = document.querySelector("#visitorsTable tbody");
  const markEntryBtn = document.getElementById("markEntryBtn");
  let currentVisitorId = null;

  function escapeHtml(s) {
    if (!s) return "";
    return String(s)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;");
  }

  /* ---- Load Visitors Table ---- */
  async function loadVisitors() {
    try {
      const res = await fetch("fetch_visitors.php");
      const data = await res.json();

      visitorsTbody.innerHTML = "";

      if (!Array.isArray(data) || data.length === 0) {
        visitorsTbody.innerHTML =
          `<tr><td colspan="10" class="text-center">No visitors found</td></tr>`;
        return;
      }

      data.forEach(v => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td>${escapeHtml(v.first_name || "")}</td>
          <td>${escapeHtml(v.last_name || "")}</td>
          <td>${escapeHtml(v.contact_number || "")}</td>
          <td>${escapeHtml(v.day_name || "")}</td>
          <td>${escapeHtml(v.date)}</td>
          <td>${escapeHtml(v.time_in || "")}</td>
          <td>${v.time_out ? escapeHtml(v.time_out) : ""}</td>
          <td>${escapeHtml(v.status)}</td>
          <td>${escapeHtml(v.key_card_number || "")}</td>
          <td>
            <button class="btn btn-info btn-sm view-btn" data-id="${v.id}">View</button>
            ${(!v.time_in || v.status === "Pending")
              ? `<button class="btn btn-success btn-sm entry-btn" data-id="${v.id}">Mark Entry</button>`
              : v.status === "Inside"
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
        `<tr><td colspan="10" class="text-center text-danger">Failed to load visitors</td></tr>`;
    }
  }

  // Initial load + refresh every 30s
  loadVisitors();
  setInterval(loadVisitors, 30000);

  /* ---- Handle Action Buttons ---- */
  visitorsTbody.addEventListener("click", async (e) => {
    const btn = e.target.closest("button");
    if (!btn) return;

    const visitorId = btn.dataset.id;

    // View Button
    if (btn.classList.contains("view-btn")) {
      try {
        if (!visitorId) {
          alert("Visitor ID is missing.");
          return;
        }
        const res = await fetch(`fetch_visitor_details.php?id=${encodeURIComponent(visitorId)}`);
        const visitor = await res.json();

        if (!visitor.success) {
          alert(visitor.message || "Visitor data not found");
          return;
        }

        document.getElementById("visitorName").textContent = escapeHtml(visitor.data.full_name);
        document.getElementById("visitorContact").textContent = escapeHtml(visitor.data.contact_number);
        document.getElementById("visitorEmail").textContent = escapeHtml(visitor.data.email);
        document.getElementById("visitorAddress").textContent = escapeHtml(visitor.data.address);
        document.getElementById("visitorReason").textContent = escapeHtml(visitor.data.reason);
        document.getElementById("visitorPersonnel").textContent = escapeHtml(visitor.data.personnel_related || '');
        document.getElementById("visitorIDPhoto").src = visitor.data.id_photo_path;
        document.getElementById("visitorSelfie").src = visitor.data.selfie_photo_path;

        // Vehicle Info
        const vehicleInfo = document.getElementById("vehicleInfo");
        if (visitor.data.vehicle_owner) {
          vehicleInfo.style.display = 'block';
          document.getElementById("vehicleOwner").textContent = escapeHtml(visitor.data.vehicle_owner);
          document.getElementById("vehicleBrand").textContent = escapeHtml(visitor.data.vehicle_brand);
          document.getElementById("vehicleModel").textContent = escapeHtml(visitor.data.vehicle_model);
          document.getElementById("vehicleColor").textContent = escapeHtml(visitor.data.vehicle_color);
          document.getElementById("plateNumber").textContent = escapeHtml(visitor.data.plate_number);
          document.getElementById("vehiclePhoto").src = visitor.data.vehicle_photo_path || '';
        } else {
          vehicleInfo.style.display = 'none';
        }

        // Driver Info
        const driverInfo = document.getElementById("driverInfo");
        if (visitor.data.driver_name) {
          driverInfo.style.display = 'block';
          document.getElementById("driverName").textContent = escapeHtml(visitor.data.driver_name);
          document.getElementById("driverIdPhoto").src = visitor.data.driver_id || '';
        } else {
          driverInfo.style.display = 'none';
        }

        currentVisitorId = visitor.data.id;

        // Hide or show verify tabs based on visitor status
        const verifyTabBtn = document.querySelector('#visitorTab button[data-bs-target="#verify"]');
        const facialTabBtn = document.querySelector('#visitorTab button[data-bs-target="#facial"]');
        const vehicleTabBtn = document.querySelector('#visitorTab button[data-bs-target="#vehicle"]');
        const idTabBtn = document.querySelector('#visitorTab button[data-bs-target="#id"]');

        if (visitor.data.status === "Inside" || visitor.data.status === "Exited") {
          if (verifyTabBtn) verifyTabBtn.style.display = 'none';
          if (facialTabBtn) facialTabBtn.style.display = 'none';
          if (vehicleTabBtn) vehicleTabBtn.style.display = 'none';
          if (idTabBtn) idTabBtn.style.display = 'none';

          // Hide Next buttons in verify tabs
          const nextToVerifyBtn = document.getElementById("nextToVerify");
          const nextToFacialBtn = document.getElementById("nextToFacial");
          const nextToVehicleBtn = document.getElementById("nextToVehicle");

          if (nextToVerifyBtn) nextToVerifyBtn.style.display = 'none';
          if (nextToFacialBtn) nextToFacialBtn.style.display = 'none';
          if (nextToVehicleBtn) nextToVehicleBtn.style.display = 'none';

          // Show details tab by default since all verify tabs are hidden
          const detailsTabTriggerEl = document.querySelector('#details-tab');
          if (detailsTabTriggerEl) {
            const tab = bootstrap.Tab.getInstance(detailsTabTriggerEl) || new bootstrap.Tab(detailsTabTriggerEl);
            tab.show();
          }
        } else {
          if (verifyTabBtn) verifyTabBtn.style.display = 'block';
          if (facialTabBtn) facialTabBtn.style.display = 'block';
          if (vehicleTabBtn) vehicleTabBtn.style.display = 'block';
          if (idTabBtn) idTabBtn.style.display = 'block';

          // Show Next buttons in verify tabs
          const nextToVerifyBtn = document.getElementById("nextToVerify");
          const nextToFacialBtn = document.getElementById("nextToFacial");
          const nextToVehicleBtn = document.getElementById("nextToVehicle");

          if (nextToVerifyBtn) nextToVerifyBtn.style.display = 'inline-block';
          if (nextToFacialBtn) nextToFacialBtn.style.display = 'inline-block';
          if (nextToVehicleBtn) nextToVehicleBtn.style.display = 'inline-block';

          // Show details tab by default
          const detailsTabTriggerEl = document.querySelector('#details-tab');
          if (detailsTabTriggerEl) {
            const tab = bootstrap.Tab.getInstance(detailsTabTriggerEl) || new bootstrap.Tab(detailsTabTriggerEl);
            tab.show();
          }
        }

        new bootstrap.Modal(document.getElementById("visitorDetailsModal")).show();


      } catch (err) {
        console.error(err);
        alert("Failed to fetch visitor details.");
      }
    }

    // ---- Edit Button ----
    else if (btn.classList.contains("edit-btn")) {
      try {
        const res = await fetch(`fetch_visitor_details.php?id=${encodeURIComponent(visitorId)}`);
        const visitor = await res.json();

        if (!visitor.success) return alert("Visitor data not found");

        // Populate modal fields
        document.getElementById("editVisitorId").value = visitor.data.id;
        document.getElementById("editTimeOut").value = visitor.data.time_out || "";

        // Show modal
        new bootstrap.Modal(document.getElementById("editTimeModal")).show();
      } catch (err) {
        console.error(err);
        alert("Failed to load visitor data for editing.");
      }
    }

    // Entry Button
    else if (btn.classList.contains("entry-btn")) {
      await markEntry(visitorId);
    }

  // Exit Button
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

    if (!res.ok) {
      const text = await res.text();
      console.error("Response not ok:", text);
      alert("Request failed: " + res.status + " - " + text.substring(0, 100));
    } else {
      const result = await res.json();
      if (result.success) {
        alert(result.message);
        loadVisitors(); // Refresh table to show "Exited" status
      } else {
        alert("Error: " + result.message);
      }
    }
  } catch (err) {
    console.error(err);
    alert("Request failed: " + err.message);
  }
}


  });

  // ---- Save Edit Time ----
  document.getElementById("saveTimeBtn").addEventListener("click", async () => {
    const visitorId = document.getElementById("editVisitorId").value;
    const timeOut = document.getElementById("editTimeOut").value;
    const validityStart = document.getElementById("editValidityStart").value;
    const validityEnd = document.getElementById("editValidityEnd").value;

    if (!timeOut) return alert("Please enter a valid time out");
    if (!validityStart) return alert("Please enter a valid validity start");
    if (!validityEnd) return alert("Please enter a valid validity end");

    try {
      const formData = new URLSearchParams();
      formData.append("visitor_id", visitorId);
      formData.append("time_out", timeOut);
      formData.append("validity_start", validityStart);
      formData.append("validity_end", validityEnd);

      const res = await fetch("update_visitor_time_out.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: formData
      });

      const result = await res.json();
      if (result.success) {
        alert(result.message);
        loadVisitors(); // Refresh table
        bootstrap.Modal.getInstance(document.getElementById("editTimeModal")).hide();
      } else {
        alert("Error: " + result.message);
      }
    } catch (err) {
      console.error(err);
      alert("Request failed.");
    }
  });

  /* ---- Mark Entry from modal ---- */
  markEntryBtn.addEventListener("click", async () => {
    if (!currentVisitorId) return;
    await markEntry(currentVisitorId);
    markEntryBtn.style.display = "none";
    bootstrap.Modal.getInstance(document.getElementById("visitorDetailsModal")).hide();
  });

  /* ---- Functions ---- */
  async function markEntry(visitorId) {
  try {
    const formData = new URLSearchParams();
    formData.append("visitor_id", visitorId);

    const res = await fetch("mark_entry_visitor.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: formData
    });

    if (!res.ok) {
      const text = await res.text();
      console.error("Response not ok:", text);
      alert("Request failed: " + res.status + " - " + text.substring(0, 100));
    } else {
      const result = await res.json();
      if (result.success) {
        alert("Visitor entry marked!");
        loadVisitors();
      } else {
        alert("Error: " + result.message);
      }
    }
  } catch (err) {
    console.error(err);
    alert("Request failed: " + err.message);
  }
  }
});
