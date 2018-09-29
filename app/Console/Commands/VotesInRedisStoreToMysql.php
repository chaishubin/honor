<?php

namespace App\Console\Commands;

use App\Models\Vote\VoteModel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class VotesInRedisStoreToMysql extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'VotesInRedisStoreToMysql';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '将redis中的票数，定时存入数据库中';

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
        try{
            $info = Redis::keys('rongyao2018:vote:*');
            foreach ($info as $k => $v) {
                $vote = new VoteModel();

                //正则匹配取到id和奖项id
                preg_match('/^rongyao2018:vote:([\d]+?):([\d]+?)$/', $v, $match);

                //如果没有匹配到这两个id，说明存入redis的数据错误，就跳出循环
                if (is_null($match[1]) || is_null($match[2])) {
                    Log::warning('redis中存储的投票信息有误，其key为：' . $v);
                    Log::warning('正则匹配到的candidate_id为：' . $match[1] . ';award_id为：' . $match[2]);
                    continue;
                }
                $keys_value = Redis::hgetall($v);
                $candidate_id = $match[1];
                $award_id = $match[2];
                $score = $keys_value['public_votes'] + $keys_value['expert_votes'] * 4;

                $check = $vote->where(['candidate_id' => $candidate_id, 'award_id' => $award_id])->first();

                if ($check) { //更新操作
                    $check->score = $score;
                    $check->public_votes = $keys_value['public_votes'];
                    $check->expert_votes = $keys_value['expert_votes'];
                    $check->save();

                } else { //新增操作
                    $vote->candidate_id = $candidate_id;
                    $vote->award_id = $award_id;
                    $vote->score = $score;
                    $vote->public_votes = $keys_value['public_votes'];
                    $vote->expert_votes = $keys_value['expert_votes'];
                    $vote->save();
                }
            }
        } catch (\Exception $e){
            Log::info('redis写入mysql失败');
            Log::error($e);
        }
    }
}
