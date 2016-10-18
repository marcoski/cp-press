<?php

echo $filter->apply('cppress_filter_form',
	'<form method="GET" class="filters-form" data-filter-url="'.$url.'" data-query="'.htmlspecialchars(json_encode($query, JSON_HEX_TAG)).'">', $url, $query);
