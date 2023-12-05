<?php

if (!function_exists("pagination")) {

    function pagination($paginateData)
    {
        $pagination = [];
        $pagination['current_page'] = $paginateData['current_page'];
        $pagination['first_page_url'] = $paginateData['first_page_url'];
        $pagination['from'] = $paginateData['from'];
        $pagination['last_page'] = $paginateData['last_page'];
        $pagination['last_page_url'] = $paginateData['last_page_url'];
        $pagination['links'] = $paginateData['links'];
        $pagination['next_page_url'] = $paginateData['next_page_url'];
        $pagination['per_page'] = $paginateData['per_page'];
        $pagination['prev_page_url'] = $paginateData['prev_page_url'];
        $pagination['to'] = $paginateData['to'];
        $pagination['total'] = $paginateData['total'];

        return $pagination;
    }
}
