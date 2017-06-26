
(function ($) {
    tinymce.PluginManager.add('my_custom_button', function (editor, url) {
        editor.addButton('my_custom_button', {
            text: 'Вставить блок',
            icon: false,
            type: 'menubutton',
            onclick: function () {
                $.ajax({
                    url: ajaxurl,
                    type:'POST',
                    data: {action: 'es_get_blocks'},
                })
                .done(function(result) {
                    editor.windowManager.open({
                        title: 'Выберите из существующих блоков',
                        body: [
                        {
                            type: 'listbox',
                            name: 'level',
                            label: 'Выберите шаблон',
                            values: JSON.parse(result)
                        }
                        ],
                        onsubmit: function (e) {
                            editor.insertContent(e.data.level);
                        }
                    });
                });
                
                
            }
        });
    });
})(jQuery);