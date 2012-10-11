<?php

// Prevent loading this file directly - Busted!
if ( !defined('ABSPATH') )
	die('-1');

if ( !class_exists( 'WC_GitHub_Updater' ) ):

	require_once( 'class-wpgithubupdater.php' );

	class WC_GitHub_Updater extends WPGitHubUpdater {

		/**
		 * Class Constructor
		 *
		 * @since 1.0
		 * @param array $config configuration
		 * @return void
		 */
		public function __construct( $config = array() ) {

			global $wp_version;


			$this->config = array(
				'slug' => 'woocommerce/woocommerce.php',
				'proper_folder_name' => 'woocommerce',
				'api_url' => 'https://api.github.com/repos/woothemes/woocommerce',
				'raw_url' => 'https://raw.github.com/woothemes/woocommerce/master',
				'github_url' => 'https://github.com/woothemes/woocommerce',
				'zip_url' => 'https://github.com/woothemes/woocommerce/zipball/master',
				'readme' => 'readme.txt',
				'sslverify' => true,
				'requires' => $wp_version,
				'tested' => $wp_version
			);


			$this->config['version'] = get_option( 'wc_beta_tester_installed_commit', false );
			$this->config['new_version'] = $this->get_latest_commit()->sha;

			$this->set_defaults();

			if ( ( defined('WP_DEBUG') && WP_DEBUG ) || ( defined('WP_GITHUB_FORCE_UPDATE') || WP_GITHUB_FORCE_UPDATE ) )
				$this->delete_transients();

			add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'api_check' ) );

			// Hook into the plugin details screen
			add_filter( 'plugins_api', array( $this, 'get_plugin_info' ), 10, 3 );
			add_filter( 'upgrader_post_install', array( $this, 'upgrader_post_install' ), 10, 3 );

			// set timeout
			add_filter( 'http_request_timeout', array( $this, 'http_request_timeout' ) );
			
			// set sslverify for zip download
			add_filter( 'http_request_args', array( $this, 'http_request_sslverify' ), 10, 2 );
		}


		public function get_latest_commit() {

			if ( ! isset( $last_commit ) || ! $last_commit || '' == $last_commit ) {
				$commits = wp_remote_get(
					trailingslashit( $this->config['api_url'] ) . 'commits',
					array(
						'sslverify' => $this->config['sslverify'],
					)
				);

				if ( is_wp_error( $commits ) )
					return false;

				$commits = json_decode( $commits['body'] );
				$last_commit = $commits[0];

				// refresh every hour
				set_site_transient( $this->config['slug'].'_last_commit', $last_commit, 60*60*1);
			}

			return $last_commit;
		}

		public function api_check( $transient ) {

			// Check if the transient contains the 'checked' information
			// If not, just return its value without hacking it
			if ( empty( $transient->checked ) )
				return $transient;

			// if sha's match, let's move on
			if( $this->config['new_version'] == $this->config['version'] )
				return $transient;

			// check the version and decide if it's new
			$update = version_compare( $this->get_commit_time( $this->config['new_version'] ), $this->get_commit_time( $this->config['version'] ) );

			if ( 1 === $update ) {
				$response = new stdClass;
				$response->new_version = $this->config['new_version'];
				$response->slug = $this->config['proper_folder_name'];
				$response->url = $this->config['github_url'];
				$response->package = $this->config['zip_url'];

				// If response is false, don't alter the transient
				if ( false !== $response )
					$transient->response[ $this->config['slug'] ] = $response;
			}

			return $transient;
		}

		public function get_commit_time( $sha ) {

			if( !$sha )
				return 0;
			

			$commit = wp_remote_get(
				trailingslashit( $this->config['api_url'] ) . 'git/commits/' . $sha,
				array(
					'sslverify' => $this->config['sslverify'],
				)
			);

			$commit = json_decode( $commit['body'] );

			return ( isset( $commit->author->date ) ) ? strtotime( $commit->author->date ) : strtotime( $commit->committer->date );

		}

	}

endif; // endif class exists