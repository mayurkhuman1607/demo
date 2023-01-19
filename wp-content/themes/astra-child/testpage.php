<?php /* Template Name: testpage */ ?>
<?php get_header(); ?>
<?php
$posts = get_posts(array(
    'numberposts' => -1,
    'post_type' => 'post',
    'orderby' => 'title',
    'order' => 'ASC',
));

$firstLetters = array();
foreach ($posts as $post) {
    $title = $post->post_title;
    $startingLetter = substr($title, 0, 1);
    $dupeFirstLetters[] = $startingLetter;
    $firstLetters = array_unique($dupeFirstLetters);
    sort($firstLetters);
}
foreach ($firstLetters as $letter) {
    echo "<a href=\"?lettre=$letter\">$letter</a>";
}
if (!empty($_GET['lettre'])) {
    $letter = $_GET['lettre'];
} else {
    $letter = $firstLetters[0];
} ?>
<?php get_footer(); ?>