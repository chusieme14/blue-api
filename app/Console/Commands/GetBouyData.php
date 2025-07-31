<?php

namespace App\Console\Commands;

use App\Models\BouyData;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class GetBouyData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-bouy-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = 100;
        $response = Http::timeout(1800)->get('http://export.blueoceangear.com:4321/api/v1/export_data/?user_id=1286&user_mobile=%2B33603328977&skip=0&limit=' . $limit);
        // $pagination_data = DB::table('paginations')->select('skip')->first();
        $skip = 0;
        $counter = 0;
        $total_data = $response->json()['total'];
        $total_page = ceil($total_data/100);
        // dd($total_data);
        for ($i=1; $i <= $total_page; $i++) {
            $datas = Http::timeout(1800)->get('http://export.blueoceangear.com:4321/api/v1/export_data/?user_id=1286&user_mobile=%2B33603328977&skip=' . $skip . '&limit=' . $limit);
            foreach ($datas->json()['data'] as $key => $data) {
                
                $data['transmit_time'] = Carbon::parse($data['transmit_time']);
                
                if(BouyData::where('transmit_time', $data['transmit_time'])->where('device_id', $data['device_id'])->exists()){
                    $counter++;
                    echo $data['device_id'] . '-' . $data['transmit_time'] . '-'. $key . PHP_EOL;
                    continue;
                }

                // if($counter >= 4) break;

                BouyData::create($data);
            }
            $skip = $i * $limit;
        }

        Artisan::call('app:backup-bouy-csv');

    }
}
