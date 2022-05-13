<?php

namespace App\Tool;

    class Paging
    {
        public function pagingComments($page, $comments): array
        {
            $paging = [
                'page' => $page,
                'nbPages' => ceil(count($comments) / 10),
                'nomRoute' => 'product',
                'paramsRoute' => [],
            ];

            return $paging;
        }
    }
