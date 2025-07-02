<?php
/**
 * Post Type Utility Class
 *
 * Provides utility functions for working with individual WordPress post types,
 * including information retrieval, capability checking, and feature support.
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
 * PostType Class
 *
 * Core operations for working with individual WordPress post types.
 */
class PostType {

	// ========================================
	// Core Retrieval
	// ========================================

	/**
	 * Check if a post type exists.
	 *
	 * @param string $post_type Post type name.
	 *
	 * @return bool True if exists, false otherwise.
	 */
	public static function exists( string $post_type ): bool {
		return post_type_exists( $post_type );
	}

	/**
	 * Get post type object.
	 *
	 * @param string $post_type Post type name.
	 *
	 * @return WP_Post_Type|null Post type object or null if not found.
	 */
	public static function get( string $post_type ): ?WP_Post_Type {
		if ( ! self::exists( $post_type ) ) {
			return null;
		}

		return get_post_type_object( $post_type );
	}

	// ========================================
	// Labels & Information
	// ========================================

	/**
	 * Get post type labels.
	 *
	 * @param string $post_type Post type name.
	 *
	 * @return object|null Labels object or null if not found.
	 */
	public static function get_labels( string $post_type ): ?object {
		$post_type_obj = self::get( $post_type );

		return $post_type_obj ? $post_type_obj->labels : null;
	}

	/**
	 * Get specific label for a post type.
	 *
	 * @param string $post_type Post type name.
	 * @param string $label     Label key (e.g., 'name', 'singular_name').
	 *
	 * @return string|null Label value or null if not found.
	 */
	public static function get_label( string $post_type, string $label ): ?string {
		$post_type_obj = self::get( $post_type );

		return $post_type_obj && isset( $post_type_obj->labels->{$label} )
			? $post_type_obj->labels->{$label}
			: null;
	}

	/**
	 * Get singular label.
	 *
	 * @param string $post_type Post type name.
	 *
	 * @return string|null Singular label or null if not found.
	 */
	public static function get_singular_label( string $post_type ): ?string {
		return self::get_label( $post_type, 'singular_name' );
	}

	/**
	 * Get plural label.
	 *
	 * @param string $post_type Post type name.
	 *
	 * @return string|null Plural label or null if not found.
	 */
	public static function get_plural_label( string $post_type ): ?string {
		return self::get_label( $post_type, 'name' );
	}

	/**
	 * Get post type description.
	 *
	 * @param string $post_type Post type name.
	 *
	 * @return string|null Description or null if not found.
	 */
	public static function get_description( string $post_type ): ?string {
		$post_type_obj = self::get( $post_type );

		return $post_type_obj ? $post_type_obj->description : null;
	}

	/**
	 * Get menu position for a post type.
	 *
	 * @param string $post_type Post type name.
	 *
	 * @return int|null Menu position or null if not set.
	 */
	public static function get_menu_position( string $post_type ): ?int {
		$post_type_obj = self::get( $post_type );

		return $post_type_obj ? $post_type_obj->menu_position : null;
	}

	// ========================================
	// Properties & Status
	// ========================================

	/**
	 * Check if post type is hierarchical.
	 *
	 * @param string $post_type Post type name.
	 *
	 * @return bool True if hierarchical, false otherwise.
	 */
	public static function is_hierarchical( string $post_type ): bool {
		$post_type_obj = self::get( $post_type );

		return $post_type_obj ? $post_type_obj->hierarchical : false;
	}

	/**
	 * Check if post type is public.
	 *
	 * @param string $post_type Post type name.
	 *
	 * @return bool True if public, false otherwise.
	 */
	public static function is_public( string $post_type ): bool {
		$post_type_obj = self::get( $post_type );

		return $post_type_obj ? $post_type_obj->public : false;
	}

	/**
	 * Check if post type is built-in.
	 *
	 * @param string $post_type Post type name.
	 *
	 * @return bool True if built-in, false otherwise.
	 */
	public static function is_built_in( string $post_type ): bool {
		$post_type_obj = self::get( $post_type );

		return $post_type_obj ? $post_type_obj->_builtin : false;
	}

	/**
	 * Check if post type has archive.
	 *
	 * @param string $post_type Post type name.
	 *
	 * @return bool True if has archive, false otherwise.
	 */
	public static function has_archive( string $post_type ): bool {
		$post_type_obj = self::get( $post_type );

		return $post_type_obj ? (bool) $post_type_obj->has_archive : false;
	}

	/**
	 * Check if post type shows in REST API.
	 *
	 * @param string $post_type Post type name.
	 *
	 * @return bool True if shows in REST, false otherwise.
	 */
	public static function show_in_rest( string $post_type ): bool {
		$post_type_obj = self::get( $post_type );

		return $post_type_obj ? $post_type_obj->show_in_rest : false;
	}

	// ========================================
	// Features & Support
	// ========================================

	/**
	 * Check if post type supports a feature.
	 *
	 * @param string $post_type Post type name.
	 * @param string $feature   Feature to check.
	 *
	 * @return bool True if supports feature, false otherwise.
	 */
	public static function supports_feature( string $post_type, string $feature ): bool {
		return post_type_supports( $post_type, $feature );
	}

	/**
	 * Check if post type supports thumbnails.
	 *
	 * @param string $post_type Post type name.
	 *
	 * @return bool True if supports thumbnails, false otherwise.
	 */
	public static function supports_thumbnails( string $post_type ): bool {
		return self::supports_feature( $post_type, 'thumbnail' );
	}

	/**
	 * Check if post type supports comments.
	 *
	 * @param string $post_type Post type name.
	 *
	 * @return bool True if supports comments, false otherwise.
	 */
	public static function supports_comments( string $post_type ): bool {
		return self::supports_feature( $post_type, 'comments' );
	}

	/**
	 * Check if post type supports revisions.
	 *
	 * @param string $post_type Post type name.
	 *
	 * @return bool True if supports revisions, false otherwise.
	 */
	public static function supports_revisions( string $post_type ): bool {
		return self::supports_feature( $post_type, 'revisions' );
	}

	// ========================================
	// Capabilities
	// ========================================

	/**
	 * Get post type capabilities.
	 *
	 * @param string $post_type Post type name.
	 *
	 * @return object|null Capabilities object or null if not found.
	 */
	public static function get_capabilities( string $post_type ): ?object {
		$post_type_obj = self::get( $post_type );

		return $post_type_obj ? $post_type_obj->cap : null;
	}

	// ========================================
	// Taxonomies & Relationships
	// ========================================

	/**
	 * Get taxonomies for a post type.
	 *
	 * @param string $post_type Post type name.
	 * @param string $output    Optional. Output type ('names' or 'objects'). Default 'names'.
	 *
	 * @return array Array of taxonomy names or objects.
	 */
	public static function get_taxonomies( string $post_type, string $output = 'names' ): array {
		return get_object_taxonomies( $post_type, $output );
	}

	/**
	 * Check if post type is registered for a taxonomy.
	 *
	 * @param string $post_type Post type name.
	 * @param string $taxonomy  Taxonomy name.
	 *
	 * @return bool True if registered, false otherwise.
	 */
	public static function is_registered_for_taxonomy( string $post_type, string $taxonomy ): bool {
		$taxonomies = self::get_taxonomies( $post_type );

		return in_array( $taxonomy, $taxonomies, true );
	}

	// ========================================
	// URLs & Archives
	// ========================================

	/**
	 * Get archive URL for a post type.
	 *
	 * @param string $post_type Post type name.
	 *
	 * @return string|false Archive URL or false if no archive.
	 */
	public static function get_archive_url( string $post_type ) {
		$post_type_obj = self::get( $post_type );

		return $post_type_obj && $post_type_obj->has_archive ? get_post_type_archive_link( $post_type ) : false;
	}

	/**
	 * Get archive slug for a post type.
	 *
	 * @param string $post_type Post type name.
	 *
	 * @return string|null Archive slug or null if no archive.
	 */
	public static function get_archive_slug( string $post_type ): ?string {
		$post_type_obj = self::get( $post_type );
		if ( ! $post_type_obj || ! $post_type_obj->has_archive ) {
			return null;
		}

		return is_string( $post_type_obj->has_archive )
			? $post_type_obj->has_archive
			: $post_type_obj->rewrite['slug'] ?? $post_type;
	}

	// ========================================
	// REST API
	// ========================================

	/**
	 * Get REST API base slug for a post type.
	 *
	 * @param string $post_type Post type name.
	 *
	 * @return string|null REST base slug or null if no REST support.
	 */
	public static function get_rest_base( string $post_type ): ?string {
		$post_type_obj = self::get( $post_type );
		if ( ! $post_type_obj || ! $post_type_obj->show_in_rest ) {
			return null;
		}

		return $post_type_obj->rest_base ?? $post_type;
	}

	// ========================================
	// Post Counts & Stats
	// ========================================

	/**
	 * Count posts in a post type.
	 *
	 * @param string $post_type Post type name.
	 * @param string $status    Optional. Post status. Default 'publish'.
	 *
	 * @return int Number of posts.
	 */
	public static function count_posts( string $post_type, string $status = 'publish' ): int {
		$counts = wp_count_posts( $post_type );

		return (int) ( $counts->{$status} ?? 0 );
	}

}