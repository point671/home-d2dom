<?php
/**
 * Ddom functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Ddom
 */

if (!defined('_S_VERSION')) {
    // Replace the version number of the theme on each release.
    define('_S_VERSION', '1.0.0');
}

/**
 * Устанавливает параметры темы и регистрирует поддержку различных функций WordPress.
 *
 * Обратите внимание, что эта функция подключена к хуку after_setup_theme, который
 * выполняется до хука init. Хук init слишком поздний для некоторых функций, таких как
 * указание поддержки миниатюр записей.
 */
function ddom_setup()
{
    /*
     * Делает тему доступной для перевода.
     * Переводы могут быть размещены в каталоге /languages/.
     * Если вы создаете тему на основе Ddom, используйте поиск и замену
     * чтобы изменить 'ddom' на название вашей темы во всех шаблонных файлах.
     */
    load_theme_textdomain('ddom', get_template_directory() . '/languages');

    // Добавляет ссылки на RSS-ленты постов и комментариев в head.
    add_theme_support('automatic-feed-links');

    /*
     * Позволяет WordPress управлять заголовком документа.
     * Добавляя поддержку темы, мы заявляем, что эта тема не использует
     * жестко заданный <title> тег в заголовке документа, и ожидаем, что WordPress
     * предоставит его для нас.
     */
    add_theme_support('title-tag');

    /*
     * Включает поддержку миниатюр записей для постов и страниц.
     *
     * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
     */
    add_theme_support('post-thumbnails');

    // Эта тема использует wp_nav_menu() в одном месте.
    register_nav_menus(
        array(
            'menu-1' => esc_html__('Primary', 'ddom'),
        )
    );

    /*
     * Переключает разметку ядра WordPress для формы поиска, формы комментариев и комментариев
     * на вывод валидного HTML5.
     */
    add_theme_support(
        'html5',
        array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        )
    );

    // Настраивает фоновую функцию ядра WordPress.
    add_theme_support(
        'custom-background',
        apply_filters(
            'ddom_custom_background_args',
            array(
                'default-color' => 'ffffff',
                'default-image' => '',
            )
        )
    );

    // Добавляет поддержку избирательного обновления для виджетов.
    add_theme_support('customize-selective-refresh-widgets');

    /**
     * Добавляет поддержку для пользовательского логотипа ядра.
     *
     * @link https://codex.wordpress.org/Theme_Logo
     */
    add_theme_support(
        'custom-logo',
        array(
            'height' => 250,
            'width' => 250,
            'flex-width' => true,
            'flex-height' => true,
        )
    );
}
add_action('after_setup_theme', 'ddom_setup');

/**
 * Устанавливает ширину контента в пикселях, исходя из дизайна и таблицы стилей темы.
 *
 * Приоритет 0, чтобы сделать его доступным для более низкоприоритетных обратных вызовов.
 *
 * @global int $content_width
 */
function ddom_content_width()
{
    $GLOBALS['content_width'] = apply_filters('ddom_content_width', 640);
}
add_action('after_setup_theme', 'ddom_content_width', 0);

/**
 * Регистрация области виджетов.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function ddom_widgets_init()
{
    register_sidebar(
        array(
            'name' => esc_html__('Sidebar', 'ddom'),
            'id' => 'sidebar-1',
            'description' => esc_html__('Add widgets here.', 'ddom'),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget' => '</section>',
            'before_title' => '<h2 class="widget-title">',
            'after_title' => '</h2>',
        )
    );
}
add_action('widgets_init', 'ddom_widgets_init');

/**
 * Enqueue scripts and styles.
 */
// function ddom_scripts() {
// 	wp_enqueue_style( 'ddom-style', get_stylesheet_uri(), array(), _S_VERSION );
// 	wp_style_add_data( 'ddom-style', 'rtl', 'replace' );

// 	wp_enqueue_script( 'ddom-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true );

// 	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
// 		wp_enqueue_script( 'comment-reply' );
// 	}
// }
// add_action( 'wp_enqueue_scripts', 'ddom_scripts' );


// Подключение стилей


// правильный способ подключить стили и скрипты
add_action('wp_enqueue_scripts', 'theme_name_scripts');
// add_action('wp_print_styles', 'theme_name_scripts'); // можно использовать этот хук он более поздний
function theme_name_scripts()
{
    wp_enqueue_style('style-name', get_stylesheet_uri());
    wp_enqueue_script('script-name', get_template_directory_uri() . '/js/app.js', array(), '1.0.0', true);
}



// Подключаем или заменяем ресурсы на лендинге
add_action('wp_enqueue_scripts', 'custom_landing_script', 20);
function custom_landing_script()
{
    if (is_page('premium-otdelka-domov')) {

        // Отключаем стандартный стиль
        wp_dequeue_style('style-name');
        wp_deregister_style('style-name');

        // Подключаем CSS лендинга
        wp_enqueue_style(
            'landing-style',
            get_template_directory_uri() . '/style/style.css',
            array(),
            '1.0',
            'all'
        );

        // Вариант 1: если нужен app.js → подключаем landing-script ПОСЛЕ него
        $need_app_js = false; // меняем на false, если app.js не нужен

        if ($need_app_js) {
            wp_enqueue_script(
                'landing-script',
                get_template_directory_uri() . '/js/new-landing.js',
                array('script-name'), // грузится после app.js
                '1.0',
                true
            );
        } else {
            // Вариант 2: если app.js НЕ нужен → убираем его
            wp_dequeue_script('script-name');
            wp_deregister_script('script-name');

            wp_enqueue_script(
                'landing-script',
                get_template_directory_uri() . '/js/new-landing.js',
                array(), // грузится самостоятельно
                '1.0',
                true
            );
        }
    }
}


// Подключаем стили для новой главной страницы (new-main-d2dom)
add_action('wp_enqueue_scripts', 'custom_new_main_assets', 20);
function custom_new_main_assets()
{
    // Проверяем, используется ли наш специальный шаблон
    if (is_page_template('main-d2dom-test.php')) {

        // Отключаем стандартный стиль
        wp_dequeue_style('style-name');
        wp_deregister_style('style-name');

        // Подключаем CSS новой главной страницы
        wp_enqueue_style(
            'new-main-style',
            get_template_directory_uri() . '/pages/d2dom/style.css',
            array(),
            '1.0',
            'all'
        );

        // Отключаем app.js (не нужен на этой странице)
        wp_dequeue_script('script-name');
        wp_deregister_script('script-name');

        // Подключаем JS новой главной страницы
        wp_enqueue_script(
            'new-main-script',
            get_template_directory_uri() . '/pages/d2dom/script.js',
            array(),
            '1.0',
            true
        );
    }
}


/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if (defined('JETPACK__VERSION')) {
    require get_template_directory() . '/inc/jetpack.php';
}



//Скрипты для фильтров и таксаномий


add_action('wp_enqueue_scripts', 'my_theme_enqueue_styles');
function my_theme_enqueue_styles()
{


    wp_enqueue_script('scripts-js', get_stylesheet_directory_uri() . '/js/scripts.js', ['jquery'], '', true);
    wp_localize_script('scripts-js', 'variables', [
        'ajax_url' => admin_url('admin-ajax.php'),
    ]);
}




add_action('init', 'register_custom_post_types');
function register_custom_post_types()
{
    register_post_type('movie', [
        'labels' => [
            'name' => 'Movie',
            'singular_name' => 'Movie',
            'menu_name' => 'Home',
        ],
        'public' => true,
        'publicly_queryable' => true,
        'menu_icon' => '',
        'has_archive' => false,
        'rewrite' => ['slug' => 'proekty-domov'],
        'supports' => [
            'title',
            'editor',
            'thumbnail',
        ],

        'taxonomies' => ['post_tag'], // Add support for tags
    ]);

    // Регистрируем тип записи "bani"
    register_post_type('bani', [
        'labels' => [
            'name' => 'Bani',  // Множественное название типа записи
            'singular_name' => 'Banya', // Единственное название типа записи
            'menu_name' => 'Bani', // Название в меню админ-панели
        ],
        'public' => true,
        'publicly_queryable' => true,
        'menu_icon' => 'dashicons-admin-home', // Пример иконки
        'has_archive' => false,
        'rewrite' => ['slug' => 'bani'],
        'supports' => [
            'title',
            'editor',
            'thumbnail',
        ],
        'taxonomies' => ['post_tag'], // Add support for tags
    ]);


    // Регистрация типа записи "portfolio"
    register_post_type('portfolio', [
        'labels' => [
            'name' => 'portfolio', // Название типа записей во множественном числе
            'singular_name' => 'portfolio', // Название типа записей в единственном числе
            'menu_name' => 'portfolio', // Название в меню админки
        ],
        'public' => true,
        'publicly_queryable' => true,
        'menu_icon' => 'dashicons-admin-home', // Пример иконки
        'has_archive' => false,
        'rewrite' => ['slug' => 'portfolio'],
        'supports' => [
            'title',
            'editor',
            'thumbnail',
        ],
    ]);
}

add_action('init', 'register_taxonomies');
function register_taxonomies()
{
    register_taxonomy('movie_type', ['movie'], [
        'hierarchical' => true,
        'labels' => [
            'name' => __('Categories'),
            'singular_name' => __('Category'),
            'menu_name' => __('Categories'),
        ],
        'show_ui' => true,
        'show_admin_column' => true,
        'rewrite' => ['slug' => 'proekty-domov'],
    ]);

    // Регистрируем таксономию для типа записи "bani"
    register_taxonomy('bani_type', ['bani'], [
        'hierarchical' => true,
        'labels' => [
            'name' => __('Categories'),
            'singular_name' => __('Category'),
            'menu_name' => __('Categories'),
        ],
        'show_ui' => true,
        'show_admin_column' => true,
        'rewrite' => ['slug' => 'type'],
    ]);

    // Регистрация таксономии для типа записи "portfolio"
    register_taxonomy('portfolio_type', ['portfolio'], [
        'hierarchical' => true,
        'labels' => [
            'name' => __('Categories'),
            'singular_name' => __('Category'),
            'menu_name' => __('Categories'),
        ],
        'show_ui' => true,
        'show_admin_column' => true,
        'rewrite' => ['slug' => 'type'],
    ]);
}



//Создание столбцов с названиями проектов в админке 
add_filter('manage_movie_posts_columns', 'add_movie_custom_columns');
function add_movie_custom_columns($columns)
{
    $columns['movie_name_acf'] = 'Имя проекта'; // Добавляем новый столбец
    return $columns;
}

add_action('manage_movie_posts_custom_column', 'fill_movie_custom_columns', 10, 2);
function fill_movie_custom_columns($column, $post_id)
{
    if ($column === 'movie_name_acf') {
        $rating = get_field('home-post-nazvanie_proekta', $post_id); // Получаем значение ACF поля
        echo $rating ? esc_html($rating) : '—'; // Выводим значение или прочерк
    }
}


// Добавление нового столбца для поля "Размер" для типа записи "bani"
add_filter('manage_bani_posts_columns', 'add_bani_custom_columns');
function add_bani_custom_columns($columns)
{
    $columns['bani_name_acf'] = 'Имя проекта'; // Новый столбец для поля "Размер"
    return $columns;
}

// Заполнение столбца данными из ACF
add_action('manage_bani_posts_custom_column', 'fill_bani_custom_columns', 10, 2);
function fill_bani_custom_columns($column, $post_id)
{
    if ($column === 'bani_name_acf') {
        $size = get_field('bani_home-post-nazvanie_proekta', $post_id); // Получаем значение ACF поля
        echo $size ? esc_html($size) : '—'; // Выводим значение поля или прочерк
    }
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 












add_action('wp_ajax_filter_posts', 'filter_posts');
add_action('wp_ajax_nopriv_filter_posts', 'filter_posts');


function filter_posts()
{
    $args = [
        'post_type' => 'movie',
        'posts_per_page' => 18,
        'meta_key' => 'ploshhad',
        'orderby' => 'meta_value_num',
        'order' => 'ASC',
        'post_status' => 'publish',

        'paged' => $_POST['page'] ?? 1, // Номер страницы из POST
        'tax_query' => [],
    ];

    // Получаем метки из POST-запроса
    $materialy = $_POST['materialy'];
    $etazhej = $_POST['etazhej'];
    $spalen = $_POST['spalen'];
    $kolichestvo_sanuzlov = $_POST['kolichestvo_sanuzlov'];
    $osobennosti = $_POST['osobennosti'] ?? [];
    $stili = $_POST['stili'];
    $tip_doma = $_POST['tip_doma'];
    $ploshhad = $_POST['ploshhad'];
    $kolichestvosemei = $_POST['kolichestvosemei'];


    // Проверяем наличие выбранных меток и добавляем их в запрос
    if (!empty($materialy)) {
        $args['tax_query'][] = [
            'taxonomy' => 'post_tag', // Замените на соответствующую таксономию
            'field' => 'slug', // или 'term_id', если передается ID метки
            'terms' => $materialy,
        ];
    }
    if (!empty($etazhej)) {
        $args['tax_query'][] = [
            'taxonomy' => 'post_tag',
            'field' => 'slug',
            'terms' => $etazhej,
        ];
    }
    if (!empty($spalen)) {
        $args['tax_query'][] = [
            'taxonomy' => 'post_tag',
            'field' => 'slug',
            'terms' => $spalen,
        ];
    }
    if (!empty($kolichestvo_sanuzlov)) {
        $args['tax_query'][] = [
            'taxonomy' => 'post_tag',
            'field' => 'slug',
            'terms' => $kolichestvo_sanuzlov,
        ];
    }
    if (!empty($osobennosti)) {
        $args['tax_query'][] = [
            'taxonomy' => 'post_tag',
            'field' => 'slug',
            'terms' => (array) $osobennosti,
            'operator' => 'AND', // все выбранные особенности должны совпадать
        ];
    }
    if (!empty($stili)) {
        $args['tax_query'][] = [
            'taxonomy' => 'post_tag',
            'field' => 'slug',
            'terms' => $stili,
        ];
    }
    if (!empty($tip_doma)) {
        $args['tax_query'][] = [
            'taxonomy' => 'post_tag',
            'field' => 'slug',
            'terms' => $tip_doma,
        ];
    }

    if (!empty($ploshhad)) {
        $args['tax_query'][] = [
            'taxonomy' => 'post_tag',
            'field' => 'slug',
            'terms' => $ploshhad,
        ];
    }
    if (!empty($kolichestvosemei)) {
        $args['tax_query'][] = [
            'taxonomy' => 'post_tag',
            'field' => 'slug',
            'terms' => $kolichestvosemei,
        ];
    }



    $movies = new WP_Query($args);

    if ($movies->have_posts()) {
        while ($movies->have_posts()) {
            $movies->the_post();
            get_template_part('template-parts/loop', 'movie');
        }
    }

    wp_die();
}
add_action('wp_ajax_filter_posts', 'filter_posts');
add_action('wp_ajax_nopriv_filter_posts', 'filter_posts');


function load_more_movies_callback()
{
    $paged = $_POST['page'];
    $args = [
        'post_type' => 'movie',
        'posts_per_page' => 18,
        'meta_key' => 'ploshhad',
        'orderby' => 'meta_value_num',
        'order' => 'ASC',
        'post_status' => 'publish',
        'paged' => $_POST['page'] ?? 1, // Номер страницы из POST
        'tax_query' => [],
    ];

    // Получаем метки из POST-запроса
    $materialy = $_POST['materialy'];
    $etazhej = $_POST['etazhej'];
    $spalen = $_POST['spalen'];
    $kolichestvo_sanuzlov = $_POST['kolichestvo_sanuzlov'];
    $osobennosti = $_POST['osobennosti'] ?? [];
    $stili = $_POST['stili'];
    $tip_doma = $_POST['tip_doma'];
    $ploshhad = $_POST['ploshhad'];
    $kolichestvosemei = $_POST['kolichestvosemei'];
    // Проверяем наличие выбранных меток и добавляем их в запрос
    if (!empty($materialy)) {
        $args['tax_query'][] = [
            'taxonomy' => 'post_tag', // Замените на соответствующую таксономию
            'field' => 'slug', // или 'term_id', если передается ID метки
            'terms' => $materialy,
        ];
    }
    if (!empty($etazhej)) {
        $args['tax_query'][] = [
            'taxonomy' => 'post_tag',
            'field' => 'slug',
            'terms' => $etazhej,
        ];
    }
    if (!empty($spalen)) {
        $args['tax_query'][] = [
            'taxonomy' => 'post_tag',
            'field' => 'slug',
            'terms' => $spalen,
        ];
    }
    if (!empty($kolichestvo_sanuzlov)) {
        $args['tax_query'][] = [
            'taxonomy' => 'post_tag',
            'field' => 'slug',
            'terms' => $kolichestvo_sanuzlov,
        ];
    }
    if (!empty($osobennosti)) {
        $args['tax_query'][] = [
            'taxonomy' => 'post_tag',
            'field' => 'slug',
            'terms' => (array) $osobennosti,
            'operator' => 'AND', // все выбранные особенности должны совпадать
        ];
    }
    if (!empty($stili)) {
        $args['tax_query'][] = [
            'taxonomy' => 'post_tag',
            'field' => 'slug',
            'terms' => $stili,
        ];
    }
    if (!empty($tip_doma)) {
        $args['tax_query'][] = [
            'taxonomy' => 'post_tag',
            'field' => 'slug',
            'terms' => $tip_doma,
        ];
    }
    if (!empty($ploshhad)) {
        $args['tax_query'][] = [
            'taxonomy' => 'post_tag',
            'field' => 'slug',
            'terms' => $ploshhad,
        ];
    }
    if (!empty($kolichestvosemei)) {
        $args['tax_query'][] = [
            'taxonomy' => 'post_tag',
            'field' => 'slug',
            'terms' => $kolichestvosemei,
        ];
    }

    // Аналогично добавьте остальные метки


    $movies = new WP_Query($args);

    if ($movies->have_posts()) {
        while ($movies->have_posts()) {
            $movies->the_post();
            get_template_part('template-parts/loop', 'movie');
        }
    }

    wp_die();
}
add_action('wp_ajax_load_more_movies', 'load_more_movies_callback');
add_action('wp_ajax_nopriv_load_more_movies', 'load_more_movies_callback');


// Регистрация скрипта и передача параметров AJAX
function enqueue_custom_scripts()
{
    wp_enqueue_script('custom-js', get_template_directory_uri() . '/js/script.js', array('jquery'), null, true);
    wp_localize_script('custom-js', 'variables', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_custom_scripts');




// Всегда опускать проекты с названием, начинающимся на "СП-", в конец выборки
// Основано на значении ACF-поля 'home-post-nazvanie_proekta'
add_filter('posts_join', function ($join, $query) {
    if (is_admin() && !wp_doing_ajax()) {
        return $join;
    }

    $postType = $query->get('post_type');

    // Применяем для любого запроса типа "movie"
    if (
        ($postType === 'movie' || (is_array($postType) && in_array('movie', $postType, true)))
    ) {
        global $wpdb;
        // Присоединяем meta как alias hmnp только для нужного ACF-поля
        if (strpos($join, ' AS hmnp ') === false && strpos($join, ' AS hmnp\n') === false) {
            $join .= " LEFT JOIN {$wpdb->postmeta} AS hmnp ON (hmnp.post_id = {$wpdb->posts}.ID AND hmnp.meta_key = 'home-post-nazvanie_proekta')";
        }
    }

    return $join;
}, 10, 2);

add_filter('posts_orderby', function ($orderby, $query) {
    if (is_admin() && !wp_doing_ajax()) {
        return $orderby;
    }

    $postType = $query->get('post_type');

    // Применяем для любого запроса типа "movie"
    if (
        ($postType === 'movie' || (is_array($postType) && in_array('movie', $postType, true)))
    ) {
        global $wpdb;
        // Проверяем ACF-название проекта; если его нет — используем post_title
        // Нормализуем строку: приводим к верхнему регистру, убираем пробелы, приводим разные тире к '-'
        $caseOrder = "CASE WHEN LEFT(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(LTRIM(COALESCE(hmnp.meta_value, {$wpdb->posts}.post_title))), ' ', ''), '‑', '-'), '–', '-'), '—', '-'), '−', '-'), 3) = 'СП-' THEN 1 ELSE 0 END ASC";

        // Добавляем приоритетный CASE перед существующим ORDER BY
        $orderby = $orderby ? ($caseOrder . ', ' . $orderby) : $caseOrder;
    }

    return $orderby;
}, 10, 2);

// Всегда опускать проекты с названием, начинающимся на "СП-", в конец выборки ------ КОНЕЦ



if (function_exists('acf_add_options_page')) {
    $args = array(
        'page_title' => 'Футер',
        'menu_title' => '',
        'menu_slug' => 'Options',
        'post_id' => 'options',
    );
    acf_add_options_page($args);
}
if (function_exists('acf_add_options_page')) {
    $args = array(
        'page_title' => 'Контент под постом',
        'menu_title' => '',
        'menu_slug' => 'Options-down-post',
        'post_id' => 'options-down-post',
    );
    acf_add_options_page($args);
}



add_action('wp_ajax_load_more_posts', 'load_more_posts');
add_action('wp_ajax_nopriv_load_more_posts', 'load_more_posts');

function load_more_posts()
{
    $page = $_POST['page'];
    $category = $_POST['category'];

    $args = array(
        'post_type' => 'post',
        'posts_per_page' => 3, // количество постов, загружаемых за раз
        'paged' => $page,
        'category_name' => $category
    );

    $posts = new WP_Query($args);

    ob_start();

    if ($posts->have_posts()) {
        while ($posts->have_posts()) {
            $posts->the_post();
            // Здесь выведите HTML-разметку для каждого загруженного поста, согласно вашему текущему шаблону
            // Например, можно использовать функции the_field(), the_title(), the_content() и т.д.
            get_template_part('template-parts/portfolio', get_post_format());
        }
    }

    wp_reset_postdata();

    $response = array(
        'success' => true,
        'data' => array(
            'posts' => ob_get_clean(),
            'has_more' => $posts->max_num_pages > $page // Проверяем, есть ли ещё посты
        )
    );

    wp_send_json($response);
    exit;
}



add_image_size('custom-size', 470, 315, true); // Замените 'custom-size' и размеры на нужные вам




add_filter('wpcf7_load_css', '__return_false');

// Remove <p> and <br/> from Contact Form 7
add_filter('wpcf7_autop_or_not', '__return_false');

add_filter('wpcf7_form_elements', function ($content) {
    $content = preg_replace('/<(span).*?class="\s*(?:.*\s)?wpcf7-form-control-wrap(?:\s[^"]+)?\s*"[^\>]*>(.*)<\/\1>/i', '\2', $content);

    return $content;
});

add_filter('wpcf7_form_elements', 'custom_remove_span_from_checkbox', 10, 1);

function custom_remove_span_from_checkbox($form)
{
    // Находим все вхождения <span> и удаляем их
    // $form = preg_replace( '/<label[^>]+>/', '', $form );
    // $form = preg_replace( '/<\/label>/', '', $form );
    $form = preg_replace('/<span[^>]+>/', '', $form);
    $form = preg_replace('/<\/span>/', '', $form);
    return $form;
}

add_filter('post_type_link', 'change_movie_permalink_structure', 10, 2);
function change_movie_permalink_structure($post_link, $post)
{
    if ('movie' === $post->post_type) {
        // Получаем рубрики записи
        $categories = get_the_category($post->ID);
        if (!empty($categories)) {
            // Берем первую рубрику и используем ее slug в URL
            $category_slug = $categories[0]->slug;
            $post_link = str_replace('movie', $category_slug, $post_link);
        }
    }
    return $post_link;
}

add_filter('wpcf7_validate_tel*', 'custom_phone_validation', 20, 2);
function custom_phone_validation($result, $tag)
{
    $name = $tag->name;
    if ($name == 'tel-723') {
        $phone = isset($_POST[$name]) ? trim($_POST[$name]) : '';
        if (!preg_match('/^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,10}$/', $phone)) {
            $result->invalidate($tag, "Введите корректный номер телефона.");
        }
    }
    return $result;
}


add_filter('wpcf7_validate_tel*', 'custom_phone_validationBackCall', 20, 2);
function custom_phone_validationBackCall($result, $tag)
{
    $name = $tag->name;
    if ($name == 'tel') {
        $phone = isset($_POST[$name]) ? trim($_POST[$name]) : '';
        if (!preg_match('/^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,10}$/', $phone)) {
            $result->invalidate($tag, "Введите корректный номер телефона111.");
        }
    }
    return $result;
}

function enqueue_custom_js()
{
    wp_enqueue_script('jquery'); // подключаем jQuery, если ещё не подключён
    wp_enqueue_script('main-js', get_template_directory_uri() . '/js/main.js', ['jquery'], null, true);

    // Добавляем переменную ajaxurl, которая будет доступна в вашем скрипте
    wp_localize_script('main-js', 'ajaxurl', admin_url('admin-ajax.php'));
}
add_action('wp_enqueue_scripts', 'enqueue_custom_js');


function enqueue_my_scripts()
{
    if (is_page('portfolio') || is_singular('portfolio')) {
        wp_enqueue_script('my-custom-js', get_template_directory_uri() . '/js/custom.js', array('jquery'), '1.0', true);

        // Передаём ajaxurl в скрипт
        wp_localize_script('my-custom-js', 'ajaxurl', admin_url('admin-ajax.php'));
    }
}
add_action('wp_enqueue_scripts', 'enqueue_my_scripts');

function load_more_portfolio_posts()
{
    $paged = isset($_POST['page']) ? intval($_POST['page']) : 1;

    $args = [
        'post_type' => 'portfolio',
        'posts_per_page' => 6,
        'paged' => $paged,
    ];

    $portfolio = new WP_Query($args);

    if ($portfolio->have_posts()) {
        while ($portfolio->have_posts()) {
            $portfolio->the_post();
            get_template_part('template-parts/loop', 'portfolio');
        }
    } else {
        wp_send_json(false);
    }

    wp_die(); // Останавливаем выполнение скрипта
}
add_action('wp_ajax_load_more_portfolio', 'load_more_portfolio_posts');
add_action('wp_ajax_nopriv_load_more_portfolio', 'load_more_portfolio_posts');



//Убрать баг  когда переходит по крошкам на type
add_filter('wpseo_breadcrumb_links', function ($links) {
    foreach ($links as &$link) {
        $link['url'] = str_replace('/type/', '/', $link['url']); // Убираем /type/
    }
    return $links;
});











add_action('wp_ajax_load_more_movies1', 'load_more_movies1');
add_action('wp_ajax_nopriv_load_more_movies1', 'load_more_movies1');

function load_more_movies1()
{
    // $paged = isset($_POST['page']) ? intval($_POST['page']) : 1;



    $page = $_POST['page'];

    $slugsData = isset($_POST['slugsData']) ? json_decode(stripslashes($_POST['slugsData'])) : [];

    $MinStoimost = isset($_POST['MinStoimost']) ? floatval($_POST['MinStoimost']) : 0;
    $MaxStoimost = isset($_POST['MaxStoimost']) ? floatval($_POST['MaxStoimost']) : 0;



    // Валидация slug-ов
    $slugsArray = array_map('sanitize_text_field', $slugsData);

    if (empty($slugsArray)) {
        echo 0;
        wp_die();
    }

    $args = array(
        'post_type' => 'movie',
        'posts_per_page' => 9,
        'paged' => $page,
        'meta_key' => 'ploshhad',
        'orderby' => 'meta_value_num',
        'order' => 'ASC',
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => 'post-stoimost', // Название ACF-поля
                'value' => array($MinStoimost, $MaxStoimost), // Диапазон: текущая стоимость ± 1 млн
                'compare' => 'BETWEEN',
                'type' => 'NUMERIC' // Указываем, что сравниваем числа
            )
        ),
        'tax_query' => array(
            array(
                'taxonomy' => 'post_tag',
                'field' => 'slug',
                'terms' => $slugsArray, // Массив slug-ов
                'operator' => 'IN' // Включает все переданные slug-ы
            )
        )
    );



    $movies_query = new WP_Query($args);

    if ($movies_query->have_posts()):
        while ($movies_query->have_posts()):
            $movies_query->the_post();
            get_template_part('template-parts/loop', 'movie');
        endwhile;
        wp_reset_postdata();
    else:
        echo 0;
    endif;

    wp_die();
}



add_filter('wpseo_breadcrumb_links', 'custom_project_breadcrumbs');
function custom_project_breadcrumbs($links)
{
    if (is_singular('movie')) {
        // Проверяем, есть ли уже рубрика в крошках
        $has_category = false;
        foreach ($links as $link) {
            if (isset($link['term']) && $link['term']->taxonomy === 'movie_type') {
                $has_category = true;
                break;
            }
        }

        // Если рубрики нет, добавляем "Дома"
        if (!$has_category) {
            array_splice($links, 1, 0, array(
                array(
                    'text' => 'Дома',
                    'url' => site_url('/proekty-domov/'),
                )
            ));
        }
    }




    if (is_singular('portfolio')) {
        // Проверяем, есть ли уже рубрика в крошках
        $has_category = false;
        foreach ($links as $link) {
            if (isset($link['term']) && $link['term']->taxonomy === 'portfolio_type') {
                $has_category = true;
                break;
            }
        }

        // Если рубрики нет, добавляем "Портфолио"
        if (!$has_category) {
            array_splice($links, 1, 0, array(
                array(
                    'text' => 'Портфолио',
                    'url' => site_url('/portfolio/'),
                )
            ));
        }
    }


    if (is_singular('bani')) {
        // Проверяем, есть ли уже рубрика в крошках
        $has_category = false;
        foreach ($links as $link) {
            if (isset($link['term']) && $link['term']->taxonomy === 'bani_type') {
                $has_category = true;
                break;
            }
        }

        // Если рубрики нет, добавляем "Бани"
        if (!$has_category) {
            array_splice($links, 1, 0, array(
                array(
                    'text' => 'Бани',
                    'url' => site_url('/bani/'),
                )
            ));
        }
    }




    return $links;
}


/* Добавление картинки в админку для проектов домов*/

// Добавляем колонку с изображением в админке
add_filter('manage_movie_posts_columns', 'add_movie_image_column');
function add_movie_image_column($columns)
{
    $new_columns = array();
    foreach ($columns as $key => $value) {
        if ($key == 'title') {
            $new_columns['movie_image'] = 'Изображение';
        }
        $new_columns[$key] = $value;
    }
    return $new_columns;
}

// Выводим изображение из ACF поля внутри повторителя
add_action('manage_movie_posts_custom_column', 'show_movie_image_column', 10, 2);
function show_movie_image_column($column_name, $post_id)
{
    if ($column_name == 'movie_image') {

        // Получаем повторитель
        $repeater = get_field('kartinka-home-project-home', $post_id);

        if ($repeater && is_array($repeater)) {
            // Берем первую запись из повторителя
            $first_row = $repeater[0];

            // Получаем изображение из поля внутри повторителя
            $image = $first_row['kartinka-home-project-home-img'] ?? '';

            if ($image) {
                // Если поле вернуло массив (формат "Image Array")
                if (is_array($image)) {
                    echo '<img src="' . esc_url($image['sizes']['thumbnail']) . '" alt="" style="max-width: 150px; height: auto;" />';
                }
                // Если поле вернуло URL (формат "Image URL")
                else {
                    echo '<img src="' . esc_url($image) . '" alt="" style="max-width: 150px; height: auto;" />';
                }
            } else {
                echo '—';
            }
        } else {
            echo '—';
        }
    }
}

// Делаем колонку сортируемой (опционально)
add_filter('manage_edit-movie_sortable_columns', 'movie_image_sortable_column');
function movie_image_sortable_column($columns)
{
    $columns['movie_image'] = 'movie_image';
    return $columns;
}

/*Конец Добавление картинки в админку для проектов домов*/


//Код добавления прелоудера шрифтов

// УДАЛИТЕ старую функцию my_theme_enqueue_google_fonts(), она больше не нужна.

/**
 * Оптимизированное подключение Google Fonts (preconnect, preload, async load).
 * Исключает страницу 'premium-otdelka-domov'.
 */

function my_theme_add_optimized_google_fonts()
{
    if (is_page('premium-otdelka-domov')) {
        return;
    }

    $gf_url = 'https://fonts.googleapis.com/css2'
        . '?family=Inter:wght@100..900'
        . '&family=Roboto:wght@400;700'
        . '&family=Cormorant+Garamond:ital,wght@0,300..700;1,300..700'
        . '&display=swap';

    echo '<link rel="preconnect" href="https://fonts.googleapis.com">';
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';

    echo '<link rel="preload" as="style" href="' . esc_url($gf_url) . '">';
    echo '<link rel="stylesheet" href="' . esc_url($gf_url) . '" media="print" onload="this.media=\'all\'">';

    echo '<noscript><link rel="stylesheet" href="' . esc_url($gf_url) . '"></noscript>';
}
add_action('wp_head', 'my_theme_add_optimized_google_fonts', 5);






//Конец кода добавления прелоудера шрифтов




// Добавление прелоудера на главную

function dg_to_webp_express_url(string $jpeg_url): string
{
    $webp = preg_replace('#/wp-content/uploads/#', '/wp-content/webp-express/webp-images/uploads/', $jpeg_url);
    return str_ends_with(strtolower($webp), '.webp') ? $webp : $webp . '.webp';
}

function dg_build_mobile_hero_srcset(int $attachment_id): array
{
    $meta = wp_get_attachment_metadata($attachment_id);
    if (!$meta || empty($meta['file']))
        return ['srcset' => '', 'href' => ''];

    $uploads = wp_get_upload_dir();
    $baseurl = trailingslashit($uploads['baseurl']);
    $dir = trailingslashit(dirname($meta['file']));

    // Жёстко берём только эти 4 размера, и только если они реально существуют
    $map = [
        'hero-640' => 640,
        'hero-960' => 960,
        'hero-1280' => 1280,
        'hero-1600' => 1600,
    ];

    $candidates = [];
    foreach ($map as $size_name => $w) {
        if (!empty($meta['sizes'][$size_name]['file'])) {
            $jpg = $baseurl . $dir . $meta['sizes'][$size_name]['file']; // например ...-640x405.jpg
            $candidates[$w] = [
                'w' => $w,
                'url_webp' => dg_to_webp_express_url($jpg),               // ...-640x405.jpg.webp
            ];
        }
    }

    if (!$candidates)
        return ['srcset' => '', 'href' => ''];

    ksort($candidates, SORT_NUMERIC);
    $parts = [];
    foreach ($candidates as $c) {
        $parts[] = $c['url_webp'] . ' ' . $c['w'] . 'w';
    }
    $last = end($candidates);

    return [
        'srcset' => implode(', ', $parts),
        'href' => $last['url_webp'], // 1600-кандидат
    ];
}



add_action('wp_head', function () {
    // if ( ! is_front_page() ) return;
    $img_url = 'https://derevgroup.ru/wp-content/uploads/2025/11/derevgroup-scaled-1.jpg';
    $img_id = attachment_url_to_postid($img_url);
    if (!$img_id)
        return;

    $m = dg_build_mobile_hero_srcset($img_id);
    if (empty($m['srcset']))
        return; // не грузим лишнее, пока нет размеров

    echo '<link rel="preload" as="image" type="image/webp" media="(max-width: 768px)" href="' . esc_url($m['href']) . '" imagesrcset="' . esc_attr($m['srcset']) . '" imagesizes="100vw">' . "\n";
}, 1);


// Конец добавление прелоудера на главную



//Кастом размеры для проектов
function my_custom_image_sizes()
{
    // Формат: 'название', ширина, высота, жесткая обрезка (true)

    // Размер для обычных экранов (чуть округлим 394x591 до 400x600)
    add_image_size('catalog-card', 400, 600, true);

    // Размер для Retina экранов (2x)
    add_image_size('catalog-card-retina', 800, 1200, true);
}
add_action('after_setup_theme', 'my_custom_image_sizes');