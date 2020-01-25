import $ from 'jquery';

class MyNotes {
    constructor() {
        this.events();
    }

    events() {
        // having 2 selectors, the first of which is always present, ensures any new elements with the second selector will also trigger the associated handler
        $('#my-notes').on('click', '.delete-note', this.deleteNote.bind(this));
        $('#my-notes').on('click', '.edit-note', this.editNote.bind(this));
        $('#my-notes').on('click', '.update-note', this.updateNote.bind(this));
        $('.submit-note').on('click', this.createNote.bind(this));
    }

    createNote(e) {
        const data = {
            'title': $('.new-note-title').val(),
            'content': $('.new-note-body').val(),
            'status': 'publish' // omitting field creates draft post, setting to 'publish' makes post available to everyone
        };
        
        $.ajax({
            beforeSend: xhr => xhr.setRequestHeader('X-WP-NONCE', universityData.nonce),
            url: `${universityData.root_url}/wp-json/wp/v2/note`,
            method: 'POST',
            data,
            success: res => {
                $('.new-note-title, .new-note-body').val('');
                $('#my-notes').hide().slideDown().prepend(`
                    <li data-id="${res.id}">
                        <input readonly class="note-title-field" type="text" value="${res.title.raw}">
                        <span class="edit-note"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</span>
                        <span class="delete-note"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</span>
                        <textarea readonly class="note-body-field">${res.content.raw}</textarea>
                        <span class="update-note btn btn--blue btn--small"><i class="fa fa-arrow-right" aria-hidden="true"></i> Save</span>
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
        const data = {
            'title': note.children('.note-title-field').val(),
            'content': note.children('.note-body-field').val()
        };
        
        $.ajax({
            beforeSend: xhr => xhr.setRequestHeader('X-WP-NONCE', universityData.nonce),
            url: `${universityData.root_url}/wp-json/wp/v2/note/${note.data('id')}`,
            method: 'POST',
            data,
            success: res => {
                this.makeNoteReadOnly(note);
                console.log('success');
                console.log(res);
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
        note.find('.note-title-field, .note-body-field').removeAttr('readonly').addClass('note-active-field');
        note.find('.update-note').addClass('update-note--visible');
        note.data('state', 'editable');
    }

    makeNoteReadOnly(note) {
        note.find('.edit-note').html('<i class="fa fa-pencil" aria-hidden="true"></i> Edit');
        note.find('.note-title-field, .note-body-field').attr('readonly', true).removeClass('note-active-field');
        note.find('.update-note').removeClass('update-note--visible');
        note.data('state', 'readonly');
    }
}

export default MyNotes;