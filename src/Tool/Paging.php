<?php

    namespace App\Tool;

    class Paging
    {
        public function pagingComments($page, $comments): array
        {
            $paging = array(
                'page' => $page,
                'nbPages' => ceil(count($comments) / 10),
                'nomRoute' => 'product',
                'paramsRoute' => array()
            );
            return $paging;
        }
    }