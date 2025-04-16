jQuery(document).ready(function ($) {
    // Кнопка "Загрузить еще"
    $('#devlog-load-more').on('click', function () {
        var button = $(this);
        var offset = button.data('offset');
        var loading = $('#devlog-loading');

        // Показываем индикатор загрузки
        button.hide();
        loading.show();

        // Отправляем AJAX запрос
        $.ajax({
            url: devlog_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'devlog_load_more',
                offset: offset,
                nonce: devlog_ajax.nonce
            },
            success: function (response) {
                // Добавляем новые посты в контейнер
                $('#devlog-more-posts-container').append(response.posts);

                // Добавляем полные версии постов
                $('body').append(response.full_posts);

                // Обновляем offset для следующего запроса
                button.data('offset', offset + 5);

                // Если больше нет постов, скрываем кнопку
                if (!response.has_more) {
                    button.remove();
                    loading.text('Больше записей нет').show();
                } else {
                    // Иначе показываем кнопку снова
                    button.show();
                    loading.hide();
                }

                // Обновляем thickbox для новых элементов
                tb_init('a.thickbox');
            },
            error: function () {
                // В случае ошибки
                loading.text('Ошибка загрузки. Попробуйте еще раз.');
                button.show();
            }
        });
    });
}); 