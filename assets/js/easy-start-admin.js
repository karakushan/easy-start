jQuery(function ($) {

    /* Возвращает шаблон мета поля с помощью AJAX */
    function getTemplate(templateName, name, append) {
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                "template": templateName,
                "name": name,
                "action": "es_get_template"
            },
            success: function (data) {
                console.log(data);
                if (data.success && data.data.template) {
                    $(append).append(data.data.template);

                }
            }
        });

    }

    // Изменяет индекс секций слайдера при удалении или изменении порядка
    function slidesReIndex(slides) {
        slides.each(function (index, value) {

            var name = $(this).parent('.es-slides').data('name');
            var el = $(this);
            var c = index;
            el.attr('data-index', c);
            el.find('.count').text(c);
            el.find('input,textarea').each(function () {
                jQuery(this).attr('name', "easy[" + name + "][" + c + "][" + jQuery(this).data('name') + "]")
            })
            el.find('.title').attr('name', "easy[" + name + "][" + c + "][title]");
            el.find('.image-input').attr('name', "easy[" + name + "][" + c + "][image]");
        });
    }

    $('.es-sort').sortable({
        update: function (event, ui) {
            slidesReIndex(ui.item.parent().find('section'));
        }
    });

    // поле типа слайдер
    $(document).on('click', '[data-action="add-slide"]', function (event) {
        event.preventDefault();
        let slidesWrap = $(this).parents('.es-slider-wrapper').find('.es-slides');
        let name = slidesWrap.data('name');
        // getTemplate('slider', name, slidesWrap);

        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                "template": "slider",
                "name": name,
                "action": "es_get_template"
            },
            success: function (data) {
                console.log(data);
                if (data.success && data.data.template) {
                    slidesWrap.append(data.data.template);
                    slidesReIndex(slidesWrap.find('section'));
                }
            }
        });
    });

    // удаление секции слайдера
    $(document).on('click', '[data-action="remove-section"]', function (event) {
        event.preventDefault();
        var sections = $(this).parents('.es-slides');
        if (confirm('Подтверждаете действие?')) {
            $(this).parents('section').remove();
            slidesReIndex(sections.find('section'));
        }
    });

    // открытие секции слайдера
    $(document).on('click', '[data-action="open-section"]', function (event) {
        event.preventDefault();
        $(this).parents('section').toggleClass('active');

    });

    // загрузка изображения или файла из медиатеки
    $(document).on('click', '[data-action="select-image"],[data-action="select-file"]', function (event) {
        event.preventDefault();
        var button = $(this);
        // родительский див
        if (button.parents('.sub-field').length) {
            var buttonWrapper = button.parents('.sub-field');
        } else {
            var buttonWrapper = button.parents('.es_meta_field');
        }

        var send_attachment_bkp = wp.media.editor.send.attachment;
        wp.media.editor.send.attachment = function (props, attachment) {
            // загрузка изображения
            if (button.attr("data-action") == "select-image") {
                if (attachment.mime != "image/x-icon") {
                    buttonWrapper.find('.file img').attr('src', attachment.url);
                    $(button).prev().val(attachment.id);
                } else {
                    alert('Файл не является изображением');
                }
            }
            // загрузка файла
            if (button.attr("data-action") == "select-file") {
                $(button).prev().val(attachment.id);
                buttonWrapper.find('.file img').attr('src', attachment.icon);
            }
            wp.media.editor.send.attachment = send_attachment_bkp;
        };
        wp.media.editor.open(button);
        return false;

    });


    // вкладки языков
    $('.es_tax_tabs>ul>li>a').on('click', function (event) {
        event.preventDefault();

        $(this).parents('.es_tax_tabs').find('.es-tab-body').removeClass('active');
        $(this).parents('ul').find('a').removeClass('active');
        $(this).addClass('active');
        var selector = $(this).attr('href');
        $(selector).addClass('active');

    });

    /*
     * удаляем значение произвольного поля
     * если быть точным, то мы просто удаляем value у input type="hidden"
     */
    $(document).on('click', "[data-action='remove-atachment']", function (e) {
        e.preventDefault();
        var button = $(this);
        var buttonWrapper = button.parents('.es_meta_field');
        // родительский див
        if (button.parents('.sub-field').length) {
            buttonWrapper = button.parents('.sub-field');
        } else if (button.parents('.es-tab-body').length) {
            buttonWrapper = button.parents('.es-tab-body');
        }
        if (confirm("Подтверждаете?")) {
            console.log(buttonWrapper);
            buttonWrapper.find('.file img').attr('src', button.data('no-image'));
            buttonWrapper.find('input').val('');
        }
    });
    //удаление изображений из галереи
    $('.es-gallery-wrapper').on('click', '.es-image-delete', function (e) {
        e.preventDefault();
        $(this).parent().fadeIn().remove();
    });

    $('[data-action="es-add-field"]').on('click', function (e) {
        e.preventDefault();
        var rowsEl = $(this).parents('.es_meta_field').find('.es-rows');
        var rowFirst = rowsEl.find('.es-form-row').first();
        rowFirst.clone().appendTo(rowsEl);
        var rowsCount = rowsEl.find('.es-form-row').length;

        rowsEl.find('.es-form-row').each(function (index, el) {
            var ind = index + 1;
            $(el).find('.es-section-id span').text(ind);
            $(el).find('.es-acc-title').attr('name', 'easy[accordion-item][' + index + '][title]');
            $(el).find('.es-acc-subtitle').attr('name', 'easy[accordion-item][' + index + '][subtitle]');
            $(el).find('.es-acc-desc').attr('name', 'easy[accordion-item][' + index + '][text]');
            if (index == rowsCount - 1) {
                $(el).find('.es-acc-title').val('');
                $(el).find('.es-acc-subtitle').val('');
                $(el).find('.es-acc-desc').val('');
            }

        })
    });

    // Gallery
    jQuery('.es-add-gallery').on('click', function (e) {
        e.preventDefault();
        if (frame) {
            frame.open();
            return;
        }
        var imgContainer = jQuery(this).parents('.es-meta-gallery').find('.es-gallery-wrapper');
        var frame;
        var name = jQuery(this).data('name');
        // Create a new media frame
        frame = wp.media({
            title: 'Select images',
            button: {
                text: 'Add'
            },
            multiple: true
        });
        // When an image is selected in the media frame...
        frame.on('select', function () {
            // Get media attachment details from the frame state
            var attachment = frame.state().get('selection').toJSON();
            for (key in attachment) {
                // Send the attachment URL to our custom image input field.
                imgContainer.append(
                    '<div class="item" style="background-image:url(' + attachment[key].url + ');">' +
                    '<a class="es-image-delete" href="#" aria-label="Delete image">remove</a>' +
                    '<input type="hidden" name="easy[' + name + '][]" value="' + attachment[key].id + '"> ' +
                    '</div>'
                )
                ;
            }
        });
        // Finally, open the modal on click
        frame.open();

    });

    //удаление строки формы
    $('[data-es-action="row-remove"]').on('click', function (e) {
        e.preventDefault();
        if (confirm('Confirm?')) {
            $(this).parents('.es-form-row').fadeOut(800).remove();
        }
    });

    // добавление секции слайдера
    $('[data-es-action="add-slider-row"]').on('click', function (e) {
        e.preventDefault();
        var container = $(this).parents('.es-slider-wrapper');
        var name = $(this).data('name');
        var key = container.find('.item').length;

        var html = '<div class="item es-form-row"><a class="es-image-delete" data-es-action="row-remove" href="#" aria-label="">remove</a> <input type="hidden" name="easy[' + name + '][' + key + '][image]" data-type="image" class="image-id"> <div class="item-left"> <div class="image-add" data-es-action="add-image" title="Add/edit image" style="background-image:url(/wp-content/plugins/easy-start/img/image-add-button.svg);background-size: 63px;"> </div> </div> <div class="item-right"> <p> <label>Заголовок</label> <input type="text" name="easy[' + name + '][' + key + '][title]" data-type="title"> </p> <p> <label>Описание</label> <textarea name="easy[' + name + '][' + key + '][text]" rows="8" data-type="text"></textarea> </p> </div> <div class="clear"></div> </div>';
        container.append(html);

        // обновляем индекс названий полей
        $('.es-slider-wrapper .es-form-row').each(function (index) {
            $(this).find('input').each(function () {
                $(this).attr('name', 'easy[' + name + '][' + index + '][' + $(this).data('type') + ']');
            });
            $(this).find('textarea').each(function () {
                $(this).attr('name', 'easy[' + name + '][' + index + '][' + $(this).data('type') + ']');
            })
        })
    })
    $(".es-gallery-wrapper").sortable();

});
