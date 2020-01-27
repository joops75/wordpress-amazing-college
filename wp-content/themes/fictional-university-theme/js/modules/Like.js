import $ from 'jquery';

class Like {
    constructor() {
        this.events();
    }

    events() {
        $('.like_box').on('click', this.likeUnlike.bind(this));
    }

    likeUnlike(e) {
        const currentLikeBox = $(e.target).closest('.like_box'); // ensure clicking on heart icon still references container like box
        // const action = currentLikeBox.data('exists') === 'no' ? 'like' : 'unlike'; // only fires once on page load
        const action = currentLikeBox.attr('data-exists') === 'no' ? 'like' : 'unlike';
        const prof_id = currentLikeBox.attr('data-prof_id');
        const like_id = currentLikeBox.attr('data-like_id');
        
        $.ajax({
            beforeSend: xhr => {
                xhr.setRequestHeader('X-WP-NONCE', universityData.nonce);
            },
            url: `${universityData.root_url}/wp-json/university/v1/manageLike`,
            method: 'POST',
            data: { action, prof_id, like_id }, // WP treats POST data the same way as URL query parameters
            success: res => {
                const likeCount = +currentLikeBox.find('.like-count').text();
                if (action === 'like') {
                    currentLikeBox.attr('data-exists', 'yes');
                    currentLikeBox.find('.like-count').text(likeCount + 1);
                    currentLikeBox.attr('data-like_id', res);
                } else {
                    currentLikeBox.attr('data-exists', 'no');
                    currentLikeBox.find('.like-count').text(likeCount - 1);
                    currentLikeBox.attr('data-like_id', '');
                }
            },
            error: res => {
                console.log('Error: ', res.responseText);
            }
        })
    }
}

export default Like;