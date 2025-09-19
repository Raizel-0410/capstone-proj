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

  /* ---- Handle minimize + remove ---- */
  document.querySelectorAll(".widget-card").forEach(widget => {
    const minimizeBtn = widget.querySelector(".minimize-btn");
    const removeBtn = widget.querySelector(".remove-btn");

    if (minimizeBtn) {
      minimizeBtn.addEventListener("click", () => {
        widget.classList.toggle("minimized");
      });
    }

    if (removeBtn) {
      removeBtn.addEventListener("click", () => {
        widget.style.display = "none";
      });
    }
  });

  /* ---- Sidebar links (restore + scroll) ---- */
  document.querySelectorAll(".nav-links a[href^='#']").forEach(link => {
    link.addEventListener("click", (e) => {
      e.preventDefault();
      const targetId = link.getAttribute("href").substring(1);
      const target = document.getElementById(targetId);

      if (target) {
        // If hidden, restore
        if (target.style.display === "none") {
          target.style.display = "block";
        }
        // If minimized, expand
        target.classList.remove("minimized");

        // Smooth scroll
        target.scrollIntoView({ behavior: "smooth", block: "start" });
      }
    });
  });

  /* ---- Chart.js demo ---- */
  const ctx = document.getElementById("visitsChart");
  if (ctx) {
    new Chart(ctx, {
      type: "bar",
      data: {
        labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
        datasets: [{
          label: "Visits",
          data: [12, 19, 3, 5, 2, 3, 7],
          backgroundColor: "#36C3EF",
          borderRadius: 6
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
      }
    });
  }
});
