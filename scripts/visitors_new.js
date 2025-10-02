document.addEventListener("DOMContentLoaded", () => {

  // ----- Buttons & Elements -----
  const nextToVerifyBtn = document.getElementById("nextToVerify");
  const nextToFacialBtn = document.getElementById("nextToFacial");
  const nextToVehicleBtn = document.getElementById("nextToVehicle");
  const nextToIdBtn = document.getElementById("nextToId");
  const skipVehicleBtn = document.getElementById("skipVehicle");
  const rejectBtn = document.getElementById("rejectBtn");
  const markEntryBtn = document.getElementById("markEntryBtn");
  const saveTimeBtn = document.getElementById("saveTimeBtn");
  const logoutLink = document.getElementById("logout-link");

  const expectedVisitorsTbody = document.querySelector("#expectedVisitorsTable tbody");
  const insideVisitorsTbody = document.querySelector("#insideVisitorsTable tbody");
  const exitedVisitorsTbody = document.querySelector("#exitedVisitorsTable tbody");

  let currentVisitorId = null;

  // ----- Helper Functions -----
  function escapeHtml(s) {
    if (!s) return "";
    return String(s)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;");
  }

  function showTab(tabId) {
    const tabTrigger = document.querySelector(`#visitorTab button[data-bs-target="#${tabId}"]`);
    if (tabTrigger) {
      const tab = new bootstrap.Tab(tabTrigger);
      tab.show();
    }
  }

  async function fetchVisitorDetails(visitorId) {
    try {
      const res = await fetch(`fetch_visitor_details.php?id=${encodeURIComponent(visitorId)}`);
      const visitor = await res.json();

      if (!visitor.success) {
        alert(visitor.message || "Visitor data not found");
        return null;
      }

      return visitor.data;
    } catch (err) {
      console.error(err);
      alert("Failed to fetch visitor details.");
      return null;
    }
  }

  function showVisitorDetails(visitor) {
    document.getElementById("visitorName").textContent = escapeHtml(visitor.full_name);
    document.getElementById("visitorContact").textContent = escapeHtml(visitor.contact_number);
    document.getElementById("visitorEmail").textContent = escapeHtml(visitor.email);
    document.getElementById("visitorAddress").textContent = escapeHtml(visitor.address);
    document.getElementById("visitorReason").textContent = escapeHtml(visitor.reason);
    document.getElementById("visitorPersonnel").textContent = escapeHtml(visitor.personnel_related || '');
    document.getElementById("visitorIDPhoto").src = visitor.id_photo_path;
    document.getElementById("visitorSelfie").src = visitor.selfie_photo_path;

    // Vehicle Info
    const vehicleInfo = document.getElementById("vehicleInfo");
    if (visitor.vehicle_owner) {
      vehicleInfo.style.display = 'block';
      document.getElementById("vehicleOwner").textContent = escapeHtml(visitor.vehicle_owner);
      document.getElementById("vehicleBrand").textContent = escapeHtml(visitor.vehicle_brand);
      document.getElementById("vehicleModel").textContent = escapeHtml(visitor.vehicle_model);
      document.getElementById("vehicleColor").textContent = escapeHtml(visitor.vehicle_color);
      document.getElementById("plateNumber").textContent = escapeHtml(visitor.plate_number);
      document.getElementById("vehiclePhoto").src = visitor.vehicle_photo_path || '';
    } else vehicleInfo.style.display = 'none';

    // Driver Info
    const driverInfo = document.getElementById("driverInfo");
    if (visitor.driver_name) {
      driverInfo.style.display = 'block';
      document.getElementById("driverName").textContent = escapeHtml(visitor.driver_name);
      document.getElementById("driverIdPhoto").src = visitor.driver_id || '';
    } else driverInfo.style.display = 'none';

    currentVisitorId = visitor.id;

    // Show/Hide tabs based on status
    const verifyTabBtn = document.querySelector('#visitorTab button[data-bs-target="#verify"]');
    const facialTabBtn = document.querySelector('#visitorTab button[data-bs-target="#facial"]');
    const vehicleTabBtn = document.querySelector('#visitorTab button[data-bs-target="#vehicle"]');
    const idTabBtn = document.querySelector('#visitorTab button[data-bs-target="#id"]');

    const isReadOnly = visitor.status === "Inside" || visitor.status === "Exited";

    [verifyTabBtn, facialTabBtn, vehicleTabBtn, idTabBtn].forEach(tab => {
      if (tab) tab.style.display = isReadOnly ? 'none' : 'block';
    });

    [nextToVerifyBtn, nextToFacialBtn, nextToVehicleBtn].forEach(btn => {
      if (btn) btn.style.display = isReadOnly ? 'none' : 'inline-block';
    });

    const detailsTabTriggerEl = document.querySelector('#details-tab');
    if (detailsTabTriggerEl) {
      const tab = bootstrap.Tab.getInstance(detailsTabTriggerEl) || new bootstrap.Tab(detailsTabTriggerEl);
      tab.show();
    }

    new bootstrap.Modal(document.getElementById("visitorDetailsModal")).show();
  }

  async function markEntry(visitorId) {
    try {
      const res = await fetch("mark_entry_visitor.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `visitor_id=${encodeURIComponent(visitorId)}`
      });
      const data = await res.json();
      if (data.success) {
        alert("Visitor marked as inside.");
        await loadExpectedVisitors();
        await loadInsideVisitors();
      } else alert(data.message || "Failed to mark entry.");
    } catch (err) {
      console.error(err);
      alert("Error while marking entry.");
    }
  }

  async function markExit(visitorId) {
    try {
      const res = await fetch("mark_exit_visitor.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `visitor_id=${encodeURIComponent(visitorId)}`
      });
      const data = await res.json();
      if (data.success) {
        alert("Visitor marked as exited.");
        await loadInsideVisitors();
        await loadExitedVisitors();
      } else alert(data.message || "Failed to mark exit.");
    } catch (err) {
      console.error(err);
      alert("Error while marking exit.");
    }
  }

  // ----- Load Tables -----
  async function loadTable(url, tbody, columns) {
    try {
      const res = await fetch(url);
      const data = await res.json();

      tbody.innerHTML = "";
      if (!Array.isArray(data) || data.length === 0) {
        tbody.innerHTML = `<tr><td colspan="${columns}" class="text-center">No records found</td></tr>`;
        return;
      }

      data.forEach(v => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td>${escapeHtml(v.first_name || "")}</td>
          <td>${escapeHtml(v.last_name || "")}</td>
          <td>${escapeHtml(v.contact_number || "")}</td>
          ${v.key_card_number !== undefined ? `<td>${escapeHtml(v.key_card_number || "")}</td>` : ""}
          ${v.time_in !== undefined ? `<td>${escapeHtml(v.time_in || "")}</td>` : ""}
          ${v.time_out !== undefined ? `<td>${escapeHtml(v.time_out || "")}</td>` : ""}
          <td>${escapeHtml(v.status)}</td>
          <td>
            <button class="btn btn-info btn-sm view-btn" data-id="${v.id}">View</button>
            ${v.time_in === undefined ? `<button class="btn btn-success btn-sm entry-btn" data-id="${v.id}">Mark Entry</button>` : ""}
            ${v.time_out === undefined && v.time_in ? `<button class="btn btn-danger btn-sm exit-btn" data-id="${v.id}">Mark Exit</button>` : ""}
            ${v.time_in && !v.time_out ? `<button class="btn btn-warning btn-sm edit-btn" data-id="${v.id}">Edit</button>` : ""}
          </td>
        `;
        tbody.appendChild(tr);
      });
    } catch (err) {
      console.error(`Error loading table: ${url}`, err);
      tbody.innerHTML = `<tr><td colspan="${columns}" class="text-center text-danger">Failed to load data</td></tr>`;
    }
  }

  const loadExpectedVisitors = () => loadTable("fetch_expected_visitors.php", expectedVisitorsTbody, 7);
  const loadInsideVisitors = () => loadTable("fetch_inside_visitors.php", insideVisitorsTbody, 8);
  const loadExitedVisitors = () => loadTable("fetch_exited_visitors.php", exitedVisitorsTbody, 8);

  // ----- Event Listeners -----
  [nextToVerifyBtn, nextToFacialBtn, nextToVehicleBtn, nextToIdBtn, skipVehicleBtn].forEach(btn => {
    if (!btn) return;
    btn.addEventListener("click", () => showTab(btn.dataset.targetTab || btn.id.replace("nextTo", "").toLowerCase()));
  });

  markEntryBtn?.addEventListener("click", () => {
    if (currentVisitorId) markEntry(currentVisitorId);
  });

  saveTimeBtn?.addEventListener("click", () => {
    if (currentVisitorId) markExit(currentVisitorId);
  });

  // Delegate table buttons
  [expectedVisitorsTbody, insideVisitorsTbody, exitedVisitorsTbody].forEach(tbody => {
    tbody.addEventListener("click", async e => {
      const id = e.target.dataset.id;
      if (!id) return;

      if (e.target.classList.contains("view-btn")) {
        const visitor = await fetchVisitorDetails(id);
        if (visitor) showVisitorDetails(visitor);
      } else if (e.target.classList.contains("entry-btn")) {
        markEntry(id);
      } else if (e.target.classList.contains("exit-btn")) {
        markExit(id);
      }
    });
  });

  logoutLink?.addEventListener("click", () => {
    if (confirm("Are you sure you want to log out?")) {
      window.location.href = "logout.php";
    }
  });

  // ----- Initial Load -----
  loadExpectedVisitors();
  loadInsideVisitors();
  loadExitedVisitors();
});
