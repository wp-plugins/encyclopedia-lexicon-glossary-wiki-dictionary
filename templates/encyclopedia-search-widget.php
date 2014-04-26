<form role="search" method="get" id="encyclopedia-searchform" action="<?php Echo Esc_URL(home_url('/')) ?>">
  <label class="screen-reader-text" for="encyclopedia-saerch-term"><?php _e('Search for:') ?></label>
  <input type="text" id="s" name="s" class="search-field" value="<?php the_search_query() ?>" placeholder="<?php Echo esc_attr_x( 'Search &hellip;', 'placeholder' ) ?>">
  <input type="submit" class="search-submit submit button" id="encyclopedia-search-submit" value="<?php esc_attr_e('Search') ?>">
  <input type="hidden" name="post_type" value="<?php Echo $this->post_type ?>">
</form>