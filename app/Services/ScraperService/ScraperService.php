<?php

namespace App\Services\ScraperService;

use App\Models\Post;
use App\Services\ScraperService\Base\BaseScraperService;
use Goutte\Client;

class ScraperService extends BaseScraperService {

    protected $page = 1;

    public function __construct($from = false, $to = false, $limit = 0)
    {
        // before assigning check if date string have valid date format 
        $this->from = ($from = strtotime($from)) ? date(Post::DATE_FORMAT, $from) : false;
        $this->to = ($to = strtotime($to)) ? date(Post::DATE_FORMAT, $to) : false;
        $this->has_date = $this->from || $this->to;
            
        $this->limit = (int) $limit > 0 ? (int) $limit : false;
        
        $this->posts = [];
        $this->client = new Client;
        $this->base_url = "https://10web.io/blog/page/";
    }

    function getAll() {
        $i = 1;

        if ($this->has_date) {
            do {
                $scraped_posts = $this->scrapFromPageFilteredByDate();
        
                if ($this->limit && ($posts_count = count($this->posts) + count($scraped_posts)) >= $this->limit) {
                    $limited_posts_count = $posts_count - $this->limit;

                    foreach($scraped_posts as $post_id => $post) {
                        if (!$limited_posts_count) break;
                        $limited_posts_count--;
                        $this->posts[$post_id] = $post;
                    }
                    
                    break;
                } else {
                    $this->posts += $scraped_posts;
                }

                $i++;
                $this->setPage($i);
                
            } while (count($scraped_posts));
        } else {
            do {
                $scraped_posts = $this->scrapFromPage();
        
                if ($this->limit && ($posts_count = count($this->posts) + count($scraped_posts)) >= $this->limit) {
                    $limited_posts_count = $this->limit - count($this->posts);

                    // dd($limited_posts_count, $posts_count, $this->limit, count($scraped_posts));
                    foreach($scraped_posts as $post_id => $post) {
                        if (!$limited_posts_count) break;
                        $limited_posts_count--;
                        $this->posts[$post_id] = $post;
                    }
                    
                    break;
                } else {
                    $this->posts += $scraped_posts;
                }

                $i++;
                $this->setPage($i);
                
            } while (count($scraped_posts));
        }

        return $this->posts;
    }

    function scrapFromPage() {
        $scraped_posts = [];
        $this->client->request('GET', $this->getUrl())->filter('.blog-content .blog-post[class*="post-"]')->each(function($node) use(&$scraped_posts) {
            $post_id = null;
            $class_names = explode(' ', trim($node->attr('class')));

            foreach($class_names as $class) {
                if (strpos($class, 'post-') === 0) {
                    $post_id = (integer) str_replace('post-', '', $class);
                    break;
                }
            }

            if (!$post_id) return;

            $article_date = $node->filter('time[datetime]')->attr('datetime');
            $article_date = date(Post::DATE_FORMAT, strtotime($article_date));
            $image_node = $node->filter('img');
            
            $scraped_posts[$post_id] = [
                'scraped_post_id' => $post_id,
                'title' => $node->filter('.blog-post-title > a')->text(),
                'author' => $node->filter('.post-author-date > a')->text(),
                'image' => $image_node->attr('data-src') ?? $image_node->attr('src'),
                'excerpt' => $node->filter('.entry-summary > p')->text(),
                'article_date' => $article_date,
            ];
        });

        return $scraped_posts;
    }

    function scrapFromPageFilteredByDate() {
        $scraped_posts = [];
        $this->client->request('GET', $this->getUrl())->filter('.blog-content .blog-post[class*="post-"]')->each(function($node) use(&$scraped_posts) {
            $article_date = $node->filter('time[datetime]')->attr('datetime');
            $article_date = date(Post::DATE_FORMAT, strtotime($article_date));

            if (!$this->isInDateRange($article_date)) return;

            $post_id = null;
            $class_names = explode(' ', trim($node->attr('class')));

            foreach($class_names as $class) {
                if (strpos($class, 'post-') === 0) {
                    $post_id = (integer) str_replace('post-', '', $class);
                    break;
                }
            }

            if (!$post_id) return;

            $image_node = $node->filter('img');
            
            $scraped_posts[$post_id] = [
                'scraped_post_id' => $post_id,
                'title' => $node->filter('.blog-post-title > a')->text(),
                'author' => $node->filter('.post-author-date > a')->text(),
                'image' => $image_node->attr('data-src') ?? $image_node->attr('src'),
                'excerpt' => $node->filter('.entry-summary > p')->text(),
                'article_date' => $article_date,
            ];
        });

        return $scraped_posts;
    }

    function isInDateRange($value) {
        return (($this->from && $this->from <= $value) || !$this->from) && ($this->to && $this->to >= $value || !$this->to);
    }

    function setPage($page) {
        return $page > 0 ? $this->page = $page : null;
    }

    function getUrl() {
        return $this->base_url . $this->page;
    }
}