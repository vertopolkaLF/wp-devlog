jQuery(document).ready(function ($) {
    // Обработчик для динамически добавленных элементов
    $(document).on('click', '#devlog-more-posts-container a.thickbox', function (e) {
        // Предотвращаем стандартное поведение ссылки
        e.preventDefault();

        // Получаем ID модального окна из атрибута href
        var modalId = $(this).attr('href').split('inlineId=')[1].split('&')[0];

        // Вызываем thickbox
        tb_show($(this).attr('title'), $(this).attr('href'));

        return false;
    });

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
                var postsCount = $(response.posts).filter('a.devlog-post').length;
                var newOffset = offset + postsCount;
                button.data('offset', newOffset);

                // Если больше нет постов, скрываем кнопку
                if (!response.has_more) {
                    button.remove();
                    loading.text(devlog_i18n.no_more_entries).show();
                } else {
                    // Иначе показываем кнопку снова
                    button.show();
                    loading.hide();
                }

                // Обновляем thickbox для новых элементов
                setTimeout(function () {
                    tb_init('a.thickbox');
                    // Для уверенности повторно инициализируем после небольшой задержки
                    setTimeout(function () {
                        tb_init('a.thickbox');
                    }, 500);
                }, 100);
            },
            error: function () {
                // В случае ошибки
                loading.text(devlog_i18n.load_error);
                button.show();
            }
        });
    });
}); 