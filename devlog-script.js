jQuery(document).ready(function ($) {
    // Обработчик для динамически добавленных элементов
    $(document).on('click', '#devlog-more-posts-container a.thickbox', function (e) {
        // Предотвращаем стандартное поведение ссылки
        e.preventDefault();

        // Получаем ID модального окна из атрибута href
        var modalId = $(this).attr('href').split('inlineId=')[1].split('&')[0];

        // Вызываем thickbox
        tb_show($(this).attr('title'), $(this).attr('href'));

        console.log('Clicked on dynamically added thickbox link:', modalId);

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
                // Отладочная информация
                console.log('AJAX response:', response);

                // Добавляем новые посты в контейнер
                $('#devlog-more-posts-container').append(response.posts);

                // Добавляем полные версии постов
                $('body').append(response.full_posts);

                // Обновляем offset для следующего запроса
                var newOffset = offset + response.debug.post_count;
                button.data('offset', newOffset);
                console.log('New offset:', newOffset);

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
                loading.text('Ошибка загрузки. Попробуйте еще раз.');
                button.show();
            }
        });
    });
}); 