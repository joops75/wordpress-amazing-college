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
        $.getJSON(`${universityData.root_url}/wp-json/university/v1/search?term=${this.searchValue}`)
            .then(results => {
                this.isSpinnerVisible = false;
                const postTypes = Object.keys(results);
                this.resultsOutput.html(`
                    ${postTypes.length
                        ?
                        `
                        <h2 class="search-overlay__section-title">Search results for "${this.searchValue}":</h2>
                            ${postTypes.map((postType, i) => {
                                return (
                                    `
                                    ${i % 3 === 0 ? `<div class="row">` : ''}
                                    <div class="one-third">
                                        <h2 class="search-overlay__section-title">${postType.slice(0, 1).toUpperCase() + postType.slice(1)}s:</h2>
                                        <ul class="link-list min-list">
                                            ${results[postType].map(item => {
                                                return (
                                                    `
                                                    ${postType === 'professor'
                                                    ?
                                                    `
                                                    <li class="professor-card__list-item">
                                                        <a class="professor-card" href="${item.permalink}">
                                                            <img src="${item.image}" class="professor-card__image">
                                                            <span class="professor-card__name">${item.title}</span>
                                                        </a>
                                                    </li>
                                                    `
                                                    :
                                                    postType === 'event'
                                                    ?
                                                    `
                                                    <div class="event-summary">
                                                        <a class="event-summary__date t-center" href="${item.permalink}">
                                                            <span class="event-summary__month">${item.month}</span>
                                                            <span class="event-summary__day">${item.day}</span>  
                                                        </a>
                                                        <div class="event-summary__content">
                                                            <h5 class="event-summary__title headline headline--tiny"><a href="${item.permalink}">${item.title}</a></h5>
                                                            <p>${item.description} <a href="${item.permalink}" class="nu gray">Learn more &raquo;</a></p>
                                                        </div>
                                                    </div>
                                                    `
                                                    :
                                                    `
                                                    <li>
                                                        <a href="${item.permalink}">${item.title}</a>
                                                        ${postType == 'post' ? `by ${item.authorName}` : ''}
                                                    </li>
                                                    `
                                                    }
                                                    `
                                                )
                                            }).join('')}
                                        </ul>
                                    </div>
                                    ${i % 3 === 2 || i === postTypes.length - 1 ? `</div>` : ''}
                                    `
                                )
                            }).join('')}
                        `
                        :
                        `
                        <h2 class="search-overlay__section-title">No search results for "${this.searchValue}".</h2>
                        `
                    }
                `);
            }, () => {
                this.isSpinnerVisible = false;
                this.resultsOutput.html('Unexpected error. Please try again');
            });
    }
}

export default Search;