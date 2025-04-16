<?php
/*
 * Plugin Name:		WP DevLog
 * Version:			1.5
 * Description:		Плагин для коммуникации между разработчиком и редакторами
 * Plugin URI:		https://t.me/vertopolkalf
 * Author:			vertopolkaLF
 * Author URI:		https://t.me/vertopolkalf
 * License:			GPL-2.0+
 * License URI:		https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:		wp-devlog
 */

add_action( 'init', function () {
	register_post_type( 'devlog', array(
		'labels' => array(
			'name' => 'Dev Log',
			'singular_name' => 'Dev Log',
			'menu_name' => 'Dev Log',
			'all_items' => 'Все Dev Log',
			'edit_item' => 'Изменить Dev Log',
			'view_item' => 'Посмотреть Dev Log',
			'view_items' => 'Посмотреть Dev Log',
			'add_new_item' => 'Добавить новый Dev Log',
			'new_item' => 'Новый Dev Log',
			'parent_item_colon' => 'Родитель Dev Log:',
			'search_items' => 'Поиск Dev Log',
			'not_found' => 'Не найдено dev log',
			'not_found_in_trash' => 'В корзине не найдено dev log',
			'archives' => 'Архивы Dev Log',
			'attributes' => 'Атрибуты Dev Log',
			'insert_into_item' => 'Вставить в dev log',
			'uploaded_to_this_item' => 'Загружено в это dev log',
			'filter_items_list' => 'Фильтровать список dev log',
			'filter_by_date' => 'Фильтр dev log по дате',
			'items_list_navigation' => 'Dev Log навигация по списку',
			'items_list' => 'Dev Log список',
			'item_published' => 'Dev Log опубликовано.',
			'item_published_privately' => 'Dev Log опубликована приватно.',
			'item_reverted_to_draft' => 'Dev Log преобразован в черновик.',
			'item_scheduled' => 'Dev Log запланировано.',
			'item_updated' => 'Dev Log обновлён.',
			'item_link' => 'Cсылка на Dev Log',
			'item_link_description' => 'Ссылка на dev log.',
		),
		'public' => true,
		'exclude_from_search' => true,
		'publicly_queryable' => false,
		'show_in_nav_menus' => false,
		'show_in_admin_bar' => false,
		'show_in_rest' => false,
		'menu_position' => 4100,
		'menu_icon' => 'dashicons-editor-code',
		'supports' => array(
			0 => 'title',
			1 => 'comments',
			2 => 'editor',
			3 => 'excerpt',
			4 => 'custom-fields'
		),
		'rewrite' => array(
			'with_front' => false,
			'pages' => false,
		),
		'delete_with_user' => false,
	) );
} );





// Функция для добавления виджета в консоль
function devlog_add_dashboard_widget() {
	wp_add_dashboard_widget(
		'devlog_dashboard_widget', // ID виджета
		'Изменения на сайте', // Заголовок виджета
		'devlog_dashboard_widget_callback' // Callback функция для отображения контента
	);
}
add_action( 'wp_dashboard_setup', 'devlog_add_dashboard_widget' );


// Функция для отображения контента виджета
function devlog_dashboard_widget_callback() {

	$args = [ 
		'post_type' => "devlog",
		'posts_per_page' => 3,
	];

	// Проверка на существование класса WP_Query
	if ( ! class_exists( 'WP_Query' ) ) {
		echo '<p>Ошибка: Класс WP_Query не найден. Возможно, WordPress работает некорректно.</p>';
		return;
	}

	$query = new WP_Query( $args );

	$posts = $query->posts;
	$first = 0;

	$current_date = new DateTime();

	$full_posts = '';

	// Обертка для всех постов
	echo '<div id="devlog-posts-container">';

	foreach ( $posts as $post ) {

		$post_date = new DateTime( $post->post_date );
		$postdate = $post_date->format( 'd.m.Y' );

		// Сначала обрабатываем контент и заменяем все картинки на полноразмерные
		$processed_content = preg_replace_callback( '/<img(.*?)src=["\'](.*?)["\'](.*?)>/i', function ($matches) {
			$img_url = $matches[2];
			$full_img_url = preg_replace( '~-(?:\d+x\d+|scaled|rotated)~', '', $img_url );
			return '<a href="' . $full_img_url . '" target="_blank"><img' . $matches[1] . 'src="' . $full_img_url . '"' . $matches[3] . '></a>';
		}, $post->post_content );

		// Теперь применяем фильтры к обработанному контенту
		$full_postcontent = apply_filters( 'the_content', $processed_content );
		$full_postcontent = wpautop( $full_postcontent );

		// Проверяем есть ли тег MORE и получаем контент до него
		if ( strpos( $processed_content, '<!--more-->' ) !== false ) {
			$parts = explode( '<!--more-->', $processed_content );
			$content_before_more = $parts[0];
			$postcontent = wp_strip_all_tags( $content_before_more, true );
			$postcontent = apply_filters( 'the_content', $postcontent );
		} else {
			$postcontent = wp_strip_all_tags( $full_postcontent, true );
			$postcontent = apply_filters( 'the_content', $postcontent );
		}

		$interval = $current_date->diff( $post_date );
		$days_difference = $interval->format( '%a' );

		if ( $first == 0 ) {
			$large = ' large';
			if ( $days_difference < 5 ) {
				$new = ' new';
			} else {
				$new = '';
			}
			$first = 1;
		} else {
			$new = '';
			$large = '';
		}

		// Выводим весь пост как одну кликабельную ссылку
		printf(
			'<a href="/?TB_inline&width=772&height=850&inlineId=%1$s" title="%2$s - %3$s" class="devlog-post thickbox%4$s%5$s">
				<div class="devlog-post-header">
					<h2>%2$s</h2>
					<span class="devlog-post-date">%3$s</span>
				</div>
				<div class="devlog-post-content">%6$s</div>
			</a>',
			$post->ID,
			esc_html( $post->post_title ),
			esc_html( $postdate ),
			esc_attr( $large ),
			esc_attr( $new ),
			$postcontent
		);

		// Полный контент поста для модального окна - без изменений
		$full_posts .= "<div class='devlog-full-post' id='{$post->ID}' style='display:none;'>";
		$full_posts .= "<div class='devlog-full-post devlog-post-content'>{$full_postcontent}</div>
		</div>";
	}

	echo '</div>'; // Закрываем #devlog-posts-container

	// Добавляем кнопку "Загрузить еще" и контейнер для новых постов
	echo '<div id="devlog-more-posts-container"></div>';
	echo '<div id="devlog-load-more-wrap">';
	echo '<button id="devlog-load-more" class="button" data-offset="10">Загрузить еще</button>';
	echo '<span id="devlog-loading" style="display:none;">Загрузка...</span>';
	echo '</div>';

	echo $full_posts;
}



function my_enqueue( $hook ) {
	if ( 'index.php' != $hook )
		return;
	wp_enqueue_style( 'devlog', plugins_url( '/devlog-style.css', __FILE__ ) );
	wp_enqueue_script( 'devlog-script', plugins_url( '/devlog-script.js', __FILE__ ), array( 'jquery', 'thickbox' ), '1.0', true );

	// Передаем данные в JavaScript
	wp_localize_script( 'devlog-script', 'devlog_ajax', array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'nonce' => wp_create_nonce( 'devlog-ajax-nonce' )
	) );
}
add_action( 'admin_enqueue_scripts', 'my_enqueue' );

// Функция для обработки AJAX запросов
function devlog_load_more_posts() {
	// Проверка безопасности
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'devlog-ajax-nonce' ) ) {
		wp_send_json_error( 'Ошибка безопасности. Обновите страницу и попробуйте снова.' );
		wp_die();
	}

	$offset = isset( $_POST['offset'] ) ? intval( $_POST['offset'] ) : 0;

	$args = [ 
		'post_type' => 'devlog',
		'posts_per_page' => 5,
		'offset' => $offset
	];

	$query = new WP_Query( $args );
	$posts = $query->posts;
	$output = '';
	$full_posts = '';

	$current_date = new DateTime();

	foreach ( $posts as $post ) {
		$post_date = new DateTime( $post->post_date );
		$postdate = $post_date->format( 'd.m.Y' );

		// Обработка контента и замена картинок на полноразмерные
		$processed_content = preg_replace_callback( '/<img(.*?)src=["\'](.*?)["\'](.*?)>/i', function ($matches) {
			$img_url = $matches[2];
			$full_img_url = preg_replace( '~-(?:\d+x\d+|scaled|rotated)~', '', $img_url );
			return '<a href="' . $full_img_url . '" target="_blank"><img' . $matches[1] . 'src="' . $full_img_url . '"' . $matches[3] . '></a>';
		}, $post->post_content );

		// Применяем фильтры к обработанному контенту
		$full_postcontent = apply_filters( 'the_content', $processed_content );
		$full_postcontent = wpautop( $full_postcontent );

		// Проверяем есть ли тег MORE и получаем контент до него
		if ( strpos( $processed_content, '<!--more-->' ) !== false ) {
			$parts = explode( '<!--more-->', $processed_content );
			$content_before_more = $parts[0];
			$postcontent = wp_strip_all_tags( $content_before_more, true );
			$postcontent = apply_filters( 'the_content', $postcontent );
		} else {
			$postcontent = wp_strip_all_tags( $full_postcontent, true );
			$postcontent = apply_filters( 'the_content', $postcontent );
		}

		// Выводим посты
		$output .= sprintf(
			'<a href="/?TB_inline&width=772&height=850&inlineId=%1$s" title="%2$s - %3$s" class="devlog-post thickbox">
                <div class="devlog-post-header">
                    <h2>%2$s</h2>
                    <span class="devlog-post-date">%3$s</span>
                </div>
                <div class="devlog-post-content">%6$s</div>
            </a>',
			$post->ID,
			esc_html( $post->post_title ),
			esc_html( $postdate ),
			'',
			'',
			$postcontent
		);

		// Полный контент поста для модального окна
		$full_posts .= "<div class='devlog-full-post' id='{$post->ID}' style='display:none;'>";
		$full_posts .= "<div class='devlog-full-post devlog-post-content'>{$full_postcontent}</div>
        </div>";
	}

	$response = array(
		'posts' => $output,
		'full_posts' => $full_posts,
		'has_more' => count( $posts ) == 5 // Проверяем, есть ли еще посты
	);

	wp_send_json( $response );
	wp_die();
}
add_action( 'wp_ajax_devlog_load_more', 'devlog_load_more_posts' );
add_action( 'wp_ajax_nopriv_devlog_load_more', 'devlog_load_more_posts' );