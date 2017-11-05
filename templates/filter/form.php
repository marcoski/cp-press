<?php

echo $filter->apply('cppress_filter_form',
	'<form method="GET" class="filters-form" data-filter-url="'.$url.'" id="'.$id.'" data-query="'.htmlspecialchars(json_encode($query, JSON_HEX_TAG)).'">', $url, $query);
