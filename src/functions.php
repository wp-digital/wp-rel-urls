<?php

if ( ! function_exists( 'innocode_get_post_rel_urls' ) ) {
    /**
     * @param int $post_id
     * @return array
     */
    function innocode_get_post_rel_urls( int $post_id ) : array {
        $home_url = home_url( '/' );

        $urls = [
            get_the_permalink( $post_id ),
            $home_url,
            get_bloginfo_rss( 'rdf_url' ),
            get_bloginfo_rss( 'rss_url' ),
            get_bloginfo_rss( 'rss2_url' ),
            get_bloginfo_rss( 'atom_url' ),
            get_bloginfo_rss( 'comments_rss2_url' ),
            get_post_comments_feed_link( $post_id ),
        ];

        $post_author = get_post_field('post_author', $post_id );

        if ( $post_author ) {
            array_push(
                $urls,
                get_author_posts_url( $post_author ),
                get_author_feed_link( $post_author )
            );
        }

        $post_type = get_post_type( $post_id );
        $post_type_archive_link = get_post_type_archive_link( $post_type );

        if ( $post_type_archive_link && untrailingslashit( $post_type_archive_link ) != untrailingslashit( $home_url ) ) {
            array_push(
                $urls,
                $post_type_archive_link,
                get_post_type_archive_feed_link( $post_type )
            );
        }

        if ( 'post' == $post_type ) {
            $year = get_the_date( 'Y', $post_id );
            $month = get_the_date( 'm', $post_id );
            $day = get_the_date( 'd', $post_id );

            array_push(
                $urls,
                get_year_link( $year ),
                get_month_link( $year, $month ),
                get_day_link( $year, $month, $day )
            );
        }

        foreach( get_post_taxonomies( $post_id ) as $taxonomy_name ) {
            $taxonomy = get_taxonomy( $taxonomy_name );

            if ( ! $taxonomy || ! $taxonomy->public ) {
                continue;
            }

            $terms = get_the_terms( $post_id, $taxonomy_name );

            if ( empty( $terms ) || is_wp_error( $terms ) ) {
                continue;
            }

            foreach ( $terms as $term ) {
                array_push(
                    $urls,
                    get_term_link( $term ),
                    get_term_feed_link( $term->term_id, $term->taxonomy )
                );
            }
        }

        return $urls;
    }
}

if ( ! function_exists( 'innocode_get_term_rel_urls' ) ) {
    /**
     * @param int $term_taxonomy_id
     * @return array
     */
    function innocode_get_term_rel_urls( int $term_taxonomy_id ) : array {
        return false !== ( $term = get_term_by( 'term_taxonomy_id', $term_taxonomy_id ) )
            ? [
                get_term_link( $term ),
                get_term_feed_link( $term->term_id, $term->taxonomy ),
                home_url( '/' ),
            ] : [];
    }
}

if ( ! function_exists( 'innocode_get_user_rel_urls' ) ) {
    /**
     * @param int $user_id
     * @return array
     */
    function innocode_get_user_rel_urls( int $user_id ) : array {
        return [
            get_author_posts_url( $user_id ),
            get_author_feed_link( $user_id ),
            home_url( '/' ),
        ];
    }
}
