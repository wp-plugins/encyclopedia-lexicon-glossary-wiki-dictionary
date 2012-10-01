<div class="encyclopedia-filters">
<?php
/* Use the $filter var to access all available filters */

ForEach ($filter AS $filter_line) : ?>

	<div class="encyclopedia-filter">

		<?php ForEach ($filter_line AS $element) : ?>
			<span class="filter <?php Echo ($element->active) ? 'current-filter' : '' ?>">
				<a href="<?php Echo $element->link ?>" class="filter-link"><?php Echo $element->filter ?></a>
			</span>
		<?php EndForEach ?>

	</div>

<?php EndForEach ?>
</div>