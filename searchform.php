<form role="search" method="get" class="searchForm" action="<?php echo home_url( '/' ); ?>">
  <label class="searchForm-label">
    <input type="search" class="searchForm-field" placeholder="Искать ..." value="<?php echo get_search_query() ?>" name="s" title="Ваш вопрос:" />
  </label>

  <input type="submit" class="searchForm-submit" value="Найти" />
</form>