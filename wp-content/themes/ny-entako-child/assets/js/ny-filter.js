document.addEventListener("DOMContentLoaded", function () {
	const ajaxUrl = window.nyAjaxConfig?.ajaxUrl || "/wp-admin/admin-ajax.php";

	function loadFilteredProducts(catSlug, clickedEl) {
		const prodGrid = document.querySelector(".ny-block-prod");
		if (!prodGrid) return;

		const currentOrderby = document.querySelector(".custom-input-fltr")?.dataset.orderby || "date";
		const currentOrder   = document.querySelector(".custom-input-fltr")?.dataset.order || "DESC";

		prodGrid.style.opacity = "0.5";

		document.querySelectorAll(".ny-cat .ny-block-cat, .ny-cat .ny-subcat-item").forEach(function (el) {
			el.classList.remove("active");
		});
		if (clickedEl) {
			clickedEl.classList.add("active");
		}

		const formData = new URLSearchParams();
		formData.append("action", "ny_filter_products");
		formData.append("category", catSlug || "");
		formData.append("orderby", currentOrderby);
		formData.append("order", currentOrder);

		fetch(ajaxUrl, {
			method: "POST",
			headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
			body: formData.toString()
		})
		.then(function (res) { return res.json(); })
		.then(function (data) {
			if (data && data.success && data.data && data.data.html) {
				const tempWrapper = document.createElement("div");
				tempWrapper.innerHTML = data.data.html.trim();
				const newGrid = tempWrapper.querySelector(".ny-block-prod");
				if (newGrid) {
					prodGrid.replaceWith(newGrid);

					const textEl = document.querySelector(".ny-text-fltr");
					const shown  = newGrid.getAttribute("data-shown-posts");
					const found  = newGrid.getAttribute("data-found-posts");
					if (textEl && shown !== null && found !== null) {
						textEl.textContent = "Affichage de " + shown + " produits sur " + found + " disponibles";
					}
				}

				const titleEl = document.querySelector(".ny-title-fltr");
				if (titleEl && data.data.title_text) {
					titleEl.textContent = data.data.title_text;
				}
			}
		})
		.catch(function (err) {
			console.error("Erreur filtrage produits :", err);
		})
		.finally(function () {
			const updatedGrid = document.querySelector(".ny-block-prod");
			if (updatedGrid) updatedGrid.style.opacity = "1";
		});
	}

	document.addEventListener("click", function (e) {
		const catLink = e.target.closest(".ny-cat a[data-cat-slug]");
		if (!catLink) return;

		if (!document.querySelector(".ny-block-prod")) {
			return;
		}

		e.preventDefault();

		const isParentWithSubcat = catLink.classList.contains("ny-has-subcat");
		const clickedChevron     = e.target.closest(".ny-chevron-cat");
		const group              = catLink.closest(".ny-cat-group");
		const slug               = catLink.getAttribute("data-cat-slug") || "";

		if (isParentWithSubcat && group) {
			const isAlreadyOpen = group.classList.contains("open");

			document.querySelectorAll(".ny-cat .ny-cat-group.open").forEach(function (openGroup) {
				if (openGroup !== group) {
					openGroup.classList.remove("open");
				}
			});

			if (clickedChevron) {
				group.classList.toggle("open");
				return;
			}

			if (!isAlreadyOpen) {
				group.classList.add("open");
			}
		} else if (slug === "") {
			document.querySelectorAll(".ny-cat .ny-cat-group.open").forEach(function (openGroup) {
				openGroup.classList.remove("open");
			});
		}

		loadFilteredProducts(slug, catLink);
	});
});