# WordPress Post Type Utilities

A lightweight WordPress library for working with post types and post type operations. Provides clean APIs for post type information retrieval, capability checking, and search functionality with value/label formatting perfect for forms and admin interfaces.

## Features

* 🎯 **Clean API**: WordPress-style snake_case methods with consistent interfaces
* 🔍 **Built-in Search**: Post type search with value/label formatting
* 📋 **Form-Ready Options**: Perfect value/label arrays for selects and forms
* 🛠️ **Feature Detection**: Check post type capabilities and feature support
* 📊 **Information Retrieval**: Access labels, capabilities, and properties
* 🎨 **Flexible Filtering**: Filter by features, hierarchical status, etc.
* 🔗 **Taxonomy Relationships**: Easy taxonomy-post type relationship checking
* ⚡ **Status Checking**: Built-in, public, hierarchical, and REST API status checks

## Requirements

* PHP 7.4 or later
* WordPress 5.0 or later

## Installation

```bash
composer require arraypress/wp-post-type-utils
```

## Basic Usage

### Working with Single Post Types

```php
use ArrayPress\PostTypeUtils\PostType;

// Check if post type exists
if ( PostType::exists( 'product' ) ) {
	// Post type exists
}

// Get post type object
$post_type_obj = PostType::get( 'product' );

// Get labels
$singular     = PostType::get_singular_label( 'product' );
$plural       = PostType::get_plural_label( 'product' );
$custom_label = PostType::get_label( 'product', 'add_new_item' );
$description  = PostType::get_description( 'product' );

// Check properties and status
if ( PostType::is_hierarchical( 'product' ) ) {
	// Post type is hierarchical
}

if ( PostType::is_public( 'product' ) ) {
	// Post type is public
}

if ( PostType::is_built_in( 'product' ) ) {
	// Post type is built-in (core)
}

if ( PostType::has_archive( 'product' ) ) {
	// Post type has archive pages
}

if ( PostType::show_in_rest( 'product' ) ) {
	// Post type appears in REST API
}

// Check feature support
if ( PostType::supports_feature( 'product', 'thumbnail' ) ) {
	// Supports featured images
}

// Quick feature checks
if ( PostType::supports_thumbnails( 'product' ) ) {
	// Supports thumbnails
}

if ( PostType::supports_comments( 'product' ) ) {
	// Supports comments
}

if ( PostType::supports_revisions( 'product' ) ) {
	// Supports revisions
}

// Get taxonomies
$taxonomies       = PostType::get_taxonomies( 'product' );
$taxonomy_objects = PostType::get_taxonomies( 'product', 'objects' );

// Check taxonomy registration
if ( PostType::is_registered_for_taxonomy( 'product', 'product_category' ) ) {
	// Product post type uses product_category taxonomy
}

// Get capabilities
$capabilities = PostType::get_capabilities( 'product' );

// Get admin info
$menu_position = PostType::get_menu_position( 'product' );

// Get archive info
$archive_url  = PostType::get_archive_url( 'product' );
$archive_slug = PostType::get_archive_slug( 'product' );

// REST API
$rest_base = PostType::get_rest_base( 'product' );

// Count posts
$published_count = PostType::count_posts( 'product', 'publish' );
$draft_count     = PostType::count_posts( 'product', 'draft' );
```

### Working with Multiple Post Types

```php
// Check existence
$existing = PostTypes::exists( [ 'post', 'page', 'invalid' ] );
// Returns: ['post', 'page']

// Get multiple post type objects
$post_type_objects = PostTypes::get( [ 'post', 'page', 'product' ] );

// Get all registered post types
$all_types          = PostTypes::get_all();
$public_types       = PostTypes::get_public();
$hierarchical_types = PostTypes::get_hierarchical();

// Get custom post types only (exclude built-in)
$custom_types = PostTypes::get_custom();

// Get post types by taxonomy
$types_with_categories = PostTypes::get_by_taxonomy( 'category' );

// Search post types
$search_results = PostTypes::search( 'product' );

// Search post types and get options
$options = PostTypes::search_options( 'product' );
// Returns: [['value' => 'product', 'label' => 'Products'], ...]

// Get all post types as options
$all_options = PostTypes::get_options();
// Returns: ['post' => 'Posts', 'page' => 'Pages', ...]

// Exclude default post types from options
$custom_options = PostTypes::get_options( [], true );

// Use different label field
$options_with_plural = PostTypes::get_options( [], false, 'name' );
```

### Feature Detection and Analysis

```php
// Get post types by specific features
$editor_types    = PostTypes::get_by_feature( 'editor' );
$thumbnail_types = PostTypes::get_by_feature( 'thumbnail' );

// Check which types support a feature
$thumbnail_supported = PostTypes::supports_feature( [ 'post', 'page', 'product' ], 'thumbnail' );

// Get Gutenberg-supported types
$gutenberg_types = PostTypes::get_gutenberg_supported();

// Single post type feature checks
$supports_editor        = PostType::supports_feature( 'product', 'editor' );
$supports_thumbnail     = PostType::supports_feature( 'product', 'thumbnail' );
$supports_custom_fields = PostType::supports_feature( 'product', 'custom-fields' );
```

### Labels and Capabilities

```php
// Get specific labels for single post type
$add_new   = PostType::get_label( 'product', 'add_new' );
$edit_item = PostType::get_label( 'product', 'edit_item' );
$view_item = PostType::get_label( 'product', 'view_item' );

// Get capabilities for single post type
$caps = PostType::get_capabilities( 'product' );
// Access like: $caps->edit_posts, $caps->delete_posts

// Get labels for multiple post types
$labels     = PostTypes::get_labels( [ 'post', 'page' ], 'singular_name' );
$all_labels = PostTypes::get_labels( [ 'post', 'page' ] ); // All labels

// Get capabilities for multiple post types
$capabilities = PostTypes::get_capabilities( [ 'post', 'page', 'product' ] );
```

### Advanced Usage Examples

```php
// Build admin interface options
function get_content_type_options() {
	return PostTypes::get_options( [
		'public'  => true,
		'show_ui' => true
	], true, 'singular_name' ); // Exclude defaults, use singular names
}

// Check if post type is suitable for content management
function is_manageable_post_type( $post_type ) {
	return PostType::exists( $post_type ) &&
	       PostType::is_public( $post_type ) &&
	       PostType::supports_feature( $post_type, 'editor' );
}

// Get post types that can have featured images
$thumbnail_types = PostTypes::get_by_feature( 'thumbnail' );

// Get all custom post types with archives
$custom_with_archives = array_filter(
	PostTypes::get_custom(),
	fn( $type ) => PostType::has_archive( $type )
);

// Find post types that support both comments and revisions
$advanced_types = array_intersect(
	PostTypes::get_by_feature( 'comments' ),
	PostTypes::get_by_feature( 'revisions' )
);
```

## API Reference

### PostType Class (Single Post Types)

**Core Retrieval:**
- `exists( string $post_type ): bool`
- `get( string $post_type ): ?WP_Post_Type`

**Labels & Information:**
- `get_labels( string $post_type ): ?object`
- `get_label( string $post_type, string $label ): ?string`
- `get_singular_label( string $post_type ): ?string`
- `get_plural_label( string $post_type ): ?string`
- `get_description( string $post_type ): ?string`
- `get_menu_position( string $post_type ): ?int`

**Properties & Status:**
- `is_hierarchical( string $post_type ): bool`
- `is_public( string $post_type ): bool`
- `is_built_in( string $post_type ): bool`
- `has_archive( string $post_type ): bool`
- `show_in_rest( string $post_type ): bool`

**Features & Support:**
- `supports_feature( string $post_type, string $feature ): bool`
- `supports_thumbnails( string $post_type ): bool`
- `supports_comments( string $post_type ): bool`
- `supports_revisions( string $post_type ): bool`

**Capabilities:**
- `get_capabilities( string $post_type ): ?object`

**Taxonomies & Relationships:**
- `get_taxonomies( string $post_type, string $output = 'names' ): array`
- `is_registered_for_taxonomy( string $post_type, string $taxonomy ): bool`

**URLs & Archives:**
- `get_archive_url( string $post_type )`
- `get_archive_slug( string $post_type ): ?string`

**REST API:**
- `get_rest_base( string $post_type ): ?string`

**Post Counts & Stats:**
- `count_posts( string $post_type, string $status = 'publish' ): int`

### PostTypes Class (Multiple Post Types)

**Core Retrieval:**
- `exists( array $post_types ): array`
- `get( array $post_types, bool $include_nonexistent = false ): array`
- `get_all( array $args = [], string $output = 'names', bool $exclude_defaults = false ): array`
- `get_public( string $output = 'names' ): array`
- `get_hierarchical( string $output = 'names' ): array`
- `get_custom( string $output = 'names' ): array`
- `get_by_taxonomy( string $taxonomy ): array`

**Search & Options:**
- `search( string $search, array $args = [] ): array`
- `search_options( string $search, array $args = [] ): array`
- `get_options( array $args = [], bool $exclude_defaults = false, string $label_field = 'singular_name' ): array`

**Feature Analysis:**
- `get_by_feature( string $feature ): array`
- `supports_feature( array $post_types, string $feature ): array`
- `get_gutenberg_supported(): array`

**Labels & Capabilities:**
- `get_labels( array $post_types, string $label = '' ): array`
- `get_capabilities( array $post_types ): array`

## Key Features

- **Value/Label Format**: Perfect for forms and selects
- **Feature Detection**: Comprehensive capability checking
- **Search Functionality**: Built-in post type search with formatting
- **Flexible Filtering**: Multiple filtering options by features, status, etc.
- **Information Access**: Easy access to all post type properties
- **Status Checking**: Built-in, public, hierarchical, and REST API status
- **Taxonomy Relationships**: Easy taxonomy-post type relationship management
- **Custom vs Built-in**: Clear separation between custom and core post types

## Requirements

- PHP 7.4+
- WordPress 5.0+

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is licensed under the GPL-2.0-or-later License.

## Support

- [Documentation](https://github.com/arraypress/wp-post-type-utils)
- [Issue Tracker](https://github.com/arraypress/wp-post-type-utils/issues)