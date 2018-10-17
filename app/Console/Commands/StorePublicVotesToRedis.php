<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class StorePublicVotesToRedis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'StorePublicVotesToRedis';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '把redis中所有候选人的大众票数遍历统计的总和存储再redis中';

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
        //投票总数
        $redis_votes = Redis::keys('rongyao2018:vote:*');
        $public_votes = 0;
        foreach ($redis_votes as $v){
            $public_votes += Redis::hget($v,'public_votes'); //需求只统计大众投票数
        }
        Redis::set('rongyao2018:public_votes_sum',$public_votes);
    }
}
