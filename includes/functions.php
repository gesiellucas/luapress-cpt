<?php 

function lpcpt_create_article_post() 
{
    $labels = array(
        'name' => 'Artigos',
        'singular_name' => 'Artigo',
        'menu_name' => 'Artigos',
        'add_new' => 'Adicionar novo artigo',
        'add_new_item' => 'Adicionar novo artigo',
        'edit_item' => 'Editar item artigo',
        'new_item' => 'Novo artigo',
        'view_item' => 'Ver artigo',
        'search_items' => 'Buscar artigos',
        'not_found' => 'Nenhum artigo encontrado',
        'not_found_in_trash' => 'Nenhum artigo encontrado na lixeira',
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-format-aside',
        'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt' ),
    );

    register_post_type( LPCPT_SLUG, $args );
}

function lpcpt_create_taxonomy()
{
    foreach (LPCPT_TAXONOMIES as $taxonomy) {
        lpcpt_save_taxonomy($taxonomy[0], $taxonomy[1]);
    }

    // lpcpt_seeding_taxonomies();
}

function lpcpt_custom_template( $template ) 
{  
    if ( is_singular( LPCPT_SLUG ) ) {
        $new_template = plugin_dir_path( __FILE__ ) . 'templates/page-template.php';
        if ( '' !== $new_template ) {
            return $new_template;
        }
    }
    return $template;
}

function lpcpt_scripts_styles() 
{
    if ( is_singular( LPCPT_SLUG ) ) {
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'custom-blog-tailwind', plugins_url( 'assets/js/tailwind.js', __FILE__ ), null, '0.1.0' );
        wp_enqueue_script( 'custom-blog-script', plugins_url( 'assets/js/custom-blog-script.js?v=1', __FILE__ ), 'jquery', '0.1.0' );
        wp_enqueue_style( 'custom-blog-style', plugins_url( 'assets/css/custom-blog-style.css?v=1', __FILE__), null, '0.1.0' );
    }
    
}

function lpcpt_render_header()
{
    require PREFIX_BASE_PATH . 'templates/header-template.php';
}

function lpcpt_save_taxonomy($singular, $plural)
{
    $post_type = [LPCPT_SLUG];
    $prefix = 'lpcpt_';

    $labels = array(
        'name'                       => _x( $singular, 'Taxonomy General Name', 'text_domain' ),
        'singular_name'              => _x( $singular, 'Taxonomy Singular Name', 'text_domain' ),
        'menu_name'                  => __( $prefix . $singular, 'text_domain' ),
        'all_items'                  => __( 'Todos ' . $plural, 'text_domain' ),
        'parent_item'                => __( $plural . ' Acima', 'text_domain' ),
        'parent_item_colon'          => __( $singular . ' Acima:', 'text_domain' ),
        'new_item_name'              => __( 'Novo ' . $singular, 'text_domain' ),
        'add_new_item'               => __( 'Novo ' . $singular, 'text_domain' ),
        'edit_item'                  => __( 'Editar ' . $singular, 'text_domain' ),
        'update_item'                => __( 'Alterar ' . $singular, 'text_domain' ),
        'view_item'                  => __( 'Ver ' . $singular, 'text_domain' ),
        'separate_items_with_commas' => __( 'Separar ' . $plural . ' com vírgula', 'text_domain' ),
        'add_or_remove_items'        => __( 'Adicionar ou Remover ' . $plural, 'text_domain' ),
        'choose_from_most_used'      => __( 'Escolher ' . $plural . ' mais usados', 'text_domain' ),
        'popular_items'              => __( $plural . ' populares', 'text_domain' ),
        'search_items'               => __( 'Buscar ' . $plural, 'text_domain' ),
        'not_found'                  => __( 'Não encontrado', 'text_domain' ),
        'no_terms'                   => __( 'Nada encontrado', 'text_domain' ),
        'items_list'                 => __( 'Lista de ' . $plural, 'text_domain' ),
        'items_list_navigation'      => __( 'Lista de ' . $plural . ' navegação', 'text_domain' ),
    );

    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => false, // Set this to true if you want your taxonomy to behave like categories
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
        'query_var'                  => true
    );

    register_taxonomy( remove_accents(strtolower($prefix . $singular)), $post_type, $args );
}

function lpcpt_get_taxonomy($taxonomy = null)
{
    $terms = get_terms([
        'taxonomy' => $taxonomy,
        'hide_empty' => false
    ]);

    return array_map(function($value){
        return $value->name ;
    }, $terms);

}



function lpcpt_build_form($title, $taxonomy, $slug)
{
    require PREFIX_BASE_PATH . 'templates/form-template.php'; 
}

function lpcpt_select_filter()
{
    $query = $_GET;

    $taxonomy['relation'] = 'OR';

    if(isset($query['territorio']))  {
        array_push($taxonomy, [
            'taxonomy' => 'lpcpt_territorio',
            'field' => 'slug',
            'terms' => $query['territorio']
        ]);
    }

    if(isset($query['tema'])) {
        array_push($taxonomy, [
            'taxonomy' => 'lpcpt_tema',
            'field' => 'slug',
            'terms' => $query['tema']
        ]);
    }

    $args = array(
        'post_type' => LPCPT_SLUG,
        'posts_per_page' => -1,
        'tax_query' => $taxonomy
    );

    return $args;

}