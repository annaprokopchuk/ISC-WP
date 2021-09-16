<?php /* Template Name: Події та новини */ ?>

<?php

$context          = Timber::context();
$context['page'] = new Timber\Post();
$context['categories'] = Timber::get_terms('category', array('parent' => 0));
$args = array(
  'post_type' => 'post',
  'posts_per_page' => -1,
 );
 $context['posts'] = new Timber\PostQuery($args);


Timber::render( 'page-events-news.twig', $context );