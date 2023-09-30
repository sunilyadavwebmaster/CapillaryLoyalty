<?php
namespace App\Jobs;

use App\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Osiset\ShopifyApp\Objects\Values\ShopDomain;


class DomainsCreateJob implements ShouldQueue{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $shopDomain;

    /**
     * The webhook data
     *
     * @var object
     */
    public $data;

    public function __construct($shopDomain, $data)
    {
        $this->shopDomain = $shopDomain;
        $this->data = $data;
    }

    public function handle(){
        $data = json_decode(json_encode($this->data), true);
        \Log::info('Domains Webhook -- '.$this->shopDomain." " . json_encode($this->data));

        $shop = User::where('name',$this->shopDomain)->orWhere('alternate_name' , $this->shopDomain)->first();
        if($shop)
        {
            User::where('id', $shop->id)->update(array('alternate_name' => $data['host']));
            \Log::info('Domains Webhook -- Updated'.$this->shopDomain.'--'.$data['host']);
        }else{
            \Log::info('Domains Webhook -- Not Updated'.$this->shopDomain.'--'.$data['host']);
        }
        
    }
}