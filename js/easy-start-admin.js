jQuery(function ($) {
    $('.es_tax_tabs>ul>li>a').on('click', function (event) {
        event.preventDefault();
        $(this).parents('.es_tax_tabs').find('.es-tab-body').removeClass('active');
        $(this).parents('ul').find('a').removeClass('active');
        $(this).addClass('active');
        var selector = $(this).attr('href');
        $(selector).addClass('active');

    });
    /*
     * действие при нажатии на кнопку загрузки изображения
     * вы также можете привязать это действие к клику по самому изображению
     */
    $('.upload_image_button').click(function () {
        var send_attachment_bkp = wp.media.editor.send.attachment;
        var button = $(this);
        wp.media.editor.send.attachment = function (props, attachment) {
            console.log(attachment)
            if (typeof(attachment.sizes.thumbnail) == 'undefined') {
                imageUrl = attachment.url;
            }else{
                imageUrl = attachment.sizes.thumbnail.url;
            }
            $(button).parents('.es_meta_field').find('.file').html('<img src="' + imageUrl + '">').fadeIn();
            $(button).prev().val(attachment.id);
            wp.media.editor.send.attachment = send_attachment_bkp;
        };
        wp.media.editor.open(button);
        return false;
    });
    /*
     * удаляем значение произвольного поля
     * если быть точным, то мы просто удаляем value у input type="hidden"
     */
    $('.remove_image_button').click(function () {
        var r = confirm("Confirm?");
        if (r == true) {
            var src = $(this).parent().prev().attr('data-src');
            $(this).parent().prev().attr('src', src).fadeOut();
            $(this).prev().prev().val('');
            $(button).parents('.es_meta_field').find('.file').fadeOut().html('');
        }
        return false;
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

    // slider
    jQuery('body').on('click', '[data-es-action="add-image"]', function (e) {
        e.preventDefault();
        var imageFrame = jQuery(this);
        var hiddenField = imageFrame.parents('.item').find('.image-id');
        if (frame) {
            frame.open();
            return;
        }

        var frame;

        // Create a new media frame
        frame = wp.media({
            title: 'Select image',
            button: {
                text: 'Add'
            },
            multiple: false
        });
        // When an image is selected in the media frame...
        frame.on('select', function () {
            // Get media attachment details from the frame state
            var attachment = frame.state().get('selection').toJSON();
            var imageUrl = attachment[0]['url'];
            var imageId = attachment[0]['id'];
            imageFrame.css({
                'background-image': 'url(' + imageUrl + ')',
                'background-size': 'cover'

            });
            hiddenField.val(imageId);
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
