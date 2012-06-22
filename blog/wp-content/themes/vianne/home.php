<?php get_header(); ?>

<div id="main">

<h1>Blog</h1>

<div id="content">
<div id="content-inner">



<?php if(have_posts()): while(have_posts()): the_post(); ?>
	<?php get_template_part('content'); ?>
<?php endwhile; endif; ?>

<?php get_template_part('pagination'); ?>



<?php get_sidebar(); ?>
<?php get_footer(); ?>
