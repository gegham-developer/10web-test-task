<?php

namespace App\Services\ScraperService;

use App\Services\ScraperService\Base\BaseScraperService;
use Goutte\Client;

class PostsScraperService extends BaseScraperService {

    public function __construct()
    {
        $this->client = new Client;
        $this->base_url = 'https://10web.io/blog/';
    }

    function getPosts() {
        $posts = [];
        $posts = $this->client->request('GET', $this->base_url)->filter('.blog-post')->each(function($node) use(&$posts) {
            $posts[] = $node->text();
        });

        return $posts;
    }
}