<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SheetBridge {

	private int $post_id;

	private array $config;

	private bool $initialized = false;

	private const string GOOGLE_OAUTH_TOKEN_URL = 'https://oauth2.googleapis.com/token';

	private const string GOOGLE_SHEETS_API_BASE = 'https://sheets.googleapis.com/v4/spreadsheets';

	private const string SCOPE = 'https://www.googleapis.com/auth/spreadsheets';

	public function __construct( int $post_id ) {
		$this->post_id = $post_id;

		$post = get_post( $post_id );
		if ( ! $post || SHEETBRIDGE_CPT_SLUG !== $post->post_type ) {
			error_log( 'SheetBridge: CPT with ID ' . $post_id . ' does not exist or is not a sheet_bridge post type.' );
			$this->config = array();
			return;
		}

		if ( 'publish' !== $post->post_status ) {
			error_log( 'SheetBridge: CPT with ID ' . $post_id . ' exists but is not published (status: ' . $post->post_status . ').' );
			$this->config = array();
			return;
		}

		$this->config      = $this->load_config();
		$this->initialized = true;
	}

	private function load_config(): array {
		$config = array(
			'spreadsheet_id'      => get_post_meta( $this->post_id, '_sb_spreadsheet_id', true ),
			'spreadsheet_url'     => get_post_meta( $this->post_id, '_sb_spreadsheet_url', true ),
			'sheet_name'          => get_post_meta( $this->post_id, '_sb_sheet_name', true ) ?: 'Sheet1',
			'api_key'             => get_post_meta( $this->post_id, '_sb_api_key', true ),
			'client_email'        => get_post_meta( $this->post_id, '_sb_client_email', true ),
			'private_key'         => get_post_meta( $this->post_id, '_sb_private_key', true ),
			'private_key_id'      => get_post_meta( $this->post_id, '_sb_private_key_id', true ),
			'project_id'          => get_post_meta( $this->post_id, '_sb_project_id', true ),
			'service_account_json' => get_post_meta( $this->post_id, '_sb_service_account_json', true ),
		);

		if ( ! $config['spreadsheet_id'] && $config['spreadsheet_url'] ) {
			$config['spreadsheet_id'] = $this->extract_id_from_url( $config['spreadsheet_url'] );
		}

		if ( $config['service_account_json'] && ( ! $config['client_email'] || ! $config['private_key'] || ! $config['private_key_id'] || ! $config['project_id'] ) ) {
			$parsed = json_decode( $config['service_account_json'], true );
			if ( is_array( $parsed ) ) {
				$config['client_email']   = $parsed['client_email'] ?? '';
				$config['private_key']    = $parsed['private_key'] ?? '';
				$config['private_key_id'] = $parsed['private_key_id'] ?? '';
				$config['project_id']     = $parsed['project_id'] ?? '';
			}
		}

		return $config;
	}

	private function extract_id_from_url( string $url ): ?string {
		if ( preg_match( '/\/d\/([a-zA-Z0-9_-]+)/', $url, $matches ) ) {
			return $matches[1];
		}
		return null;
	}

	public function push( $data ): array {
		if ( ! $this->initialized ) {
			return $this->result( false, 'SheetBridge instance not initialized. CPT does not exist or is not published.' );
		}

		$values = $this->normalize_data( $data );
		if ( empty( $values ) ) {
			return $this->result( false, 'Empty data provided' );
		}

		try {
			$headers = $this->get_headers();
			$row     = $this->map_values_to_columns( $values, $headers );

			$range  = $this->config['sheet_name'] . '!A:A';
			$body   = array(
				'values' => array( $row ),
			);

			$response = $this->api_request(
				$this->config['spreadsheet_id'] . '/values/' . rawurlencode( $range ) . ':append',
				array(
					'valueInputOption' => 'USER_ENTERED',
					'insertDataOption' => 'INSERT_ROWS',
				),
				$body
			);

			$updates = $response['updates'] ?? array();
			$result = $this->result(
				true,
				'Data appended successfully',
				array(
					'row'        => ( $updates['updatedRange'] ?? '' ),
					'rows_added' => $updates['updatedRows'] ?? 0,
					'response'   => $response,
				)
			);

			$this->update_sync_status( 'success' );

			return $result;
		} catch ( \Exception $e ) {
			$this->update_sync_status( 'error: ' . $e->getMessage() );
			return $this->result( false, $e->getMessage() );
		}
	}

	public function pull(): array {
		if ( ! $this->initialized ) {
			return $this->result( false, 'SheetBridge instance not initialized. CPT does not exist or is not published.' );
		}

		try {
			$range   = $this->config['sheet_name'] . '!A:ZZ';
			$response = $this->api_request(
				$this->config['spreadsheet_id'] . '/values/' . rawurlencode( $range ),
				array(
					'majorDimension' => 'ROWS',
				)
			);

			$rows = $response['values'] ?? array();

			if ( empty( $rows ) ) {
				return $this->result( true, 'Sheet is empty', array( 'data' => array() ) );
			}

			$headers = array_shift( $rows );
			$data    = array();

			foreach ( $rows as $row ) {
				$item = array();
				foreach ( $headers as $i => $header ) {
					$item[ $header ] = $row[ $i ] ?? '';
				}
				$data[] = $item;
			}

			$this->update_sync_status( 'success' );

			return $this->result( true, 'Data pulled successfully', array( 'data' => $data ) );
		} catch ( \Exception $e ) {
			$this->update_sync_status( 'error: ' . $e->getMessage() );
			return $this->result( false, $e->getMessage() );
		}
	}

	private function normalize_data( $data ): array {
		if ( is_string( $data ) ) {
			$decoded = json_decode( $data, true );
			if ( JSON_ERROR_NONE !== json_last_error() || ! is_array( $decoded ) ) {
				return array();
			}
			return $decoded;
		}

		if ( is_array( $data ) ) {
			return $data;
		}

		return array();
	}

	private function get_headers(): array {
		try {
			$range    = $this->config['sheet_name'] . '!1:1';
			$response = $this->api_request(
				$this->config['spreadsheet_id'] . '/values/' . rawurlencode( $range ),
				array(
					'majorDimension' => 'ROWS',
				)
			);

			return $response['values'][0] ?? array();
		} catch ( \Exception $e ) {
			return array();
		}
	}

	private function map_values_to_columns( array $values, array $headers ): array {
		if ( empty( $headers ) ) {
			return array_values( $values );
		}

		$row = array_fill( 0, count( $headers ), '' );

		foreach ( $values as $key => $value ) {
			$index = array_search( (string) $key, $headers, true );
			if ( false !== $index ) {
				$row[ $index ] = $value;
			}
		}

		return $row;
	}

	private function get_access_token(): string {
		if ( ! $this->config['client_email'] || ! $this->config['private_key'] ) {
			throw new \RuntimeException( 'Service account credentials not configured. Set client_email and private_key.' );
		}

		$now       = time();
		$payload   = array(
			'iss'   => $this->config['client_email'],
			'scope' => self::SCOPE,
			'aud'   => self::GOOGLE_OAUTH_TOKEN_URL,
			'exp'   => $now + 3600,
			'iat'   => $now,
		);

		$jwt = $this->create_signed_jwt( $payload, $this->config['private_key'] );

		$response = wp_remote_post(
			self::GOOGLE_OAUTH_TOKEN_URL,
			array(
				'body' => array(
					'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
					'assertion'  => $jwt,
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			throw new \RuntimeException( 'Failed to obtain access token: ' . $response->get_error_message() );
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! isset( $body['access_token'] ) ) {
			throw new \RuntimeException(
				'Failed to obtain access token: ' . ( $body['error_description'] ?? 'Unknown error' )
			);
		}

		return $body['access_token'];
	}

	private function create_signed_jwt( array $payload, string $private_key ): string {
		$header = array(
			'alg' => 'RS256',
			'typ' => 'JWT',
		);

		if ( $this->config['private_key_id'] ) {
			$header['kid'] = $this->config['private_key_id'];
		}

		$segments   = array();
		$segments[] = $this->base64url_encode( wp_json_encode( $header ) );
		$segments[] = $this->base64url_encode( wp_json_encode( $payload ) );

		$signing_input = implode( '.', $segments );

		$key = openssl_pkey_get_private( $private_key );
		if ( ! $key ) {
			throw new \RuntimeException( 'Invalid private key' );
		}

		$signature = '';
		if ( ! openssl_sign( $signing_input, $signature, $key, OPENSSL_ALGO_SHA256 ) ) {
			throw new \RuntimeException( 'Failed to sign JWT' );
		}

		$segments[] = $this->base64url_encode( $signature );

		return implode( '.', $segments );
	}

	private function base64url_encode( string $data ): string {
		return rtrim( strtr( base64_encode( $data ), '+/', '-_' ), '=' );
	}

	private function api_request( string $path, array $query_params = array(), ?array $body = null ): array {
		$spreadsheet_id = $this->config['spreadsheet_id'];
		if ( ! $spreadsheet_id ) {
			throw new \RuntimeException( 'Spreadsheet ID not configured' );
		}

		$url = self::GOOGLE_SHEETS_API_BASE . '/' . $path;

		$query_params['key'] = $this->config['api_key'];

		$url = add_query_arg( $query_params, $url );

		$args = array(
			'headers' => array(
				'Content-Type' => 'application/json',
			),
		);

		if ( $this->config['client_email'] && $this->config['private_key'] ) {
			$args['headers']['Authorization'] = 'Bearer ' . $this->get_access_token();
		}

		if ( null !== $body ) {
			$args['method'] = 'POST';
			$args['body']   = wp_json_encode( $body );
		} else {
			$args['method'] = 'GET';
		}

		$response = wp_remote_request( $url, $args );

		if ( is_wp_error( $response ) ) {
			throw new \RuntimeException( 'Google Sheets API request failed: ' . $response->get_error_message() );
		}

		$status_code = wp_remote_retrieve_response_code( $response );
		$response_body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( $status_code < 200 || $status_code >= 300 ) {
			$error_msg = $response_body['error']['message'] ?? 'HTTP ' . $status_code;
			throw new \RuntimeException( 'Google Sheets API error: ' . $error_msg );
		}

		return $response_body;
	}

	private function update_sync_status( string $status ): void {
		update_post_meta( $this->post_id, '_sb_last_sync_status', $status );
		update_post_meta( $this->post_id, '_sb_last_sync_time', current_time( 'mysql' ) );
	}

	private function result( bool $success, string $message, array $extra = array() ): array {
		return array_merge(
			array(
				'success' => $success,
				'message' => $message,
			),
			$extra
		);
	}
}
