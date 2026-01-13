// helcraw-faq.js
document.addEventListener("DOMContentLoaded", function () {
  const items = document.querySelectorAll(
    ".helcraw-faq-list .helcraw-faq-item"
  );

  items.forEach(function (item) {
    const btn = item.querySelector(".helcraw-faq-question");
    const panel = item.querySelector(".helcraw-faq-answer");
    if (!btn || !panel) return;

    // initialize open state
    if (!panel.hasAttribute("hidden")) {
      panel.style.maxHeight = panel.scrollHeight + "px";
      item.classList.add("is-open");
      btn.setAttribute("aria-expanded", "true");
    } else {
      panel.style.maxHeight = null;
      item.classList.remove("is-open");
      btn.setAttribute("aria-expanded", "false");
    }

    btn.addEventListener("click", function () {
      const isOpen = item.classList.contains("is-open");

      // close siblings so only one open at a time
      items.forEach(function (sib) {
        if (sib === item) return;
        const sibPanel = sib.querySelector(".helcraw-faq-answer");
        const sibBtn = sib.querySelector(".helcraw-faq-question");
        if (sib.classList.contains("is-open")) {
          sib.classList.remove("is-open");
          if (sibPanel) {
            sibPanel.hidden = true;
            sibPanel.style.maxHeight = null;
          }
          if (sibBtn) {
            sibBtn.setAttribute("aria-expanded", "false");
            sibBtn.style.background = "transparent";
          }
        }
      });

      if (isOpen) {
        // close this one
        item.classList.remove("is-open");
        panel.hidden = true;
        panel.style.maxHeight = null;
        btn.setAttribute("aria-expanded", "false");
        btn.style.background = "transparent";
      } else {
        // open this one
        item.classList.add("is-open");
        panel.hidden = false;
        // set maxHeight to animate expansion
        panel.style.maxHeight = panel.scrollHeight + "px";
        btn.setAttribute("aria-expanded", "true");
        btn.style.background = "transparent"; // ensure no inline background applied
      }
    });

    // keyboard accessibility
    btn.addEventListener("keydown", function (e) {
      if (e.key === "Enter" || e.key === " ") {
        e.preventDefault();
        btn.click();
      }
    });
  });
});
