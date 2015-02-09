<?php
/**
 * Plugin Name: Yahoo! Finance Charts
 */
class Y_Finance {
    public function __construct() {
        add_action('admin_enqueue_scripts', array($this, 'wp_editor_assets'));
        add_action('admin_head', array($this, 'wp_editor_addon'));
        add_action('wp_ajax_yfinance_get_form', array($this, 'ajax_get_form'));

        add_shortcode('yfinance', array($this, 'shortcode'));
        add_filter('widget_text', 'do_shortcode');
    }

    /**
     * Checks if post type is post or page.
     */
    public function is_allowed_post() {
        global $typenow;
        return in_array($typenow, array('post', 'page'));
    }

    /**
     * Enqueue relevant scripts and stylesheets.
     */
    public function wp_editor_assets() {
        if ($this->is_allowed_post()) {
            wp_enqueue_style('yfinance', plugin_dir_url(__FILE__) . 'css/yfinance.css');
        }
    }

    /**
     * TinyMCE WP editor button/plugin registration.
     */
    public function wp_editor_addon() {
        if ($this->is_allowed_post()) {
           add_filter('mce_external_plugins', array($this, 'wp_editor_plugins'));
           add_filter('mce_buttons', array($this, 'wp_editor_buttons'));
        }
    }

    /**
     * Filter for mce_external_plugins.
     */
    public function wp_editor_plugins($plugin_array) {
        $plugin_array['yfinance'] = plugin_dir_url(__FILE__) . 'js/yfinance.min.js';
        return $plugin_array;
    }

    /**
     * Filter for mce_buttons.
     */
    public function wp_editor_buttons($buttons) {
        array_push($buttons, 'yfinance');
        return $buttons;
    }


    /**
     * Render shortcode for yfinance
     */
    public function shortcode($atts) {
        $parameters = array(
            's' => $atts['ticker'],
            't' => $atts['time_int'] . $atts['time_unit'],
            'z' => $atts['size'],
            'q' => $atts['type'],
            'l' => $atts['log_scaling']
        );

        // Moving average indicator
        if (!empty($atts['mai_1']) || !empty($atts['mai_2'])) {
            $parameters['m'] = '';
            for ($i = 1; $i < 3; $i++) {
                $parameters['m'] += (!empty($atts['mai_' . $i])) ? $atts['mai_' . $i] . ',' : '';
            }
            $parameters['m'] = rtrim($parameters['m'], ',');
        }

        $query_string = '';
        foreach ($parameters as $k => $v) {
            $query_string .= $k . '=' . $v . '&';
        }
        $query_string = rtrim($query_string, '&');
        
        return '<img class="yfinance-chart" src="http://chart.finance.yahoo.com/z?' . $query_string . '">';
    }

    /**
     * Write out form HTML.
     */
    public function ajax_get_form() {
        include plugin_dir_path(__FILE__) . 'form.html';
        wp_die();
    }
}

new Y_Finance;
?>