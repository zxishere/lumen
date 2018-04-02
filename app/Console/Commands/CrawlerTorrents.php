<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use App\Utils\WebCrawler;
use Log;

class CrawlerTorrents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawler:torrents {--site=} {--page=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawler Torrents';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private function crawlerSite($key, $page)
    {
        $site = explode(',', env('SITES'))[$key];
        $cookie = explode(',', env('COOKIES'))[$key] ?? '';
        if ($page != 1) {
            $site = str_replace('page=1', 'page='.$page, $site);
        }
        $crawler = new WebCrawler();
        $html = $crawler->single($site, $cookie);
        $urls = [];
        $i = 1;
        foreach ($html->find('div.tl', 0)->find('table', 0)->find('tr') as $e) {
            if ($e->find('td', 1)->find('a', 0) && str_contains($e->find('td', 1)->find('a', 0)->href, 'fid=') && explode('fid=', $e->find('td', 1)->find('a', 0)->href)[1] == 53) {
                $urls[$i]['url']= explode('home.php', $site)[0]. htmlspecialchars_decode($e->find('th', 0)->find('a', 0)->href);
                $urls[$i]['info']  = "";
                $i++;
            }
        }
        $single_callback = function ($html) {
            preg_match("|【哈希校验】:(.*)<br|Ui", $html->find('td.t_f', 0)->innertext, $matches);
            preg_match("|【影片名稱】:(.*)<br|Ui", $html->find('td.t_f', 0)->innertext, $nameMatches);
            if ($matches[1] && $nameMatches[1]) {
                echo 'magnet:?xt=urn:btih:'.$matches[1].'&dn='.$nameMatches[1].'<br>';
            }
        };
        $multi_callback = function ($instance) use ($crawler, $single_callback) {
            $info = unserialize($instance->getOpt(CURLOPT_PRIVATE));
            $crawler->verifyResponse($info['url'], $instance->responseHeaders, $instance->response);
            $html = $crawler->convert($instance->responseHeaders['Content-Type'], $instance->response, true);
            $single_callback($html);
        };
        $crawler->multi($urls, $multi_callback, $cookie);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $site = $this->option('site');
        $page = (int)$this->option('page') ?? 1;
        switch ($site) {
            case is_int($site):
                $this->crawlerSite($site, $page);
                break;
            default:
                $this->error('No match site found!');
                break;
        }
    }
}
