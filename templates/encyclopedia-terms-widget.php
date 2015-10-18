<ul>
  <?php While ($term_query->Have_Posts()): $term_query->The_Post(); ?>
  <li><a href="<?php The_Permalink() ?>" title="<?php The_Title_Attribute() ?>"><?php The_Title() ?></a></li>
  <?php EndWhile ?>
</ul>