<?php If( $related_terms = $this->Get_Tag_Related_Terms(Null, $attributes['max_terms']) ) : ?>
<h3><?php Echo $this->t('Related entries') ?></h3>
<ul class="related-terms">
	<?php While($related_terms->have_posts()) : $related_terms->the_post(); ?>
	<li class="term"><a href="<?php the_permalink() ?>"><?php the_title() ?></a></li>
	<?php EndWhile; WP_Reset_Postdata(); ?>
</ul>
<?php EndIf;