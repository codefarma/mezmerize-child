<?php
/**
 * The template for displaying a single doc
 *
 * To customize this template, create a folder in your current theme named "wedocs" and copy it there.
 *
 * @package weDocs
 */

$skip_sidebar = ( get_post_meta( $post->ID, 'skip_sidebar', true ) == 'yes' ) ? true : false;

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main container" role="main">
        <?php while ( have_posts() ) : the_post(); ?>

            <div class="wedocs-single-wrap">

                <?php if ( ! $skip_sidebar ) { ?>

                    <?php wedocs_get_template_part( 'docs', 'sidebar' ); ?>

                <?php } ?>

                <div class="wedocs-single-content">
                    <?php wedocs_breadcrumbs(); ?>

                    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?> itemscope itemtype="http://schema.org/Article">
                        <header class="entry-header">
                            <?php the_title( '<h1 class="entry-title" itemprop="headline">', '</h1>' ); ?>

                            <?php if ( wedocs_get_option( 'print', 'wedocs_settings', 'on' ) == 'on' ): ?>
                                <a href="#" class="wedocs-print-article wedocs-hide-print wedocs-hide-mobile" title="<?php echo esc_attr( __( 'Print this article', 'wedocs' ) ); ?>"><i class="wedocs-icon wedocs-icon-print"></i></a>
                            <?php endif; ?>
                        </header><!-- .entry-header -->

                        <div class="entry-content" itemprop="articleBody">
                            <?php
                                the_content( sprintf(
                                    /* translators: %s: Name of current post. */
                                    wp_kses( __( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'wedocs' ), array( 'span' => array( 'class' => array() ) ) ),
                                    the_title( '<span class="screen-reader-text">"', '"</span>', false )
                                ) );

                                wp_link_pages( array(
                                    'before' => '<div class="page-links">' . esc_html__( 'Docs:', 'wedocs' ),
                                    'after'  => '</div>',
                                ) );
								
								/* Look for first child */
								$next = current( get_children( array( 'numberposts' => 1, 'post_parent' => $post->ID, 'post_status' => 'publish', 'orderby' => 'menu_order', 'order' => 'ASC' ) ) );
								
								/* Look for next sibling */
								$getNextSiblingOf = function( $post ) {
									if ( $post->post_parent ) {
										$children = get_children( array( 'post_parent' => $post->post_parent, 'post_status' => 'publish', 'orderby' => 'menu_order', 'order' => 'ASC' ) );
										while( $child = array_shift( $children ) ) {
											if ( $child->ID == $post->ID ) {
												return array_shift( $children );
											}
										}
									}
								};
								
								if ( ! $next ) {
									$next = $getNextSiblingOf( $post );
								}
								
								/* Look for next parent */
								$_current = $post;
								while( ! $next and $_current->post_parent ) {
									$_current = get_post( $_current->post_parent );
									$next = $getNextSiblingOf( $_current );
								}
								
								if ( ! trim( $post->post_content ) ) 
								{
									$children = wp_list_pages("title_li=&order=menu_order&child_of=". $post->ID ."&echo=0&post_type=" . $post->post_type);
									if ( $children ) {
										echo '<div class="article-child">';
											//echo '<h3>' . __( 'Sections', 'wedocs' ) . '</h3>';
											echo '<ul>';
												echo $children;
											echo '</ul>';
										echo '</div>';
									}
								}

								if ( $next ) {
									echo "<div class='text-center' style='margin:60px 0;'><a class='btn btn-success btn-lg' href='" . get_permalink( $next ) . "'>" . esc_html( $next->post_title ) . " &nbsp;<i class='fa fa-angle-double-right'></i></a></div>";
								}

                                $tags_list = wedocs_get_the_doc_tags( $post->ID, '', ', ' );

                                if ( $tags_list ) {
                                    printf( '<span class="tags-links"><span class="screen-reader-text">%1$s </span>%2$s</span>',
                                        _x( 'Tags', 'Used before tag names.', 'wedocs' ),
                                        $tags_list
                                    );
                                }
                            ?>
                        </div><!-- .entry-content -->

                        <footer class="entry-footer wedocs-entry-footer">
                            <?php if ( wedocs_get_option( 'email', 'wedocs_settings', 'on' ) == 'on' ): ?>
                                <span class="wedocs-help-link wedocs-hide-print wedocs-hide-mobile">
                                    <i class="wedocs-icon wedocs-icon-envelope"></i>
                                    <?php printf( '%s <a id="wedocs-stuck-modal" href="%s">%s</a>', __( 'Still stuck?', 'wedocs' ), '#', __( 'How can we help?', 'wedocs' ) ); ?>
                                </span>
                            <?php endif; ?>

                            <div class="wedocs-article-author" itemprop="author" itemscope itemtype="https://schema.org/Person">
                                <meta itemprop="name" content="<?php echo get_the_author(); ?>" />
                                <meta itemprop="url" content="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>" />
                            </div>

                            <meta itemprop="datePublished" content="<?php echo get_the_time( 'c' ); ?>"/>
                            <time itemprop="dateModified" datetime="<?php echo esc_attr( get_the_modified_date( 'c' ) ); ?>"><?php printf( __( 'Updated on %s', 'wedocs' ), get_the_modified_date() ); ?></time>
                        </footer>

                        <?php wedocs_doc_nav(); ?>

                        <?php if ( wedocs_get_option( 'helpful', 'wedocs_settings', 'on' ) == 'on' ): ?>
                            <?php wedocs_get_template_part( 'content', 'feedback' ); ?>
                        <?php endif; ?>

                        <?php if ( wedocs_get_option( 'email', 'wedocs_settings', 'on' ) == 'on' ): ?>
                            <?php wedocs_get_template_part( 'content', 'modal' ); ?>
                        <?php endif; ?>

                    </article><!-- #post-## -->
                </div><!-- .wedocs-single-content -->
            </div><!-- .wedocs-single-wrap -->

        <?php endwhile; ?>

    </main><!-- .site-main -->

</div><!-- .content-area -->

<?php get_footer(); ?>
