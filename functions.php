<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Access denied.' ); 
}

include_once 'simple_html_dom.php';

/**
 * Enqueue parent stylesheet
 */
add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
});

/**
 * Add mwp-bootstrap class to body class
 */
add_filter( 'body_class', function( $classes ) {
	$classes[] = 'mwp-bootstrap';
	return $classes;
});

/**
 * Post link shortcode
 */
add_shortcode( 'permalink', function( $atts ) {
	if ( isset( $atts['id'] ) ) {
		return get_permalink( $atts['id'] );
	}
});

add_filter( 'the_content', function( $content ) {
	if ( get_post_type() == 'docs' ) {
		if ( $html = str_get_html( $content ) ) {
			$headings = array();
			foreach( $html->find('h3') as $h3 ) {
				if ( strpos( $h3->innertext, '@' ) !== false ) {
					$id = sanitize_title( $h3->innertext );
					$headings[] = array( 'title' => $h3->innertext, 'id' => $id );
					$content = str_replace( '<h3>' . $h3->innertext . '</h3>', "<h3><a id=\"{$id}\" class=\"section-ref\"></a>" . str_replace( '@ ', '@', $h3->innertext ) .  '</h3>', $content );
				}
			}
			foreach( $html->find('h6') as $h6 ) {
				if ( $code = $h6->find('code') ) {
					$text = $code[0]->plaintext;
					$text = preg_replace( '/\((.*?)\)/', '', $text );
					$text = preg_replace( '/^(.*?)(::|-\&gt;)/', '', $text );
					$id = sanitize_title( $text );
					$content = str_replace( '<h6>' . $h6->innertext . '</h6>', "<h6><a id=\"{$id}\" class=\"section-ref\"></a>" . $h6->innertext .  '</h6>', $content );
				}
			}
			unset( $html );
			
			$heading_html = '';
			if ( ! empty( $headings ) ) {
				$heading_html .= '<ul class="section-links">';
				foreach( $headings as $heading ) {
					$heading_html .= "<li><a href=\"#{$heading['id']}\">" . esc_html( $heading['title'] ) . "</a></li>";
				}
				$heading_html .= "</ul>";
			}
			
			$content = $heading_html . $content;
		}
	}
	
	return $content;
});