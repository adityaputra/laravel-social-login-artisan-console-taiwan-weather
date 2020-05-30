<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;
use Symfony\Component\DomCrawler\Crawler;
use App\Weather;

class GetTaiwanWeather extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twweather:retrieve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
	//
	echo "hello world";
	$this->doScrape();
    }

    protected function doScrape(){
	$goutteClient = new Client();
	$guzzleClient = new GuzzleClient(array('timeout'=>60));
	$goutteClient->setClient($guzzleClient);
	$crawler = $goutteClient->request('GET', 'https://www.cwb.gov.tw/V8/C/W/County/MOD/wf7dayNC_NCSEI/ALL_Week.html?');
	$crawler->filter('div.city-weeek div.panel')->each(function ($node_city){

            $s_city = $node_city->filter('.panel-title ul li.right a')->text();
            $c_date = $node_city->filter('.panel-body ul')->each(function($node) use (&$s_city){
                $s_date = $node->filter('.date .daily')->text();
                $s_day_signal = $node->filter('li.Day img')->attr('alt');
	        $s_day_temp = $node->filter('li.Day span.tem-C')->text();
	        $s_night_signal = $node->filter('li.Night img')->attr('alt');
	        $s_night_temp = $node->filter('li.Night span.tem-C')->text();
	        $weather = new Weather;
	        $weather->city_id = $s_city;
	        $weather->city_name = $s_city;
	        $weather->date = $s_date;
	        $weather->day_signal = $s_day_signal;
	        $weather->day_temp = $s_day_temp;
	        $weather->night_signal = $s_night_signal;
	        $weather->night_temp = $s_night_temp;
	        $weather->save();
	        dump($weather->toArray());
            });

	});
    }
}
