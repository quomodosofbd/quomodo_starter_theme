<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Q_Blog_Starter
 */

if ( ! is_active_sidebar( 'main_sidebar' ) ) {
	return;
}
?>
<?php dynamic_sidebar( 'main_sidebar' ); ?>