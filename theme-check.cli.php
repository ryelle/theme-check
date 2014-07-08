<?php
/**
 * A CLI script for running a theme check
 */
include 'checkbase.php';
include 'main.php';

class ThemeCheckCLI extends WP_CLI_Command {

	function __construct() {
		parent::__construct();

		$this->fetcher = new \WP_CLI\Fetchers\Theme;
	}

	/**
	 * Show a list of the current themes
	 *
	 * ## OPTIONS
	 *
	 * [--errors=<errors>]
	 * : set true to return only themes with errors, false for only without errors. Default: false
	 *
	 * [--allowed=<allowed>]
	 * : (Multisite) set true to return only themes allowed on this site, false for only those not allowed,
	 * 'site' for only site-allowed, 'network' for only network-allowed.
	 *
	 * [--blog_id=<id>]
	 * : (Multisite) Blog ID, if different than current
	 *
	 * @subcommand list
	 */
	public function list_themes( $args = array(), $assoc_args = array() ) {
		$defaults = array( 'errors' => false, 'allowed' => null, 'blog_id' => 0 );
		$args = wp_parse_args( $assoc_args, $defaults );
		$args['errors'] = 'true' === $args['errors'];

		if ( ( 'true' == $args['allowed'] ) || ( 'false' == $args['allowed'] ) )
			$args['allowed'] = 'true' === $args['allowed'];

		$themes = wp_get_themes( $args );

		foreach ( $themes as $slug => $theme ) {
			WP_CLI::line( $slug . ': ' . $theme->get('Name') );
		}
	}

	/**
	 * Check a theme
	 *
	 * <theme>
	 * : The theme slug to check
	 */
	public function check( $args = array(), $assoc_args = array() ){
		global $checkcount, $themechecks;

		$theme = $this->fetcher->get_check( $args[0] );
		$files = $theme->get_files( null, -1 );
		$css = $php = $other = array();

		foreach( $files as $key => $filename ) {
			if ( substr( $filename, -4 ) == '.php' ) {
				$php[ $filename ] = php_strip_whitespace( $filename );
			} else if ( substr( $filename, -4 ) == '.css' ) {
				$css[ $filename ] = file_get_contents( $filename );
			} else {
				$other[ $filename ] = ( ! is_dir( $filename ) ) ? file_get_contents( $filename ) : '';
			}
		}

		$success = themecheck_run_checks($php, $css, $other);

		$errors = array();
		foreach ( $themechecks as $check ) {
			if ( $check instanceof themecheck ) {
				$error = $check->getError();
				if ( ! empty( $error ) ) {
					$errors = array_merge( $error, $errors );
				}
			}
		}
		$errors = array_unique( $errors );
		$errors = array_map( 'strip_tags', $errors );
		rsort( $errors );

		foreach ( $errors as $error ) {
			list( $type, $message ) = explode( ':', $error, 2 );
			if ( 'REQUIRED' == trim( $type ) ) {
				WP_CLI::line( WP_CLI::colorize( '%rRequired:%n '.trim( $message ) ) );
			} elseif ( 'RECOMMENDED' == trim( $type ) ) {
				WP_CLI::line( WP_CLI::colorize( '%cRecommended:%n '.trim( $message ) ) );
			} elseif ( 'WARNING' == trim( $type ) ) {
				WP_CLI::line( WP_CLI::colorize( '%yWarning:%n '.trim( $message ) ) );
			} else {
				WP_CLI::line( $error );
			}
		}

		if ( empty( $errors ) ){
			WP_CLI::success( "No errors!" );
		}

	}

}

// Here we define the command name we want to use.
WP_CLI::add_command( 'trt', 'ThemeCheckCLI' );
