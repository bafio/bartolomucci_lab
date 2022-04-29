$(function() {
    var editor = $('#froala_editor');

    editor.editable({
        inlineMode: false,
        inverseSkin: true,
        shortcuts: false,
        spellcheck: true,
        toolbarFixed: false,
        mediaManager: false,
        placeholder: '',
        imageUploadURL: '/admin/image/upload.php',
        imageUploadParams: froala_editor_params.callback_extra_params,
        imageErrorCallback: function(data) {
            alert('error code: '+data.errorCode+' --> error: '+data.errorStatus);
            editor.editable('hideImageWrapper');
        },
        afterRemoveImageCallback: function(src) {
            $.post(
                '/admin/image/delete.php',
                $.extend({image: src}, froala_editor_params.callback_extra_params)
            );
        }
    }).editable('setHTML', froala_editor_params.body_content, false);

    $('.froala_submit_button').click(function() {
        content = editor.editable('getHTML');
        if(content.length > 0) {
            $('input[name="body"]').val(content[0]);
        }
    });
});
