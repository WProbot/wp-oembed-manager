<?php
/**
 * Plugin environment handling.
 *
 * @package System
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */

namespace Oemm\System;

use Exception;

/**
 * The class responsible to manage and detect plugin environment.
 *
 * @package System
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */
class Environment {

	/**
	 * Initializes the class and set its properties.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
	}

	/**
	 * Defines all needed globals.
	 *
	 * @since 1.0.0
	 */
	public static function init() {
		$plugin_path         = str_replace( OEMM_SLUG . '/includes/system/', OEMM_SLUG . '/', plugin_dir_path( __FILE__ ) );
		$plugin_url          = str_replace( OEMM_SLUG . '/includes/system/', OEMM_SLUG . '/', plugin_dir_url( __FILE__ ) );
		$plugin_relative_url = str_replace( get_site_url() . '/', '', $plugin_url );
		define( 'OEMM_PLUGIN_DIR', $plugin_path );
		define( 'OEMM_PLUGIN_URL', $plugin_url );
		define( 'OEMM_PLUGIN_RELATIVE_URL', $plugin_relative_url );
		define( 'OEMM_ADMIN_DIR', OEMM_PLUGIN_DIR . 'admin/' );
		define( 'OEMM_ADMIN_URL', OEMM_PLUGIN_URL . 'admin/' );
		define( 'OEMM_PUBLIC_DIR', OEMM_PLUGIN_DIR . 'public/' );
		define( 'OEMM_PUBLIC_URL', OEMM_PLUGIN_URL . 'public/' );
		define( 'OEMM_INCLUDES_DIR', OEMM_PLUGIN_DIR . 'includes/' );
		define( 'OEMM_VENDOR_DIR', OEMM_PLUGIN_DIR . 'includes/libraries/' );
		define( 'OEMM_LANGUAGES_DIR', OEMM_PLUGIN_DIR . 'languages/' );
		define( 'OEMM_ADMIN_RELATIVE_URL', self::admin_relative_url() );
		define( 'OEMM_AJAX_RELATIVE_URL', self::ajax_relative_url() );
		define( 'OEMM_PLUGIN_SIGNATURE', OEMM_PRODUCT_NAME . ' v' . OEMM_VERSION );
		define( 'OEMM_PLUGIN_AGENT', OEMM_PRODUCT_NAME . ' (' . self::wordpress_version_id() . '; ' . self::plugin_version_id() . '; +' . OEMM_PRODUCT_URL . ')' );
		define( 'OEMM_ASSETS_ID', OEMM_PRODUCT_ABBREVIATION . '-assets' );
	}

	/**
	 * Get the current execution mode.
	 *
	 * @return  integer The current execution mode.
	 * @since 1.0.0
	 */
	public static function exec_mode() {
		$id = 0;
		$req_uri = filter_input( INPUT_SERVER, 'REQUEST_URI' );
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			$id = 1;
		} elseif (wp_doing_cron()){
			$id = 2;
		} elseif (wp_doing_ajax()){
			$id = 3;
		} elseif ( defined('XMLRPC_REQUEST') && XMLRPC_REQUEST ){
			$id = 4;
		} elseif ( defined( 'REST_REQUEST ' ) && REST_REQUEST ) {
			$id = 5;
		} elseif ( $req_uri ? 0 === strpos(strtolower($req_uri), '/wp-json/') : false ) {
			$id = 5;
		} elseif ( $req_uri ? 0 === strpos(strtolower($req_uri), '/feed/') : false ) {
			$id = 6;
		} elseif ( is_admin() ) {
			$id = 7;
		} else {
			$id = 8;
		}
		return $id;
	}

	/**
	 * Get the major version number.
	 *
	 * @param  string $version Optional. The full version string.
	 * @return string The major version number.
	 * @since  1.0.0
	 */
	public static function major_version( $version = OEMM_VERSION ) {
		try {
			$result = substr( $version, 0, strpos( $version, '.' ) );
		} catch ( Exception $ex ) {
			$result = 'x';
		}
		return $result;
	}

	/**
	 * Get the major version number.
	 *
	 * @param  string $version Optional. The full version string.
	 * @return string The major version number.
	 * @since  1.0.0
	 */
	public static function minor_version( $version = OEMM_VERSION ) {
		try {
			$result = substr( $version, strpos( $version, '.' ) + 1, 1000 );
			$result = substr( $result, 0, strpos( $result, '.' ) );
		} catch ( Exception $ex ) {
			$result = 'x';
		}
		return $result;
	}

	/**
	 * Get the major version number.
	 *
	 * @param  string $version Optional. The full version string.
	 * @return string The major version number.
	 * @since  1.0.0
	 */
	public static function patch_version( $version = OEMM_VERSION ) {
		try {
			$result = substr( $version, strpos( $version, '.' ) + 1, 1000 );
			$result = substr( $result, strpos( $result, '.' ) + 1, 1000 );
			if ( strpos( $result, '-' ) > 0 ) {
				$result = substr( $result, 0, strpos( $result, '-' ) );
			}
		} catch ( Exception $ex ) {
			$result = 'x';
		}
		return $result;
	}

	/**
	 * Verification of WP MU.
	 *
	 * @return boolean     True if MU, false otherwise.
	 * @since  1.0.0
	 */
	public static function is_wordpress_multisite() {
		return is_multisite();
	}

	/**
	 * Get the WordPress version ID.
	 *
	 * @return string  The WordPress version ID.
	 * @since  1.0.0
	 */
	public static function wordpress_version_id() {
		global $wp_version;
		return 'WordPress/' . $wp_version;
	}

	/**
	 * Get the WordPress version Text.
	 *
	 * @return string  The WordPress version in human-readable text.
	 * @since  1.0.0
	 */
	public static function wordpress_version_text() {
		global $wp_version;
		$s = '';
		if ( is_multisite() ) {
			$s = 'MU ';
		}
		return 'WordPress ' . $s . $wp_version;
	}

	/**
	 * Get the WordPress debug status.
	 *
	 * @return string  The WordPress debug status in human-readable text.
	 * @since  1.0.0
	 */
	public static function wordpress_debug_text() {
		$debug = false;
		$opt   = [];
		$s     = '';
		if ( defined( 'WP_DEBUG' ) ) {
			if ( WP_DEBUG ) {
				$debug = true;
				if ( defined( 'WP_DEBUG_LOG' ) ) {
					if ( WP_DEBUG_LOG ) {
						$opt[] = esc_html__( 'log', 'oembed-manager' );
					}
				}
				if ( defined( 'WP_DEBUG_DISPLAY' ) ) {
					if ( WP_DEBUG_DISPLAY ) {
						$opt[] = esc_html__( 'display', 'oembed-manager' );
					}
				}
				$s = implode( ', ', $opt );
			}
		}
		return ( $debug ? esc_html__( 'Debug enabled', 'oembed-manager' ) . ( '' !== $s ? ' (' . $s . ')' : '' ) : esc_html__( 'Debug disabled', 'oembed-manager' ) );
	}

	/**
	 * Get the plugin version ID.
	 *
	 * @return string  The plugin version ID.
	 * @since  1.0.0
	 */
	public static function plugin_version_id() {
		return OEMM_PRODUCT_SHORTNAME . '/' . OEMM_VERSION;
	}

	/**
	 * Get the plugin version text.
	 *
	 * @return string  The plugin version in human-readable text.
	 * @since  1.0.0
	 */
	public static function plugin_version_text() {
		$s = OEMM_PRODUCT_NAME . ' ' . OEMM_VERSION;
		if ( defined( 'OEMM_CODENAME' ) ) {
			if ( OEMM_CODENAME !== '"-"' ) {
				$s .= ' ' . OEMM_CODENAME;
			}
		}
		return $s;
	}

	/**
	 * Is the plugin a development preview?
	 *
	 * @return boolean True if it's a development preview, false otherwise.
	 * @since  1.0.0
	 */
	public static function is_plugin_in_dev_mode() {
		return ( strpos( OEMM_VERSION, 'dev' ) > 0 );
	}

	/**
	 * Is the plugin a release candidate?
	 *
	 * @return boolean True if it's a release candidate, false otherwise.
	 * @since  1.0.0
	 */
	public static function is_plugin_in_rc_mode() {
		return ( strpos( OEMM_VERSION, 'rc' ) > 0 );
	}

	/**
	 * Is the plugin production ready?
	 *
	 * @return boolean True if it's production ready, false otherwise.
	 * @since  1.0.0
	 */
	public static function is_plugin_in_production_mode() {
		return ( ! self::is_plugin_in_dev_mode() && ! self::is_plugin_in_rc_mode() );
	}

	/**
	 * Get the PHP version.
	 *
	 * @return string  The PHP version.
	 * @since  1.0.0
	 */
	public static function php_version() {
		$s = phpversion();
		if ( strpos( $s, '-' ) > 0 ) {
			$s = substr( $s, 0, strpos( $s, '-' ) );
		}
		return $s;
	}

	/**
	 * Get the PHP full version.
	 *
	 * @return string  The PHP full version.
	 * @since  1.0.0
	 */
	public static function php_full_version() {
		return phpversion();
	}

	/**
	 * Get the PHP version text.
	 *
	 * @return string  The PHP version in human-readable text.
	 * @since  1.0.0
	 */
	public static function php_version_text() {
		return 'PHP ' . self::php_version();
	}

	/**
	 * Get the PHP full version text.
	 *
	 * @return string  The PHP full version in human-readable text.
	 * @since  1.0.0
	 */
	public static function php_full_version_text() {
		return 'PHP ' . self::php_full_version();
	}

	/**
	 * Get the MYSQL version.
	 *
	 * @return string  The MYSQL full version.
	 * @since  1.0.0
	 */
	public static function mysql_version() {
		global $wpdb;
		return $wpdb->db_version();
	}

	/**
	 * Get the MYSQL version text.
	 *
	 * @return string  The MYSQL version in human-readable text.
	 * @since  1.0.0
	 */
	public static function mysql_version_text() {
		return 'MySQL ' . self::mysql_version();
	}

	/**
	 * Get the database name and host.
	 *
	 * @return string  The database name and host in human-readable text.
	 * @since  1.0.0
	 */
	public static function mysql_name_text() {
		return DB_NAME . '@' . DB_HOST;
	}

	/**
	 * Get the database charset.
	 *
	 * @return string  The charset text.
	 * @since  1.0.0
	 */
	public static function mysql_charset_text() {
		return DB_CHARSET;
	}

	/**
	 * Get the database user.
	 *
	 * @return string  The user text.
	 * @since  1.0.0
	 */
	public static function mysql_user_text() {
		return DB_USER;
	}

	/**
	 * The detection of this url allows the plugin to make ajax calls when
	 * the site has not a standard /wp-admin/ path.
	 *
	 * @return string    The relative url for ajax calls.
	 * @since  1.0.0
	 */
	private static function ajax_relative_url() {
		$url = preg_replace( '/(http[s]?:\/\/.*\/)/iU', '', get_admin_url(), 1 );
		return ( substr( $url, 0 ) === '/' ? '' : '/' ) . $url . ( substr( $url, -1 ) === '/' ? '' : '/' ) . 'admin-ajax.php';
	}

	/**
	 * The detection of this url allows the plugin to be called from
	 * WP dashboard when the site has not a standard /wp-admin/ path.
	 *
	 * @return string    The relative url for WP admin.
	 * @since  1.0.0
	 */
	private static function admin_relative_url() {
		$url = preg_replace( '/(http[s]?:\/\/.*\/)/iU', '', get_admin_url(), 1 );
		return ( substr( $url, 0 ) === '/' ? '' : '/' ) . $url . ( substr( $url, -1 ) === '/' ? '' : '/' ) . 'admin.php';
	}
}

Environment::init();