<?php
/**
 * The template for displaying search forms in gwt_wp
 *
 * @package GWT
 * @since Government Website Template 2.0
 */
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <input type="search" class="search-field"
        placeholder="<?php echo esc_attr_x( 'Search &hellip;', 'placeholder', 'gwt_wp' ); ?>"
        value="<?php echo esc_attr( get_search_query() ); ?>" name="s"
        title="<?php _ex( 'Search for:', 'label', 'gwt_wp' ); ?>">
</form>