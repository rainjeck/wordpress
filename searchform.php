<form role="search" method="get" class="searchForm" action="/">
    <label class="searchForm-label">
        <input type="search" class="searchForm-field" placeholder="Искать ..." value="<?= get_search_query() ?>" name="s" title="Ваш вопрос:">
    </label>

    <input type="submit" class="searchForm-submit" value="Найти">
</form>
