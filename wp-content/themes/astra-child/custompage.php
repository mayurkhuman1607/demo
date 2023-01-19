<?php /* Template Name: custompage*/ ?>
<?php get_header(); ?>


<?php
$args = array(
    'post_type' => 'post',
    'orderby' => 'title',
    'order' => 'ASC',
);
$the_query = new WP_Query( $args );
if ( $the_query->have_posts() ) :
    while ( $the_query->have_posts() ) : $the_query->the_post(); 
        global $post;
        $post_slug=$post->post_name;
        echo $post_slug;
        echo"</br>";
    endwhile;
    wp_reset_query();
endif;
?>

<?php get_footer(); ?>
