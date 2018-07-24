<?php
/**
 * Short description for demo.php
 *
 * @package demo
 * @author zhenyangze <zhenyangze@gmail.com>
 * @version 0.1
 * @copyright (C) 2018 zhenyangze <zhenyangze@gmail.com>
 * @license MIT
 */

include_once __DIR__ . '/spiderX.php';

$config = [
    'start' => [
        'http://roll.news.sina.com.cn/news/gnxw/gdxw1/index.shtml',
    ],
    'rule' => [
        [
            'name' => 'list',
            'type' => 'list',
            'url' => 'gdxw1/index_\d+.shtml',
            'data' => [
                'title' => function ($pageInfo, $html, $data) {
                    preg_match_all('/<li><a href=".*?" target="_blank">(.*?)<\/a><span>/i', $html, $matches);
                    return $matches[1];
                },
                'link' => function ($pageInfo, $html, $data) {
                    preg_match_all('/<li><a href="(.*?)" target="_blank">(.*?)<\/a><span>/i', $html, $matches);
                    return $matches[1];
                },
            ]
        ],
        [
            'name' => 'detail',
            'type' => 'detail',
            'url' => 'list.link',
            'data' => [
                'title' => function ($pageInfo, $html, $data) {
                    preg_match_all('/<title>(.*?)<\/title>/i', $html, $matches);
                    return $matches[1][0];
                },
                'link' => function ($pageInfo, $html, $data) {
                    return $pageInfo['context']['link'];
                },
                'blog' => function ($pageInfo, $html, $data) {
                    preg_match_all('/href="\/\/(blog.sina.com.cn\/s\/blog_[^"]*)"/', $html, $matches);
                    $urlList = $matches[1];
                    array_walk($urlList, function (&$url) {
                        $url = 'http://' . $url;
                    });
                    return $urlList;
                }, // list 数据,如果有继承
            ]
        ],
        [
            'name' => 'blog_detail',
            'type' => 'detail',
            'url' => 'detail.blog',
            'data' => [
                'title' => function ($pageInfo, $html, $data) {
                    preg_match_all('/<title>(.*?)<\/title>/i', $html, $matches);
                    return $matches[1][0];
                },
                'keywords' => function ($pageInfo, $html, $data) {
                    preg_match_all('/name="keywords" content="(.*?)" \/>/i', $html, $matches);
                    return $matches[1];
                },
            ]
        ],

    ]
];

$spider = new SpiderX($config);
$spider->on_fetch_detail = function ($pageInfo, $html, $data) {
    echo '[' . $pageInfo['type'] . ']' . $pageInfo['url'] . "\n";
};
$spider->on_fetch_blog_detail = function ($pageInfo, $html, $data) {
    echo '[' . $pageInfo['type'] . ']' . $pageInfo['url'] . "\n";
};

$spider->on_fetch_list = function ($pageInfo, $html, $data) {
    echo '[' . $pageInfo['type'] . ']' . $pageInfo['url'] . "\n";
};
$spider->start();
