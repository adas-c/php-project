class Search {
	constructor() {
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
				this.typingTimer = setTimeout(this.getResults.bind(this), 2000);
			} else {
				this.resultsDiv.innerHTML = "";
				this.isSpinnerVisible = false;
			}
		}
		this.previousValue = this.searchField.value;
	}

	getResults() {
		this.resultsDiv.innerHTML = "HHHHHeeee";
		this.isSpinnerVisible = false;
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
		document.body.classList.add("body-no-scroll");
		this.isOverlayOpen = true;
	}

	closeOverlay() {
		this.searchOverlay.classList.remove("search-overlay--active");
		document.body.classList.remove("body-no-scroll");
		this.isOverlayOpen = false;
	}
}

export default Search;
