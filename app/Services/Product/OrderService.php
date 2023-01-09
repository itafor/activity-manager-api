<?php
namespace App\Services\Product;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Shipment;
use App\Traits\Common;

/**
 *
 */
class OrderService
{
    use Common;

    public function createOrder($request)
    {
            $order = new Order();
            $order->user_id = auth()->user()->id;
            $order->order_type = "individual";
            $order->total = $request->total;
            $order->payment_id = $request->payment_id;
            $order->payment_status = 'paid';
            $order->save();


            if(isset($request->items) && $order){
                $this->saveOrderItems($request->items, $order, $order->order_type);
            }

             if(isset($request->shipment) && $order){
                $this->addShipmentDetails($request->shipment, $order);
            }

            return $order;
    }

     public function saveOrderItems($items, $order, $orderType)
    {
        if(isset($items)){
            foreach ($items as $item) {
            $product = Product::where('id', $item['product_id'])->first();

            $orderItem = new OrderItem();
            $orderItem->order_id = $order->id;
            $orderItem->product_id = $item['product_id'];
            $orderItem->quantity = $item['quantity'];
            $orderItem->amount =  $product->individual_price * $item['quantity'];
            $orderItem->save();
        }
    }
    }


     public function addShipmentDetails($shipmentDetail, $order)
    {
            $shipment = new Shipment();
            $shipment->order_id = $order->id;
            $shipment->first_name =$shipmentDetail['first_name'];
            $shipment->last_name =$shipmentDetail['last_name'];
            $shipment->contact_means = $shipmentDetail['contact'];
            $shipment->tracking_no = $this->randomalAphanumericString(8);
            $shipment->street_address = $shipmentDetail['delivery_address'];
            $shipment->state_id = "1";
            $shipment->town_city = "city";
            $shipment->save();

            return $shipment;
    }

   
    public function myOrders()
    {
        
           return Order::where('user_id', auth()->user()->id)->with(['items', 'payment', 'shipment', 'user', 'group'])->orderBy('id', 'desc')->get();

            
    }

     public function myOrder($orderId)
    {
            return Order::where('id', $orderId)->where('user_id',  auth()->user()->id)->with(['items', 'payment', 'shipment', 'user', 'group'])->first();
    }

    public function listOrders()
    {
           return Order::orderBy('id', 'desc')->with(['items', 'payment', 'shipment', 'user', 'group'])->get();
            
    }

    public function showOrder($orderId)
    {
            return Order::where('id', $orderId)->with(['items', 'payment', 'shipment', 'user', 'group'])->first();
    }

  
}
