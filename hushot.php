<?php
/**
 * Plugin Name: Hushot - Landing Page Builder
 * Plugin URI: https://hushot.com
 * Description: Professional SaaS landing page builder for African businesses.
 * Version: 1.8.9
 * Author: Hushot
 * Author URI: https://hushot.com
 * License: GPL v2 or later
 * Text Domain: hushot
 */

if (!defined('ABSPATH')) exit;

define('HUSHOT_VERSION', '1.8.9');
define('HUSHOT_PATH', plugin_dir_path(__FILE__));
define('HUSHOT_URL', plugin_dir_url(__FILE__));
define('HUSHOT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('HUSHOT_PLUGIN_URL', plugin_dir_url(__FILE__));

final class Hushot {
    private static $instance = null;
    
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
    }
    
    private function load_dependencies() {
        require_once HUSHOT_PLUGIN_DIR . 'includes/class-hushot-database.php';
        require_once HUSHOT_PLUGIN_DIR . 'includes/class-hushot-activator.php';
        require_once HUSHOT_PLUGIN_DIR . 'includes/class-hushot-membership.php';
        require_once HUSHOT_PLUGIN_DIR . 'includes/class-hushot-pages.php';
        require_once HUSHOT_PLUGIN_DIR . 'includes/class-hushot-ajax.php';
        require_once HUSHOT_PLUGIN_DIR . 'includes/class-hushot-shortcodes.php';
        require_once HUSHOT_PLUGIN_DIR . 'includes/class-hushot-ai.php';
        require_once HUSHOT_PLUGIN_DIR . 'includes/class-hushot-flutterwave.php';
        
        // Ads Network Module (self-contained)
        require_once HUSHOT_PLUGIN_DIR . 'includes/ads/class-hushot-ads.php';
        
        if (is_admin()) {
            require_once HUSHOT_PLUGIN_DIR . 'admin/class-hushot-admin.php';
        }
    }
    
    private function init_hooks() {
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        add_action('init', array($this, 'register_cpt'), 0);
        add_action('init', array($this, 'init_classes'), 5);
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        
        add_filter('template_include', array($this, 'landing_page_template'), 999);
        add_filter('body_class', array($this, 'body_classes'));
        add_action('wp', array($this, 'track_view'));
        add_action('init', array($this, 'maybe_flush_rewrite'), 999);
        
        // URL Migration - Redirect old hushot-* URLs to clean URLs
        add_action('template_redirect', array('Hushot_Activator', 'handle_redirects'), 1);

        // URL Migration - Ensure migrations run after plugin updates (safe + idempotent)
        add_action('init', array('Hushot_Activator', 'migrate_urls'), 2);

        // Ensure required system pages exist and page ID mappings are not broken.
        // This repairs common causes of Seller Dashboard 404 and duplicated seller pages.
        add_action('init', array('Hushot_Activator', 'maybe_repair_pages'), 3);
        
        // PWA Support
        add_action('wp_head', array($this, 'pwa_meta_tags'), 1);
        add_action('wp_footer', array($this, 'pwa_service_worker'), 99);
        
        // Email Branding - Change sender from "WordPress" to "Hushot"
        add_filter('wp_mail_from', array($this, 'custom_mail_from'));
        add_filter('wp_mail_from_name', array($this, 'custom_mail_from_name'));
        
        // Multiple hooks to ensure we catch the page before Bricks
        add_action('template_redirect', array($this, 'force_system_pages'), 0);
        add_filter('bricks/active_templates', array($this, 'disable_bricks_for_hushot'), 999);
    }
    
    /**
     * Custom email sender address
     */
    public function custom_mail_from($email) {
        return 'support@hushot.net';
    }
    
    /**
     * Custom email sender name
     */
    public function custom_mail_from_name($name) {
        return 'Hushot';
    }
    
    /**
     * Output PWA meta tags
     */
    public function pwa_meta_tags() {
        ?>
        <!-- Hushot PWA Support -->
        <link rel="manifest" href="<?php echo HUSHOT_PLUGIN_URL; ?>manifest.json">
        <meta name="theme-color" content="#667eea">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="Hushot">
        <link rel="apple-touch-icon" href="<?php echo HUSHOT_PLUGIN_URL; ?>assets/images/icon.svg">
        <link rel="icon" type="image/svg+xml" href="<?php echo HUSHOT_PLUGIN_URL; ?>assets/images/icon.svg">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="application-name" content="Hushot">
        <?php
    }
    
    /**
     * Register service worker
     */
    public function pwa_service_worker() {
        ?>
        <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('<?php echo HUSHOT_PLUGIN_URL; ?>sw.js')
                    .then(function(registration) {
                        console.log('Hushot SW registered:', registration.scope);
                    })
                    .catch(function(error) {
                        console.log('Hushot SW registration failed:', error);
                    });
            });
        }
        </script>
        <?php
    }
    
    // Disable Bricks templates for Hushot system pages
    public function disable_bricks_for_hushot($templates) {
        $page_ids = get_option('hushot_page_ids', array());
        $current_id = get_queried_object_id();
        if ($current_id && in_array($current_id, array_values($page_ids))) {
            return array();
        }
        return $templates;
    }
    
    // Force system pages to render our content, bypassing Bricks completely
    public function force_system_pages() {
        if (is_admin()) return;
        
        $page_ids = get_option('hushot_page_ids', array());
        $current_id = get_queried_object_id();
        if (!$current_id) $current_id = get_the_ID();
        
        $shortcode_map = array(
            'home' => '[hushot_home]',
            'edit-page' => '[hushot_edit_page]',
            'dashboard' => '[hushot_dashboard]',
            'my-pages' => '[hushot_my_pages]',
            'create-page' => '[hushot_create_page]',
            'templates' => '[hushot_templates]',
            'login' => '[hushot_login]',
            'register' => '[hushot_register]',
            'my-account' => '[hushot_my_account]',
            'billing' => '[hushot_billing]',
            'analytics' => '[hushot_analytics]',
            'leads' => '[hushot_leads]',
            'pricing' => '[hushot_pricing]',
            'checkout' => '[hushot_checkout]',
            'forgot-password' => '[hushot_forgot_password]',
            'reset-password' => '[hushot_reset_password]',
            'ai-generator' => '[hushot_ai_generator]',
            'ai-image' => '[hushot_ai_image]',
            'ads-dashboard' => '[hushot_ads_dashboard]',
            'ads-promote' => '[hushot_ads_promote]',
            'support' => '[hushot_support]',
            'seller-dashboard' => '[hushot_seller_dashboard]',
            'seller-setup' => '[hushot_seller_setup]',
            'visual-builder' => '[hushot_visual_builder]',
            'install-app' => '[hushot_install_app]',
            'order-confirmation' => '[hushot_order_confirmation]',
        );
        
        $shortcode = '';
        
        // Method 1: Check by page ID
        foreach ($shortcode_map as $slug => $sc) {
            if (!empty($page_ids[$slug]) && (int)$page_ids[$slug] === (int)$current_id) {
                $shortcode = $sc;
                break;
            }
        }
        
        // Method 2: Fallback - match by request path (works with or without trailing slashes)
        if (!$shortcode) {
            $current_url = $_SERVER['REQUEST_URI'] ?? '';
            $path = trim((string)parse_url($current_url, PHP_URL_PATH), '/');

            // Support legacy/truncated slugs seen on some installs.
            // These should ALWAYS resolve to the correct shortcode even if the WP page is missing.
            $aliases = array(
                'seller-set' => 'seller-setup',
                'seller-dash' => 'seller-dashboard',
            );
            if (isset($aliases[$path])) {
                $path = $aliases[$path];
            }

            // Match last segment too (in case of nested paths)
            $parts = array_values(array_filter(explode('/', $path)));
            $last = end($parts);

            foreach ($shortcode_map as $slug => $sc) {
                $a = 'hushot-' . $slug;
                if ($last === $slug || $last === $a || $path === $slug || $path === $a) {
                    $shortcode = $sc;
                    break;
                }
            }
        }
        
        if (!$shortcode) return;
        
        // Output our page directly and exit - Bricks cannot intercept
        ?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title><?php wp_title('|', true, 'right'); bloginfo('name'); ?></title>
<?php wp_head(); ?>
<style>
/* FORCE HIDE ALL THEME ELEMENTS */
html, body { margin: 0 !important; padding: 0 !important; }
body { min-height: 100vh; }
.site-header, .site-footer, #masthead, #colophon, header.header, footer.footer,
.main-header, .main-footer, .page-header, .entry-header, .breadcrumb,
nav#site-navigation, .main-navigation, aside.sidebar, .elementor-location-header,
.elementor-location-footer, .brx-header, .brx-footer, #brx-header, #brx-footer,
[class*="bricks-"], .bricks-loading-dots, header[class*="header"]:not(.hs-sidebar-head):not(.lp-header),
[class*="page-title"], [class*="page-header"], [class*="hero-"],
.ast-header-html-inner, .wp-site-blocks > header, .wp-site-blocks > footer,
.site-branding, .hfeed > header, .hfeed > footer, .site > header, .site > footer,
#site-header, #site-footer, .theme-header, .theme-footer, .ast-container > header,
.main-content > header, .content-area > header, .entry-content > header:first-child,
header:not(.hs-head):not(.lp-header), .gb-container > header, #primary > header,
.ast-single-post > header, .single > header {
    display: none !important;
    visibility: hidden !important;
    height: 0 !important;
    overflow: hidden !important;
}
/* Reset any theme backgrounds for Hushot pages */
body.hushot-system-page {
    background: #0f172a !important;
}
</style>
</head>
<body <?php body_class('hushot-system-page'); ?>>
<?php echo do_shortcode($shortcode); ?>
<?php wp_footer(); ?>
</body>
</html>
        <?php
        exit; // CRITICAL: Exit immediately so Bricks cannot render anything
    }
    
    public function activate() {
        try {
            Hushot_Database::create_tables();
            $this->register_cpt();
            Hushot_Activator::create_pages();
            Hushot_Activator::create_templates();
            flush_rewrite_rules();
            update_option('hushot_activated', time());
            update_option('hushot_flush_rewrite', true);
        } catch (Exception $e) {
            error_log('Hushot activation error: ' . $e->getMessage());
        }
    }
    
    public function deactivate() {
        flush_rewrite_rules();
    }
    
    public function maybe_flush_rewrite() {
        if (get_option('hushot_flush_rewrite')) {
            flush_rewrite_rules();
            delete_option('hushot_flush_rewrite');
        }
    }
    
    public function register_cpt() {
        register_post_type('hushot_page', array(
            'labels' => array('name' => 'Landing Pages', 'singular_name' => 'Landing Page'),
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => false,
            'query_var' => true,
            'rewrite' => array('slug' => 'p', 'with_front' => false),
            'capability_type' => 'post',
            'has_archive' => false,
            'hierarchical' => false,
            'supports' => array('title', 'editor', 'author', 'thumbnail'),
            'show_in_rest' => true,
        ));
        
        register_post_type('hushot_template', array(
            'labels' => array('name' => 'Templates', 'singular_name' => 'Template'),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'supports' => array('title', 'editor', 'thumbnail'),
        ));
    }
    
    public function init_classes() {
        Hushot_Ads::init(); // Ads Network Module - must be before shortcodes
        Hushot_Shortcodes::init();
        Hushot_Ajax::init();
        Hushot_Pages::init();
        Hushot_Flutterwave::init();
        
        if (is_admin()) {
            Hushot_Admin::init();
        }
    }
    
    public function enqueue_scripts() {
        wp_enqueue_style('dashicons');
        wp_enqueue_style('hushot-public', HUSHOT_PLUGIN_URL . 'assets/css/hushot-public.css', array(), HUSHOT_VERSION);
        
        wp_enqueue_script('jquery');
        wp_enqueue_script('hushot-public', HUSHOT_PLUGIN_URL . 'assets/js/hushot-public.js', array('jquery'), HUSHOT_VERSION, true);
        
        // Get user currency info for frontend
        $currency_info = Hushot_Flutterwave::convert_usd_to_local(1);
        
        wp_localize_script('hushot-public', 'hushot', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('hushot_nonce'),
            'plugin_url' => HUSHOT_PLUGIN_URL,
            'create_url' => Hushot_Pages::get_page_url('create-page'),
            'ads_promote_url' => Hushot_Pages::get_page_url('ads-promote'),
            'currency' => $currency_info['currency'],
            'currency_symbol' => Hushot_Flutterwave::get_currency_symbol($currency_info['currency']),
            'exchange_rate' => $currency_info['exchange_rate'],
            'country' => $currency_info['country'],
        ));
    }
    
    public function admin_enqueue_scripts($hook) {
        if (strpos($hook, 'hushot') === false) return;
        
        wp_enqueue_style('hushot-admin', HUSHOT_PLUGIN_URL . 'assets/css/hushot-admin.css', array(), HUSHOT_VERSION);
        wp_enqueue_script('hushot-admin', HUSHOT_PLUGIN_URL . 'assets/js/hushot-admin.js', array('jquery'), HUSHOT_VERSION, true);
        
        wp_localize_script('hushot-admin', 'hushot_admin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('hushot_admin_nonce'),
        ));
    }
    
    public function landing_page_template($template) {
        if (is_singular('hushot_page')) {
            $custom = HUSHOT_PLUGIN_DIR . 'templates/landing-page.php';
            if (file_exists($custom)) return $custom;
        }
        return $template;
    }
    
    public function body_classes($classes) {
        if (is_singular('hushot_page')) {
            $classes[] = 'hushot-landing-page';
        }
        return $classes;
    }
    
    public function track_view() {
        if (!is_singular('hushot_page') || is_admin()) return;
        $id = get_the_ID();
        $views = (int) get_post_meta($id, '_hushot_views', true);
        update_post_meta($id, '_hushot_views', $views + 1);
    }
}

function hushot() {
    return Hushot::instance();
}

hushot();
