<?php

namespace Modules\Shop\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Laravel\Ui\Presets\React;
use Modules\Shop\Repositories\front\interfaces\AddressRepositoryInterfaces;
use Modules\Shop\Repositories\front\interfaces\CartRepositoryInterfaces;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Env;
use Modules\Shop\Repositories\front\interfaces\OrderRepositoryInterface;
use Illuminate\Support\Facades\DB;
class OrderController extends Controller
{
    protected $addressRepository;
    protected $cartRepository;
    protected $orderRepository;

    public function __construct(AddressRepositoryInterfaces $addressRepository , CartRepositoryInterfaces $cartRepository, OrderRepositoryInterface $orderRepository){

             $this->addressRepository = $addressRepository;
             $this->cartRepository = $cartRepository;
             $this->orderRepository = $orderRepository;

        }

    public function checkout(){

        $this->data['addresses'] = $this->addressRepository->findByUser(auth()->user());
        // dd($this->data['addresses']->toArray());
        $this->data['cart'] = $this->cartRepository->findByUser(auth()->user());

        return $this->loadTheme('orders.checkout', $this->data);
    }

    public function store(Request $request){
        // dd($request->all());
        $address= $this->addressRepository->findByID($request->get('address_id'));
        $cart= $this->cartRepository->findByUser(auth()->user());
        $selectedShipping=$this->getSelectedShipping($request );
        // dd($selectedShipping);

        // penyimpanan data order
        DB::beginTransaction();
        try{
            // proses order
                $order = $this->orderRepository->create($request->user(), $cart, $address, $selectedShipping);
            // dd($order->toArray());
            }
            catch (\Exception $e){
                DB::rollback();
                throw $e;
            }
            DB::commit();

            $this->cartRepository->clear(auth()->user());
            return redirect( $order->payment_url);
        }
    


    private function getSelectedShipping(Request $request){
        $address= $this->addressRepository->findByID($request->get('address_id'));
        $cart= $this->cartRepository->findByUser(auth()->user());
        $availableServices= $this->calculateShippingFee($cart, $address, $request->get('courier'));

        $selectedPackage=null;
        if (!empty($availableServices)){

            foreach($availableServices as $service){
                if ($service['services']=== $request->get('delivery_package')){
                    $selectedPackage =$service;
                }
            }
        }
        if ($selectedPackage==null){
            return [];
        }
        return [ 'deliveri_package'=> $request->get('delivery_package'), 'courier'=>$request->get('courier'), 'shipping_fee'=>$selectedPackage['cost']];

    }


    public function shippingFee(Request $request)
    {
        // mengambil data address addressnya 
        // juga mengambil data cart nya sesuai yang dilognkan
        // dd($request->all()); 
        $address= $this->addressRepository->findByID($request->get('address_id'));
        $cart= $this->cartRepository->findByUser(auth()->user());
        // dd($address->toArray());
        // dd($cart->toArray());
        $availableServices= $this->calculateShippingFee($cart, $address, $request->get('courier'));
        // dd($availableServices);
        return $this->loadTheme('orders.available_service', ['services' => $availableServices]);
    }



    public function choosePackage(Request $request){
        $address= $this->addressRepository->findByID($request->get('address_id'));
        $cart= $this->cartRepository->findByUser(auth()->user());

        $availableServices= $this->calculateShippingFee($cart, $address, $request->get('courier'));

        $selectedPackage=null;
        if (!empty($availableServices)){

            foreach($availableServices as $service){
                if ($service['services']=== $request->get('delivery_package')){
                    $selectedPackage =$service;
                }
            }
        }
        if ($selectedPackage==null){
            return [];
        }

        return [
            'shipping_fee' => number_format($selectedPackage['cost']),
            'grand_total' =>number_format($cart->grand_total + $selectedPackage['cost']),
        ];

    }


    private function calculateShippingFee($cart, $address, $courier)
    {
        // dd($courier);

        $shippingFees =[];
        // dd($cart->total_weight);


        try
            {
            $response =Http::withHeaders([
                'key' =>env('API_ONGKIR_KEY'),
            ])->post(env('API_ONGKIR_BASE_URL'). 'cost', [
                'origin' => env('API_ONGKIR_ORIGIN'),
                'destination' => $address->city,
                'weight'=> $cart->total_weight,
                'courier'=>$courier,
            ]);

            $shippingFees = json_decode($response->getBody(), true);

            }
        catch (\Exception $e)
            {
                return [];
            }
        // dd($shippingFees);

        $availableServices =[];
        if (!empty($shippingFees['rajaongkir']['results'])){
            foreach ($shippingFees['rajaongkir']['results'] as $cost){

                if (!empty($cost['costs'])){
                    foreach ($cost['costs'] as $costDetail){
                        $availableServices[]= [
                            'services' =>$costDetail['service'],
                            'description' =>$costDetail['description'],
                            'etd' => $costDetail['cost'][0]['etd'],
                            'cost' => $costDetail['cost'][0]['value'],
                            'courier' => $courier,
                            'address_id'=> $address->id,
                        ];
                    }
                }
            }
        }

        return $availableServices;
    }
}
