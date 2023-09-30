<?php

namespace App\Jobs;

use App\CapilleryAuthenticate;
use App\Customer;
use App\shopPages;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AppUninstalledJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * Shop's myshopify domain
     *
     * @var string
     */
    public $shopDomain;

    /**
     * The webhook data
     *
     * @var object
     */
    public $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($shopDomain, $data)
    {
        $this->shopDomain = $shopDomain;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $shop = User::where('name', $this->shopDomain)->orWhere('alternate_name' , $this->shopDomain)->first();
            //Log::error( date('Y-m-d H:i:s'));

            User::where('id', $shop->id)->update(array('deleted_at' => date('Y-m-d H:i:s'),'plan_id'=>null));
            /*$shop->update([
                'deleted_at' => '2022-01-07 13:53:09'
            ]);*/

            $settings = Customer::where('user_id', $shop->id)->first();
            if ($settings)
                $settings->delete();

            $shopPages = shopPages::where('user_id', $shop->id)->first();
            if ($shopPages)
                $shopPages->delete();
            $authenticate = CapilleryAuthenticate::where('user_id',$shop->id)->first();
            if($authenticate)
                $authenticate->delete();

            return 'customer data deleted successfully';
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
