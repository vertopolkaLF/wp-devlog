<?php
/*
 * Plugin Name:		WP DevLog
 * Version:			1.4
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
			4 => 'custom-fields',
			5 => 'thumbnail'
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
		'post_type' => "devlog"
	];

	$query = new WP_Query( $args );

	$posts = $query->posts;
	$first = 0;

	$current_date = new DateTime();

	$full_posts = '';

	foreach ( $posts as $post ) {

		$post_date = new DateTime( $post->post_date );
		$postdate = $post_date->format( 'd.m.Y' );
		$full_postcontent = apply_filters( 'the_content', $post->post_content );

		// Преобразуем изображения в полном тексте поста в ссылки на полноразмерные файлы
		$full_postcontent = preg_replace_callback( '/<img(.*?)src=["\'](.*?)["\'](.*?)>/i', function ($matches) {
			$img_url = $matches[2];
			// Пытаемся получить ID изображения по URL
			$attachment_id = attachment_url_to_postid( $img_url );
			if ( $attachment_id ) {
				// Если нашли ID, получаем URL полноразмерного изображения
				$full_img_url = wp_get_attachment_image_url( $attachment_id, 'full' );
				if ( $full_img_url ) {
					$img_url = $full_img_url;
				}
			}
			return '<a href="' . $img_url . '" target="_blank"><img' . $matches[1] . 'src="' . $matches[2] . '"' . $matches[3] . '></a>';
		}, $full_postcontent );

		// Проверяем есть ли тег MORE и получаем контент до него
		if ( strpos( $post->post_content, '<!--more-->' ) !== false ) {
			$parts = explode( '<!--more-->', $post->post_content );
			$content_before_more = $parts[0];
			$postcontent = preg_replace( "/<a.*?>(.*)?<\/a>/im", "$1", apply_filters( 'the_content', $content_before_more ) );
		} else {
			$postcontent = preg_replace( "/<a.*?>(.*)?<\/a>/im", "$1", $full_postcontent );
		}

		$post_thumbnail = get_the_post_thumbnail_url( $post, 'large' );
		$post_thumbnail_full = get_the_post_thumbnail_url( $post, 'full' );


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

		echo "<a href='/?TB_inline&width=772&height=850&inlineId={$post->ID}' title='{$post->post_title} - {$postdate}' class='devlog-post thickbox{$large}{$new}'>
		<div class='devlog-post-header'>
			<h2>{$post->post_title}</h2>
			<span class='devlog-post-date'>{$postdate}</h2>
		</div>";
		if ( $post_thumbnail != '' && $large == ' large' ) {
			echo "<img src='{$post_thumbnail}'>";
		}

		echo "<div class='devlog-post-content'>{$postcontent}</div>
		</a>";

		$full_posts .= "<div class='devlog-full-post' id='{$post->ID}' style='display:none;'>";
		if ( $post_thumbnail != '' ) {
			$full_posts .= "<a href='{$post_thumbnail_full}' target='_blank'><img src='{$post_thumbnail_full}'></a>";
		}
		$full_posts .= "<div class='devlog-full-post devlog-post-content'>{$full_postcontent}</div>
		</div>";
	}
	echo $full_posts;
}



function my_enqueue( $hook ) {
	if ( 'index.php' != $hook )
		return;
	wp_enqueue_style( 'devlog', plugins_url( '/devlog-style.css', __FILE__ ) );
}
add_action( 'admin_enqueue_scripts', 'my_enqueue' );