<div class="encyclopedia-prefix-filters">
<?php
/* Use the $filter var to access all available filters */

ForEach ($filter AS $filter_line) : ?>

	<div class="encyclopedia-filter">

		<?php ForEach ($filter_line AS $element) : ?>
			<span class="filter <?php Echo ($element->active) ? 'current-filter ' : ''; Echo ($element->disabled) ? 'disabled-filter ' : '' ?>">
				<?php If ($element->disabled): ?>
          <span class="filter-link">
        <?php Else: ?>
          <a href="<?php Echo $element->link ?>" class="filter-link">
        <?php EndIf ?>

        <?php Echo HTMLEntities($element->filter, Null, 'UTF-8') ?>

        <?php If ($element->disabled): ?>
          </span>
        <?php Else: ?>
          </a>
        <?php EndIf ?>
			</span>
		<?php EndForEach ?>

	</div>

<?php EndForEach ?>
</div>