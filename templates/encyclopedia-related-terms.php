<?php Namespace WordPress\Plugin\Encyclopedia;
If($related_terms) : ?>
<h3><?php Echo I18n::t('Related entries') ?></h3>
<ul class="related-terms">
	<?php While($related_terms->Have_Posts()) : $related_terms->The_Post(); ?>
	<li class="term"><a href="<?php The_Permalink() ?>" title="<?php The_Title_Attribute() ?>"><?php The_Title() ?></a></li>
	<?php EndWhile; WP_Reset_Postdata(); ?>
</ul>
<?php EndIf;