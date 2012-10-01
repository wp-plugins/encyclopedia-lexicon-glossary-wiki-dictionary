<ul>
<?php While ($term_query->have_posts()): $term_query->the_post(); ?>
<li><a href="<?php the_permalink() ?>" title="<?php the_title() ?>"><?php the_title() ?></a></li>
<?php EndWhile ?>
</ul>