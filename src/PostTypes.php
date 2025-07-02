<?php
/**
 * Post Types Utility Class
 *
 * Provides utility functions for working with multiple WordPress post types,
 * including bulk operations, search functionality, and options formatting.
 *
 * @package ArrayPress\PostTypeUtils
 * @since   1.0.0
 * @author  ArrayPress
 * @license GPL-2.0-or-later
 */

declare( strict_types=1 );

namespace ArrayPress\PostTypeUtils;

use WP_Post_Type;

/**
 * PostTypes Class
 *
 * Operations for working with multiple WordPress post types.
 */
class PostTypes {

	// ========================================
	// Core Retrieval
	// ========================================

	/**
	 * Check if multiple post types exist.
	 *
	 * @param array $post_types Array of post type names.
	 *
	 * @return array Array of existing post type names.
	 */
	public static function exists( array $post_types ): array {
		if ( empty( $post_types ) ) {
			return [];
		}

		return array_filter( $post_types, function ( $post_type ) {
			return post_type_exists( $post_type );
		} );
	}

	/**
	 * Get multiple post type objects.
	 *
	 * @param array $post_types          Array of post type names.
	 * @param bool  $include_nonexistent Whether to include non-existent types. Default false.
	 *
	 * @return array Array of post type objects with names as keys.
	 */
	public static function get( array $post_types, bool $include_nonexistent = false ): array {
		if ( empty( $post_types ) ) {
			return [];
		}

		$results = [];
		foreach ( $post_types as $post_type ) {
			$post_type_obj = get_post_type_object( $post_type );

			if ( $post_type_obj instanceof WP_Post_Type ) {
				$results[ $post_type ] = $post_type_obj;
			} elseif ( $include_nonexistent ) {
				$results[ $post_type ] = null;
			}
		}

		return $results;
	}

	/**
	 * Get all registered post types.
	 *
	 * @param array  $args             Optional. Arguments to filter post types.
	 * @param string $output           Optional. Output type ('names' or 'objects'). Default 'names'.
	 * @param bool   $exclude_defaults Optional. Whether to exclude default types. Default false.
	 *
	 * @return array Array of post type names or objects.
	 */
	public static function get_all( array $args = [], string $output = 'names', bool $exclude_defaults = false ): array {
		$defaults = [ 'public' => true, 'show_ui' => true ];
		$args     = wp_parse_args( $args, $defaults );

		$post_types = get_post_types( $args, $output );

		if ( $exclude_defaults ) {
			unset( $post_types['post'], $post_types['page'], $post_types['attachment'] );
		}

		return $post_types;
	}

	/**
	 * Get public post types.
	 *
	 * @param string $output Optional. Output type ('names' or 'objects'). Default 'names'.
	 *
	 * @return array Array of public post types.
	 */
	public static function get_public( string $output = 'names' ): array {
		return self::get_all( [ 'public' => true ], $output );
	}

	/**
	 * Get hierarchical post types.
	 *
	 * @param string $output Optional. Output type ('names' or 'objects'). Default 'names'.
	 *
	 * @return array Array of hierarchical post types.
	 */
	public static function get_hierarchical( string $output = 'names' ): array {
		return self::get_all( [ 'hierarchical' => true ], $output );
	}

	/**
	 * Get custom post types only.
	 *
	 * @param string $output Optional. Output type ('names' or 'objects'). Default 'names'.
	 *
	 * @return array Array of custom post types.
	 */
	public static function get_custom( string $output = 'names' ): array {
		return self::get_all( [ '_builtin' => false ], $output );
	}

	/**
	 * Get post types by taxonomy.
	 *
	 * @param string $taxonomy Taxonomy name.
	 *
	 * @return array Array of post type names.
	 */
	public static function get_by_taxonomy( string $taxonomy ): array {
		if ( empty( $taxonomy ) || ! taxonomy_exists( $taxonomy ) ) {
			return [];
		}

		$taxonomy_obj = get_taxonomy( $taxonomy );

		return $taxonomy_obj ? $taxonomy_obj->object_type : [];
	}

	// ========================================
	// Search & Options
	// ========================================

	/**
	 * Search post types by name or label.
	 *
	 * @param string $search Search term.
	 * @param array  $args   Optional arguments.
	 *
	 * @return array Array of post type objects.
	 */
	public static function search( string $search, array $args = [] ): array {
		if ( empty( $search ) ) {
			return [];
		}

		$defaults = [
			'public'   => true,
			'show_ui'  => true,
			'_builtin' => false,
		];

		$args       = wp_parse_args( $args, $defaults );
		$post_types = get_post_types( $args, 'objects' );

		return array_filter( $post_types, function ( $post_type ) use ( $search ) {
			return str_contains( strtolower( $post_type->label ), strtolower( $search ) ) ||
			       str_contains( strtolower( $post_type->name ), strtolower( $search ) );
		} );
	}

	/**
	 * Search post types and return in value/label format.
	 *
	 * @param string $search Search term.
	 * @param array  $args   Optional arguments.
	 *
	 * @return array Array of ['value' => name, 'label' => label] items.
	 */
	public static function search_options( string $search, array $args = [] ): array {
		$post_types = self::search( $search, $args );

		$options = [];
		foreach ( $post_types as $post_type ) {
			$options[] = [
				'value' => $post_type->name,
				'label' => $post_type->label,
			];
		}

		return $options;
	}

	/**
	 * Get post type options for form fields.
	 *
	 * @param array  $args             Optional. Arguments to filter post types.
	 * @param bool   $exclude_defaults Optional. Whether to exclude default types. Default false.
	 * @param string $label_field      Optional. Label field ('name' or 'singular_name'). Default 'singular_name'.
	 *
	 * @return array Array of ['id' => 'label'] options.
	 */
	public static function get_options( array $args = [], bool $exclude_defaults = false, string $label_field = 'singular_name' ): array {
		$defaults = [ 'public' => true, 'show_ui' => true ];
		$args     = wp_parse_args( $args, $defaults );

		$post_types = get_post_types( $args, 'objects' );

		if ( empty( $post_types ) || ! is_array( $post_types ) ) {
			return [];
		}

		if ( $exclude_defaults ) {
			unset( $post_types['post'], $post_types['page'], $post_types['attachment'] );
		}

		$options = [];
		foreach ( $post_types as $post_type => $post_type_obj ) {
			if ( ! isset( $post_type, $post_type_obj->labels ) ) {
				continue;
			}

			$label = $label_field === 'name'
				? $post_type_obj->labels->name ?? $post_type_obj->labels->singular_name ?? $post_type
				: $post_type_obj->labels->singular_name ?? $post_type_obj->labels->name ?? $post_type;

			$options[ $post_type ] = $label;
		}

		// Sort alphabetically by label
		asort( $options );

		return $options;
	}

	// ========================================
	// Feature Analysis
	// ========================================

	/**
	 * Get post types that support a feature.
	 *
	 * @param string $feature Feature to check for.
	 *
	 * @return array Array of post type names that support the feature.
	 */
	public static function get_by_feature( string $feature ): array {
		return get_post_types_by_support( $feature );
	}

	/**
	 * Check which post types support a feature.
	 *
	 * @param array  $post_types Array of post type names.
	 * @param string $feature    Feature to check.
	 *
	 * @return array Array of post type names that support the feature.
	 */
	public static function supports_feature( array $post_types, string $feature ): array {
		if ( empty( $post_types ) || empty( $feature ) ) {
			return [];
		}

		return array_filter( $post_types, function ( $post_type ) use ( $feature ) {
			return post_type_supports( $post_type, $feature );
		} );
	}

	/**
	 * Get post types that support Gutenberg.
	 *
	 * @return array Array of post type names that support Gutenberg.
	 */
	public static function get_gutenberg_supported(): array {
		$supported_post_types = [];
		foreach ( get_post_types_by_support( 'editor' ) as $post_type ) {
			if ( use_block_editor_for_post_type( $post_type ) ) {
				$supported_post_types[] = $post_type;
			}
		}

		return $supported_post_types;
	}

	// ========================================
	// Labels & Capabilities
	// ========================================

	/**
	 * Get labels for multiple post types.
	 *
	 * @param array  $post_types Array of post type names.
	 * @param string $label      Optional. Specific label to retrieve.
	 *
	 * @return array Array of labels with post type names as keys.
	 */
	public static function get_labels( array $post_types, string $label = '' ): array {
		if ( empty( $post_types ) ) {
			return [];
		}

		$results = [];
		foreach ( $post_types as $post_type ) {
			$post_type_obj = get_post_type_object( $post_type );
			if ( ! $post_type_obj ) {
				continue;
			}

			if ( $label ) {
				$results[ $post_type ] = $post_type_obj->labels->$label ?? null;
			} else {
				$results[ $post_type ] = $post_type_obj->labels;
			}
		}

		return $results;
	}

	/**
	 * Get capabilities for multiple post types.
	 *
	 * @param array $post_types Array of post type names.
	 *
	 * @return array Array of capabilities with post type names as keys.
	 */
	public static function get_capabilities( array $post_types ): array {
		if ( empty( $post_types ) ) {
			return [];
		}

		$results = [];
		foreach ( $post_types as $post_type ) {
			$post_type_obj = get_post_type_object( $post_type );
			if ( $post_type_obj ) {
				$results[ $post_type ] = $post_type_obj->cap;
			}
		}

		return $results;
	}

}