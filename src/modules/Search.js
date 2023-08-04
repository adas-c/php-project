import axios from "axios";

class Search {
	constructor() {
		this.addSearchHTML();
		this.openButton = document.querySelector(".js-search-trigger");
		this.closeButton = document.querySelector(".search-overlay__close");
		this.searchOverlay = document.querySelector(".search-overlay");
		this.searchField = document.querySelector("#search-term");
		this.resultsDiv = document.querySelector("#search-overlay__results");
		this.events();
		this.isOverlayOpen = false;
		this.isSpinnerVisible = false;
		this.previousValue;
		this.typingTimer;
	}

	events() {
		this.openButton.addEventListener("click", () => this.openOverlay());
		this.closeButton.addEventListener("click", () => this.closeOverlay());
		document.addEventListener("keydown", (e) => this.keyPressDispatcher(e));
		this.searchField.addEventListener("keyup", () => this.typingLogic());
	}

	typingLogic() {
		if (this.searchField.value != this.previousValue) {
			clearTimeout(this.typingTimer);

			if (this.searchField.value) {
				if (!this.isSpinnerVisible) {
					this.resultsDiv.innerHTML = '<div class="spinner-loader"></div>';
					this.isSpinnerVisible = true;
				}
				this.typingTimer = setTimeout(this.getResults.bind(this), 750);
			} else {
				this.resultsDiv.innerHTML = "";
				this.isSpinnerVisible = false;
			}
		}
		this.previousValue = this.searchField.value;
	}

	async getResults() {
		try {
			const respons = await axios(
				uniData.root_url +
					"/wp-json/wp/v2/posts?search=" +
					this.searchField.value
			);
			const results = respons.data;
			this.resultsDiv.innerHTML = `
				<div class="row">
          <div class="one-third">
            <h2 class="search-overlay__section-title">General Information</h2>
            ${
							results.generalInfo.length
								? '<ul class="link-list min-list">'
								: "<p>No general information matches that search.</p>"
						}
              ${results.generalInfo
								.map(
									(item) =>
										`<li><a href="${item.permalink}">${item.title}</a> ${
											item.postType == "post" ? `by ${item.authorName}` : ""
										}</li>`
								)
								.join("")}
            ${results.generalInfo.length ? "</ul>" : ""}
          </div>
        </div>
			`;
			this.isSpinnerVisible = false;
		} catch (error) {
			console.log(error);
		}
	}

	keyPressDispatcher(e) {
		if (
			e.keyCode == 83 &&
			!this.isOverlayOpen &&
			document.activeElement.tagName != "INPUT" &&
			document.activeElement.tagName != "TEXTAREA"
		) {
			this.openOverlay();
		}

		if (e.keyCode == 27 && this.isOverlayOpen) {
			this.closeOverlay();
		}
	}

	openOverlay() {
		this.searchOverlay.classList.add("search-overlay--active");
		this.searchField.value = "";
		document.body.classList.add("body-no-scroll");
		setTimeout = (() => this.searchField.focus(), 301);
		this.isOverlayOpen = true;
	}

	closeOverlay() {
		this.searchOverlay.classList.remove("search-overlay--active");
		document.body.classList.remove("body-no-scroll");
		this.isOverlayOpen = false;
	}

	addSearchHTML() {
		document.body.insertAdjacentHTML(
			"beforeend",
			`
			<div class="search-overlay">
				<div class="search-overlay__top">
					<div class="container">
						<i class="fa fa-search search-overlay__icon" aria-hidden="true"></i>
						<input type="text" class="search-term" placeholder="What are you looking for?" id="search-term">
						<i class="fa fa-window-close search-overlay__close" aria-hidden="true"></i>
					</div>
				</div>
				<div class="conatiner">
					<div id="search-overlay__results"></div>
				</div>
			</div>
		`
		);
	}
}

export default Search;
