<?php

namespace ImageOptimization\Modules\Optimization\Components;

use ImageOptimization\Classes\Logger;
use Throwable;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
class Avif_Compatibility {
	public function is_avif_supported(): bool {
		return in_array( 'image/avif', get_allowed_mime_types(), true );
	}

	public function add_to_supported_types( $mime_types ) {
		$mime_types['avif'] = 'image/avif';
		return $mime_types;
	}

	public function add_to_supported_mime_types( $mime_types ) {
		$mime_types['image/avif'] = 'avif';
		return $mime_types;
	}

	public function mark_as_displayable( $result, $path ) {
		if ( str_ends_with( $path, '.avif' ) ) {
			return true;
		}

		return $result;
	}

	public function fix_avif_images( $metadata, $attachment_id ) {
		if ( empty( $metadata ) ) {
			return $metadata;
		}

		$attachment = get_post( $attachment_id );

		if ( ! $attachment || is_wp_error( $attachment ) || 'image/avif' !== $attachment->post_mime_type ) {
			return $metadata;
		}

		if ( ( ! empty( $metadata['width'] ) && ( 0 !== $metadata['width'] ) ) && ( ! empty( $metadata['height'] ) && 0 !== $metadata['height'] ) ) {
			return $metadata;
		}

		$file = get_attached_file( $attachment_id );

		if ( ! $file ) {
			return $metadata;
		}

		if ( empty( $metadata['width'] ) ) {
			$metadata['width'] = 0;
		}

		if ( empty( $metadata['height'] ) ) {
			$metadata['height'] = 0;
		}

		if ( empty( $metadata['file'] ) ) {
			$metadata['file'] = _wp_relative_upload_path( $file );
		}

		if ( empty( $metadata['sizes'] ) ) {
			$metadata['sizes'] = [];
		}

		if ( class_exists( 'Imagick' ) ) {
			try {
				$im = new \Imagick( $file );
				$image_geometry = $im->getImageGeometry();
				$im->clear();

				$metadata['width'] = $image_geometry['width'];
				$metadata['height'] = $image_geometry['height'];
			} catch ( Throwable $t ) {
				Logger::log(
					Logger::LEVEL_ERROR,
					'AVIF image dimensions calculation error: ' . $t->getMessage()
				);
			}
		}

		return $metadata;
	}

	public function __construct() {
		if ( $this->is_avif_supported() ) {
			return;
		}

		add_filter( 'upload_mimes', [ $this, 'add_to_supported_types' ] );
		add_filter( 'mime_types', [ $this, 'add_to_supported_types' ] );
		add_filter( 'getimagesize_mimes_to_exts', [ $this, 'add_to_supported_mime_types' ] );
		add_filter( 'file_is_displayable_image', [ $this, 'mark_as_displayable' ], 10, 2 );

		add_filter( 'wp_generate_attachment_metadata', [ $this, 'fix_avif_images' ], 1, 3 );
	}
}
