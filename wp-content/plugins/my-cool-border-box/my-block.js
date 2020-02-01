wp.blocks.registerBlockType('brad/border-box', {
    title: 'Proper bo border box',
    description: 'great',
    category: 'common',
    icon: 'smiley',
    attributes: {
        content: { type: 'string' },
        color: { type: 'string', source: 'meta', meta: 'color' }
    },

    edit: function(props) {
        function updateContent(e) {
            props.setAttributes({ content: e.target.value });
        }
        function updateColor(value) {
            props.setAttributes({ color: value.hex });
        }
        // can generate ES5 code by writing JSX code at https://babeljs.io/repl or could use the create-guten-block js library
        // could use 'React' in place of 'wp.element' without any extra code, but this might be subject to breaking changes in future versions of WP
        return wp.element.createElement("div", null, wp.element.createElement("h3", {
            style: {
                border: "5px solid ".concat(props.attributes.color)
            }
        }, props.attributes.content), wp.element.createElement("input", {
            type: "text",
            value: props.attributes.content,
            onChange: updateContent
        }), wp.element.createElement(wp.components.ColorPicker, {
            color: props.attributes.color,
            onChangeComplete: updateColor
        }));
    },
 
    save: function() {
        // any changes to this function will cause an error in WP backend as it doesn't allow changing user's saved html. removal and reinsertion of the block will be required.
        // solution is to make a 'dynamic' block:
        // return null from save function
        // have php generate html in ./plugin.php via wp functions
        // ALSO
        // can save raw fields to the db by adding extra fields to attributes e.g. content: { type: 'string', source: 'meta', meta: 'content' }
        // then save this data to the db via 'register_meta' in .plugin.php
        // the data can then be retrieved by ACF function 'get_field' in php or via rest api in javascript
        return null;
    }
})