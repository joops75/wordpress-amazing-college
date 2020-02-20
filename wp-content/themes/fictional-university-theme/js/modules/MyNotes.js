import $ from 'jquery';

class MyNotes {
    constructor() {
        this.events();
    }

    events() {
        // having 2 selectors, the first of which is always present, ensures any new elements with the second selector will also trigger the associated handler
        $('#my-notes').on('click', '.delete-note', this.deleteNote.bind(this));
        $('#my-notes').on('click', '.edit-note', this.editNote.bind(this));
        $('#my-notes').on('click', '.submit-update', this.updateNote.bind(this));
        $('.submit-note').on('click', this.createNote.bind(this));
    }

    createNote(e) {
        const image = $('.new-note-image')[0].files[0];
		const data = new FormData(); // send data as form data because a file is being sent
		data.append( 'title', $('.new-note-title').val() );
        data.append( 'subtitle', $('.new-note-subtitle').val() );
		data.append( 'content', $('.new-note-body').val() );
        data.append( 'status', 'publish' );
		data.append( 'image', image );
        
        $.ajax({
            beforeSend: xhr => xhr.setRequestHeader('X-WP-NONCE', universityData.nonce),
            url: `${universityData.root_url}/wp-json/wp/v2/note`,
            method: 'POST',
			processData: false, // required when using form data
			contentType: false, // required when using form data
            data,
            success: res => {
                $('.new-note-title, .new-note-subtitle, .new-note-body').val('');
                $('.new-note-image').replaceWith('<input type="file" accept="image/*" class="new-note-image">') // remove file from input
                $('#my-notes').hide().slideDown().prepend(`
                    <li data-id="${res.id}">
                        <input readonly class="note-title-field" type="text" value="${res.title.raw}">
                        <span class="edit-note"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</span>
                        <input readonly class="note-subtitle-field" type="text" value="${res.meta.subtitle}">
                        <span class="delete-note"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</span>
                        <textarea readonly class="note-body-field">${res.content.raw}</textarea>
                        <div class="note-image-area">
                        ${
                            res.thumbnail_url ?
                            `<img src="${res.thumbnail_url}" alt="${res.thumbnail_caption}">`
                            :
                            ``
                        }
                            <input type="file" accept="image/*" class="note-image-field update-note">
                        </div>
                        <span class="update-note btn btn--blue btn--small submit-update"><i class="fa fa-arrow-right" aria-hidden="true"></i> Save</span>
                    </li>
                `);
                console.log('success');
                console.log(res);
            },
            error: res => {
                console.log('error');
                console.log(res);
                const errMsgContainer = $('.note-limit-message');
                errMsgContainer.addClass('active').text(res.responseText);
            }
        })
    }

    deleteNote(e) {
        const note = $(e.target).parents('li');

        $.ajax({
            beforeSend: xhr => xhr.setRequestHeader('X-WP-NONCE', universityData.nonce),
            url: `${universityData.root_url}/wp-json/wp/v2/note/${note.data('id')}`,
            method: 'DELETE',
            // data: { force: true }, // necessary if wp-config.php has define( 'EMPTY_TRASH_DAYS', 0 ) so post can bypass Trash and be permanently deleted. Doing so bypasses before_delete_post action hook.
            success: res => {
                note.slideUp();
                if (res.userNoteCount < 5) { // custom property added via functions.php
                    $('.note-limit-message').removeClass('active');
                }
                console.log('success');
                console.log(res);
            },
            error: res => {
                console.log('error');
                console.log(res);
            }
        })
    }

    updateNote(e) {
        const note = $(e.target).parents('li');
        const image = note.children('div').children('.note-image-field')[0].files[0];
		const data = new FormData(); // send data as form data because a file is being sent
		data.append( 'title', note.children('.note-title-field').val() );
        data.append( 'subtitle', note.children('.note-subtitle-field').val() );
		data.append( 'content', note.children('.note-body-field').val() );
		data.append( 'image', image );
        
        $.ajax({
            beforeSend: xhr => xhr.setRequestHeader('X-WP-NONCE', universityData.nonce),
            url: `${universityData.root_url}/wp-json/wp/v2/note/${note.data('id')}`,
            method: 'POST',
			processData: false, // required when using form data
			contentType: false, // required when using form data
            data,
            success: res => {
                console.log('success');
                console.log(res);
                this.makeNoteReadOnly(note);
                note.children('.note-title-field').val(res.title.raw);
                note.children('.note-subtitle-field').val(res.meta.subtitle);
                note.children('.note-body-field').val(res.content.raw);
                if (res.thumbnail_url) {
                    const existingImg = note.children('div').children('img');
                    if (existingImg.length) {
                        // if existing image present, amend
                        existingImg.attr('src', res.thumbnail_url).attr('alt', res.thumbnail_caption);
                    } else {
                        // otherwise create
                        note.children('div').prepend(`<img src="${res.thumbnail_url}" alt="${res.thumbnail_caption}">`);
                    }
                }
            },
            error: res => {
                console.log('error');
                console.log(res);
            }
        })
    }

    editNote(e) {
        const note = $(e.target).parents('li');
        if (note.data('state') === 'editable') {
            this.makeNoteReadOnly(note);
        } else {
            this.makeNoteEditable(note);
        }
    }

    makeNoteEditable(note) {
        note.find('.edit-note').html('<i class="fa fa-times" aria-hidden="true"></i> Cancel');
        note.find('.note-title-field, .note-subtitle-field, .note-body-field, .note-image-area').removeAttr('readonly').addClass('note-active-field');
        note.find('.update-note').addClass('update-note--visible');
        note.data('state', 'editable');
    }

    makeNoteReadOnly(note) {
        note.find('.edit-note').html('<i class="fa fa-pencil" aria-hidden="true"></i> Edit');
        note.find('.note-title-field, .note-subtitle-field, .note-body-field, .note-image-area').attr('readonly', true).removeClass('note-active-field');
        note.find('.update-note').removeClass('update-note--visible');
        note.data('state', 'readonly');
    }
}

export default MyNotes;