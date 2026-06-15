<?php
defined( 'ABSPATH' ) || exit;

class LXPG_Schema {

    public function __construct() {
        add_action( 'wp_head', [ $this, 'output_schema' ] );
    }

    public function output_schema(): void {
        if ( ! is_singular( 'project' ) ) {
            return;
        }
        if ( LXPG_Settings::get( 'schema_enabled', '' ) !== '1' ) {
            return;
        }

        $post_id   = get_the_ID();
        $permalink = get_the_permalink( $post_id );

        // ── CreativeWork ──────────────────────────────────────────────────────
        $creative_work = [
            '@type'         => 'CreativeWork',
            '@id'           => $permalink . '#project',
            'name'          => get_the_title( $post_id ),
            'url'           => $permalink,
            'datePublished' => get_the_date( 'c', $post_id ),
            'dateModified'  => get_the_modified_date( 'c', $post_id ),
        ];

        $subtitle = LXPG_Settings::compute_subtitle( $post_id );
        if ( $subtitle !== '' ) {
            $creative_work['headline'] = $subtitle;
        }

        $excerpt = wp_strip_all_tags( get_the_excerpt( $post_id ) );
        if ( $excerpt !== '' ) {
            $creative_work['description'] = $excerpt;
        }

        $image_url = get_the_post_thumbnail_url( $post_id, 'full' );
        if ( $image_url ) {
            $creative_work['image'] = $image_url;
        }

        $categories = get_the_terms( $post_id, 'project_category' );
        if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) {
            $creative_work['genre'] = $categories[0]->name;
        }

        $keywords_field = sanitize_key( LXPG_Settings::get( 'schema_keywords_field', '' ) );
        if ( $keywords_field !== '' && function_exists( 'get_field' ) ) {
            $raw = get_field( $keywords_field, $post_id );
            if ( is_string( $raw ) && $raw !== '' ) {
                $keywords = array_values( array_filter( array_map( 'trim', explode( ',', $raw ) ) ) );
                if ( ! empty( $keywords ) ) {
                    $creative_work['keywords'] = $keywords;
                }
            }
        }

        $org_name = LXPG_Settings::get( 'schema_org_name', '' ) ?: get_bloginfo( 'name' );
        $org_url  = LXPG_Settings::get( 'schema_org_url',  '' ) ?: get_home_url();
        $org_id   = LXPG_Settings::get( 'schema_org_id',   '' ) ?: ( get_home_url() . '/#organization' );

        $creative_work['creator'] = [
            '@type' => 'Organization',
            '@id'   => $org_id,
            'name'  => $org_name,
            'url'   => $org_url,
        ];

        // Client @id derived from URL field (used in both CreativeWork references and the full org node)
        $client_url = '';
        if ( function_exists( 'get_field' ) ) {
            $url_field = sanitize_key( LXPG_Settings::get( 'schema_client_url_field', '' ) );
            if ( $url_field !== '' ) {
                $client_url = (string) get_field( $url_field, $post_id );
            }
        }
        $client_id = $client_url !== '' ? rtrim( $client_url, '/' ) . '/#client' : '';
        if ( $client_id !== '' ) {
            $ref = [ '@id' => $client_id ];
            $creative_work['about']      = $ref;
            $creative_work['mainEntity'] = $ref;
        }

        // ── Graph ─────────────────────────────────────────────────────────────
        $graph = [ $creative_work ];

        // Client Organization node — only added when a client name is available
        $client_name = '';
        $client_desc = '';
        if ( function_exists( 'get_field' ) ) {
            $name_field = sanitize_key( LXPG_Settings::get( 'schema_client_name_field', '' ) );
            $desc_field = sanitize_key( LXPG_Settings::get( 'schema_client_description_field', '' ) );
            if ( $name_field !== '' ) {
                $client_name = (string) get_field( $name_field, $post_id );
            }
            if ( $desc_field !== '' ) {
                $client_desc = (string) get_field( $desc_field, $post_id );
            }
        }

        if ( $client_name !== '' ) {
            $client_org = [ '@type' => 'Organization' ];
            if ( $client_id   !== '' ) $client_org['@id']         = $client_id;
            $client_org['name']                                    = $client_name;
            if ( $client_url  !== '' ) $client_org['url']         = $client_url;
            if ( $client_desc !== '' ) $client_org['description'] = $client_desc;

            $state = get_post_meta( $post_id, 'project_state', true );
            if ( $state !== '' ) {
                $client_org['areaServed'] = $state;
            }

            $graph[] = $client_org;
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@graph'   => $graph,
        ];

        echo '<script type="application/ld+json">'
            . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT )
            . "</script>\n";
    }
}
