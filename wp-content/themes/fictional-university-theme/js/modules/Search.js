import $ from 'jquery';

class Search {
    constructor() {
        this.addSearchHTML();
        this.openButton = $('.js-search-trigger');
        this.closeButton = $('.search-overlay__close');
        this.searchOverlay = $('.search-overlay');
        this.searchInput = $('#search-term');
        this.resultsOutput = $('#search-overlay__results');
        this.events();
        this.isOverlayOpen = false;
        this.timer = null;
        this.isSpinnerVisible = false;
        this.searchValue;
        this.previousSearchValue;
        this.getPostTypes = ['posts', 'pages']
    }
    
    addSearchHTML() {
        $('body').append(`
            <div class="search-overlay">
                <div class="search-overlay__top">
                    <div class="container">
                        <i class="fa fa-search search-overlay__icon" aria-hidden="true"></i>
                        <input type="text" class="search-term" placeholder="What are you looking for?" id="search-term">
                        <i class="fa fa-window-close search-overlay__close" aria-hidden="true"></i>
                    </div>
                </div>
        
                <div class="container">
                    <div id="search-overlay__results"></div>
                </div>
            </div>
        `)
    }

    events() {
        // jQuery 'on' method changes the value of 'this' to the html element that responded, so need to bind context of 'this'
        this.openButton.on('click', this.openOverlay.bind(this));
        this.closeButton.on('click', this.closeOverlay.bind(this));
        this.searchInput.on('keyup', this.typingLogic.bind(this));
        $(document).on('keydown', this.handleKeys.bind(this));
    }

    openOverlay() {
        this.searchOverlay.addClass('search-overlay--active');
        $('body').addClass('body-no-scroll'); // triggers the 'overflow: hidden' property to disable scrolling
        this.isOverlayOpen = true;
        setTimeout(() => this.searchInput.focus(), 301);
    }

    closeOverlay() {
        this.searchInput.val('');
        this.searchInput.blur();
        this.resultsOutput.html('');
        this.searchOverlay.removeClass('search-overlay--active');
        $('body').removeClass('body-no-scroll');
        this.isOverlayOpen = false;
    }

    handleKeys(e) {
        if (!this.isOverlayOpen && e.keyCode == 83 && !$('input, textarea').is(':focus')) this.openOverlay();
        if (this.isOverlayOpen && e.keyCode == 27) this.closeOverlay();
    }

    typingLogic(e) {
        this.searchValue = this.searchInput.val().trim();
        if (this.searchValue != this.previousSearchValue) {
            clearTimeout(this.timer);
            if (this.searchValue) {
                if (!this.isSpinnerVisible) {
                    this.isSpinnerVisible = true;
                    this.resultsOutput.html('<div class="spinner-loader"></div>');
                }
                this.timer = setTimeout(this.getResults.bind(this), 800);
            } else {
                this.isSpinnerVisible = false;
                this.resultsOutput.html('');
            }
        }
        this.previousSearchValue = this.searchValue;
    }

    getResults() {
        // 'universityData' passed in through a script tag via functions.php
        $
            .when(...this.getPostTypes.map(postType => $.getJSON(`${universityData.root_url}/wp-json/wp/v2/${postType}?search=${this.searchValue}`)))
            .then((...data) => {
                this.isSpinnerVisible = false;
                const combinedData = data.reduce((a, b) => {
                    a.push(...b[0]);
                    return a;
                }, []);
                this.resultsOutput.html(`
                    ${combinedData.length
                        ?
                        `
                        <h2 class="search-overlay__section-title">Search results for "${this.searchValue}":</h2>
                        <ul class="link-list min-list">
                            ${combinedData.map(post => `<li><a href="${post.link}">${post.title.rendered}</a></li>`).join('')}
                        </ul>
                        `
                        :
                        `
                        <p>No search results for "${this.searchValue}"</p>
                        `
                    }
                `);
            }, () => {
                this.isSpinnerVisible = false;
                this.resultsOutput.html('Unexpected error. Please try again');
            })
    }
}

export default Search;