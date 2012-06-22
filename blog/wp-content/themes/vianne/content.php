<article class="post">
<h3><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h3>

<p class="date"><?php echo get_the_date(); ?></p>

<?php the_content(); ?>

<p><?php the_category(''); ?></p>
</article><!-- /post -->
