(() => {
  const postJson = (payload) =>
    fetch(ttCore.ajaxUrl, {
      method: "POST",
      credentials: "same-origin",
      body: payload,
    }).then((response) => response.json());

  const renderCard = (project) => {
    const image = project.image
      ? `<div class="tt-project-image" style="background-image:url('${project.image.replace(/'/g, "%27")}')"></div>`
      : `<div class="tt-project-image tt-no-image"></div>`;

    return `
      <article class="tt-project-card">
        <a class="tt-project-link" href="${project.permalink}">
          ${image}
          <div class="tt-project-info">
            <h3 class="tt-project-title">${project.title}</h3>
            <span class="tt-project-ver">Ver proyecto</span>
          </div>
        </a>
      </article>
    `;
  };

  const initProjectGrid = (wrapper) => {
    const filters = wrapper.querySelector("[data-tt-filters]");
    const grid = wrapper.querySelector("[data-tt-grid]");
    const feedback = wrapper.querySelector("[data-tt-feedback]");
    const loadMoreWrap = wrapper.querySelector("[data-tt-load-more-wrap]");
    const loadMoreButton = wrapper.querySelector("[data-tt-load-more]");

    let currentFilter = "todos";
    let currentPage = 1;
    let maxPages = 1;
    const perPage = Number(wrapper.dataset.perPage || 9);

    const setFeedback = (message = "") => {
      feedback.textContent = message;
      feedback.hidden = !message;
    };

    const loadProjects = (append = false) => {
      const payload = new FormData();
      payload.append("action", "tt_filter_projects");
      payload.append("nonce", ttCore.nonce);
      payload.append("tipo_proyecto", currentFilter);
      payload.append("page", String(currentPage));
      payload.append("per_page", String(perPage));

      setFeedback(ttCore.i18n.loading);

      postJson(payload)
        .then((response) => {
          if (!response.success) {
            throw new Error(ttCore.i18n.error);
          }

          const projects = response.data.projects || [];
          maxPages = Number(response.data.max_pages || 1);

          if (append) {
            grid.insertAdjacentHTML(
              "beforeend",
              projects.map(renderCard).join("")
            );
          } else {
            grid.innerHTML = projects.map(renderCard).join("");
          }

          if (!projects.length && currentPage === 1) {
            setFeedback(ttCore.i18n.noResults);
          } else {
            setFeedback("");
          }

          loadMoreWrap.hidden = currentPage >= maxPages || !projects.length;
        })
        .catch(() => {
          setFeedback(ttCore.i18n.error);
          loadMoreWrap.hidden = true;
        });
    };

    filters?.addEventListener("click", (event) => {
      const button = event.target.closest("[data-filter]");
      if (!button) {
        return;
      }

      filters
        .querySelectorAll(".tt-filter-btn")
        .forEach((item) => item.classList.remove("active"));
      button.classList.add("active");

      currentFilter = button.dataset.filter || "todos";
      currentPage = 1;
      loadProjects(false);
    });

    loadMoreButton?.addEventListener("click", () => {
      if (currentPage >= maxPages) {
        return;
      }

      currentPage += 1;
      loadProjects(true);
    });

    loadProjects(false);
  };

  const initForm = (form) => {
    const button = form.querySelector("[data-tt-submit]");
    const message = form.querySelector("[data-tt-message]");

    const setMessage = (text, type) => {
      message.textContent = text;
      message.hidden = !text;
      message.className = `tt-form-message ${type || ""}`.trim();
    };

    form.addEventListener("submit", (event) => {
      event.preventDefault();

      const payload = new FormData(form);
      payload.append("action", "tt_presupuesto");
      payload.append("nonce", ttCore.nonce);

      button.disabled = true;
      setMessage(ttCore.i18n.loading, "");

      postJson(payload)
        .then((response) => {
          if (!response.success) {
            throw new Error(response.data?.message || ttCore.i18n.error);
          }

          setMessage(response.data.message, "tt-success");
          form.reset();
        })
        .catch((error) => {
          setMessage(error.message || ttCore.i18n.error, "tt-error");
        })
        .finally(() => {
          button.disabled = false;
        });
    });
  };

  document.addEventListener("DOMContentLoaded", () => {
    document
      .querySelectorAll(".tt-projects-wrapper[data-per-page]")
      .forEach(initProjectGrid);

    document.querySelectorAll("form[data-tt-form]").forEach(initForm);
  });
})();
