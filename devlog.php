<?php
/*
 * Plugin Name:		WP DevLog
 * Version:			1.7.1
 * Description:		Plugin for communication between developers and editors
 * Plugin URI:		https://t.me/vertopolkalf
 * Author:			vertopolkaLF
 * Author URI:		https://t.me/vertopolkalf
 * License:			GPL-2.0+
 * License URI:		https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:		wp-devlog
 * Domain Path:     /languages
 */

define( 'DEVLOG_POSTS_PER_PAGE', get_option( 'devlog_posts_per_page', 5 ) );

// Load plugin textdomain
function devlog_load_textdomain() {
	load_plugin_textdomain( 'wp-devlog', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'devlog_load_textdomain' );

// Добавляем страницу настроек
function devlog_add_settings_page() {
	add_submenu_page(
		'edit.php?post_type=devlog', // родительское меню
		__( 'Dev Log Settings', 'wp-devlog' ),         // заголовок страницы
		__( 'Settings', 'wp-devlog' ),                 // текст пункта меню
		'manage_options',            // необходимые права доступа
		'devlog-settings',           // слаг страницы
		'devlog_settings_page_callback' // функция вывода страницы
	);
}
add_action( 'admin_menu', 'devlog_add_settings_page' );

// Функция вывода страницы настроек
function devlog_settings_page_callback() {
	// Сохраняем настройки
	if ( isset( $_POST['devlog_save_settings'] ) && check_admin_referer( 'devlog_settings_nonce' ) ) {
		$posts_per_page = intval( $_POST['devlog_posts_per_page'] );
		if ( $posts_per_page < 1 )
			$posts_per_page = 1;
		update_option( 'devlog_posts_per_page', $posts_per_page );
		echo '<div class="notice notice-success is-dismissible"><p>' . __( 'Settings saved.', 'wp-devlog' ) . '</p></div>';
	}

	$posts_per_page = get_option( 'devlog_posts_per_page', 5 );
	?>
	<div class="wrap">
		<h1><?php _e( 'Dev Log Settings', 'wp-devlog' ); ?></h1>
		<div class="devlog-settings-page">
			<h2><?php _e( 'Display Settings', 'wp-devlog' ); ?></h2>
			<form method="post" action="" class="devlog-settings-form">
				<?php wp_nonce_field( 'devlog_settings_nonce' ); ?>
				<table class="form-table">
					<tr>
						<th scope="row"><?php _e( 'Number of entries per page', 'wp-devlog' ); ?></th>
						<td>
							<input type="number" name="devlog_posts_per_page" value="<?php echo esc_attr( $posts_per_page ); ?>" min="1" class="regular-text">
							<p class="description"><?php _e( 'Number of Dev Log entries displayed in the dashboard widget', 'wp-devlog' ); ?></p>
						</td>
					</tr>
				</table>
				<p class="submit">
					<input type="submit" name="devlog_save_settings" class="button-primary" value="<?php _e( 'Save Settings', 'wp-devlog' ); ?>">
				</p>
			</form>
		</div>
	</div>
	<?php
}

// Регистрируем настройки при активации плагина
function devlog_plugin_activation() {
	add_option( 'devlog_posts_per_page', 5 );
}
register_activation_hook( __FILE__, 'devlog_plugin_activation' );

add_action( 'init', function () {
	register_post_type( 'devlog', array(
		'labels' => array(
			'name' => 'Dev Log',
			'singular_name' => 'Dev Log',
			'menu_name' => 'Dev Log',
			'all_items' => __( 'All Dev Logs', 'wp-devlog' ),
			'edit_item' => __( 'Edit Dev Log', 'wp-devlog' ),
			'view_item' => __( 'View Dev Log', 'wp-devlog' ),
			'view_items' => __( 'View Dev Logs', 'wp-devlog' ),
			'add_new_item' => __( 'Add New Dev Log', 'wp-devlog' ),
			'new_item' => __( 'New Dev Log', 'wp-devlog' ),
			'parent_item_colon' => __( 'Parent Dev Log:', 'wp-devlog' ),
			'search_items' => __( 'Search Dev Logs', 'wp-devlog' ),
			'not_found' => __( 'No dev logs found', 'wp-devlog' ),
			'not_found_in_trash' => __( 'No dev logs found in trash', 'wp-devlog' ),
			'archives' => __( 'Dev Log Archives', 'wp-devlog' ),
			'attributes' => __( 'Dev Log Attributes', 'wp-devlog' ),
			'insert_into_item' => __( 'Insert into dev log', 'wp-devlog' ),
			'uploaded_to_this_item' => __( 'Uploaded to this dev log', 'wp-devlog' ),
			'filter_items_list' => __( 'Filter dev logs list', 'wp-devlog' ),
			'filter_by_date' => __( 'Filter dev logs by date', 'wp-devlog' ),
			'items_list_navigation' => __( 'Dev Logs list navigation', 'wp-devlog' ),
			'items_list' => __( 'Dev Logs list', 'wp-devlog' ),
			'item_published' => __( 'Dev Log published.', 'wp-devlog' ),
			'item_published_privately' => __( 'Dev Log published privately.', 'wp-devlog' ),
			'item_reverted_to_draft' => __( 'Dev Log reverted to draft.', 'wp-devlog' ),
			'item_scheduled' => __( 'Dev Log scheduled.', 'wp-devlog' ),
			'item_updated' => __( 'Dev Log updated.', 'wp-devlog' ),
			'item_link' => __( 'Dev Log Link', 'wp-devlog' ),
			'item_link_description' => __( 'Link to dev log.', 'wp-devlog' ),
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
		__( 'Website Changes', 'wp-devlog' ), // Заголовок виджета
		'devlog_dashboard_widget_callback' // Callback функция для отображения контента
	);
}
add_action( 'wp_dashboard_setup', 'devlog_add_dashboard_widget' );

// Функция для отображения контента виджета
function devlog_dashboard_widget_callback() {

	$args = [ 
		'post_type' => "devlog",
		'posts_per_page' => DEVLOG_POSTS_PER_PAGE,
	];

	if ( ! class_exists( 'WP_Query' ) ) {
		echo '<p>' . __( 'Error: WP_Query class not found. WordPress may not be working correctly.', 'wp-devlog' ) . '</p>';
		return;
	}

	$query = new WP_Query( $args );

	$posts = $query->posts;
	$first = 0;

	$current_date = new DateTime();

	$full_posts = '';

	echo '<div id="devlog-posts-container">';

	foreach ( $posts as $post ) {

		$post_date = new DateTime( $post->post_date );
		$postdate = $post_date->format( 'd.m.Y' );

		$processed_content = preg_replace_callback( '/<img(.*?)src=["\'](.*?)["\'](.*?)>/i', function ($matches) {
			$img_url = $matches[2];
			$full_img_url = preg_replace( '~-(?:\d+x\d+|scaled|rotated)~', '', $img_url );
			return '<a href="' . $full_img_url . '" target="_blank"><img' . $matches[1] . 'src="' . $full_img_url . '"' . $matches[3] . '></a>';
		}, $post->post_content );

		$full_postcontent = apply_filters( 'the_content', $processed_content );
		$full_postcontent = wpautop( $full_postcontent );

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

		$full_posts .= "<div class='devlog-full-post' id='{$post->ID}' style='display:none;'>";
		$full_posts .= "<div class='devlog-full-post devlog-post-content'>{$full_postcontent}</div>
		</div>";
	}

	echo '</div>';

	echo '<div id="devlog-more-posts-container"></div>';
	echo '<div id="devlog-load-more-wrap">';
	echo '<button id="devlog-load-more" class="button" data-offset="' . DEVLOG_POSTS_PER_PAGE . '">' . __( 'Load More', 'wp-devlog' ) . '</button>';
	echo '<span id="devlog-loading" style="display:none;">' . __( 'Loading...', 'wp-devlog' ) . '</span>';
	echo '</div>';

	echo $full_posts;
}

function my_enqueue( $hook ) {
	// Для виджета на дашборде
	if ( 'index.php' == $hook ) {
		// Убедимся, что Thickbox подключен
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_style( 'thickbox' );

		wp_enqueue_style( 'devlog', plugins_url( '/devlog-style.css', __FILE__ ) );
		wp_enqueue_script( 'devlog-script', plugins_url( '/devlog-script.js', __FILE__ ), array( 'jquery', 'thickbox' ), '1.0', true );

		// Передаем данные в JavaScript
		wp_localize_script( 'devlog-script', 'devlog_ajax', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'devlog-ajax-nonce' )
		) );

		// Локализация строк для JavaScript
		wp_localize_script( 'devlog-script', 'devlog_i18n', array(
			'no_more_entries' => __( 'No more entries', 'wp-devlog' ),
			'load_error' => __( 'Error loading. Please try again.', 'wp-devlog' )
		) );
	}

	// Для страницы настроек
	if ( strpos( $hook, 'page_devlog-settings' ) !== false ) {
		wp_enqueue_style( 'devlog', plugins_url( '/devlog-style.css', __FILE__ ) );
	}
}
add_action( 'admin_enqueue_scripts', 'my_enqueue' );

// Функция для обработки AJAX запросов
function devlog_load_more_posts() {
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'devlog-ajax-nonce' ) ) {
		wp_send_json_error( __( 'Security error. Refresh the page and try again.', 'wp-devlog' ) );
		wp_die();
	}

	$offset = isset( $_POST['offset'] ) ? intval( $_POST['offset'] ) : 0;

	$args = [ 
		'post_type' => 'devlog',
		'posts_per_page' => DEVLOG_POSTS_PER_PAGE,
		'offset' => $offset
	];

	if ( ! class_exists( 'WP_Query' ) ) {
		wp_send_json_error( __( 'Error: WP_Query class not found. WordPress may not be working correctly.', 'wp-devlog' ) );
		wp_die();
	}

	$query = new WP_Query( $args );
	$posts = $query->posts;
	$output = '';
	$full_posts = '';

	$current_date = new DateTime();

	foreach ( $posts as $post ) {
		$post_date = new DateTime( $post->post_date );
		$postdate = $post_date->format( 'd.m.Y' );

		$processed_content = preg_replace_callback( '/<img(.*?)src=["\'](.*?)["\'](.*?)>/i', function ($matches) {
			$img_url = $matches[2];
			$full_img_url = preg_replace( '~-(?:\d+x\d+|scaled|rotated)~', '', $img_url );
			return '<a href="' . $full_img_url . '" target="_blank"><img' . $matches[1] . 'src="' . $full_img_url . '"' . $matches[3] . '></a>';
		}, $post->post_content );

		$full_postcontent = apply_filters( 'the_content', $processed_content );
		$full_postcontent = wpautop( $full_postcontent );

		if ( strpos( $processed_content, '<!--more-->' ) !== false ) {
			$parts = explode( '<!--more-->', $processed_content );
			$content_before_more = $parts[0];
			$postcontent = wp_strip_all_tags( $content_before_more, true );
			$postcontent = apply_filters( 'the_content', $postcontent );
		} else {
			$postcontent = wp_strip_all_tags( $full_postcontent, true );
			$postcontent = apply_filters( 'the_content', $postcontent );
		}

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

		$full_posts .= "<div class='devlog-full-post' id='{$post->ID}' style='display:none;'>";
		$full_posts .= "<div class='devlog-full-post devlog-post-content'>{$full_postcontent}</div>
		</div>";
	}

	$response = array(
		'posts' => $output,
		'full_posts' => $full_posts,
		'has_more' => ( $offset + count( $posts ) < $query->found_posts )
	);

	wp_send_json( $response );
	wp_die();
}
add_action( 'wp_ajax_devlog_load_more', 'devlog_load_more_posts' );
add_action( 'wp_ajax_nopriv_devlog_load_more', 'devlog_load_more_posts' );