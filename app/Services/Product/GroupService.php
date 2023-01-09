<?php
namespace App\Services\Product;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Shipment;
use App\Services\Product\OrderService;
use App\Traits\Common;
use Carbon\Carbon;

/**
 *
 */
class GroupService
{
    use Common;

    public $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function createGroup($request)
    {
            $group = new Group();
            $group->admin_id = auth()->user()->id;
            $group->group_name = $request->group_name;
            $group->product_id = $request->product_id;
            $group->start_date = Carbon::now()->timezone('Africa/Lagos');
            $group->end_date = Carbon::now()->timezone('Africa/Lagos')->addDays(1);
            $group->reference_id = $this->randomalAphanumericString(10);
            $group->status = 'active';
            $group->save();


            if($request && $group){
                $this->addMembersToGroup($group, $request);
                $this->createGroupOrder($group, $request);
            }

            return $group;
    }

     public function addMembersToGroup($group, $request)
    {
        $product = Product::where('id', $group->product_id)->first();

            $member = new GroupMember();
            $member->group_id = $group->id;
            $member->member_id = auth()->user()->id;
            $member->product_quantity = $request->quantity;
            $member->product_size = $request->size;
            $member->total = $product->group_price * $request->quantity;
            $member->member_type = $group->admin_id == auth()->user()->id ? 'admin' : 'member';
            $member->save();

            if(count($group->members) >=5){
                $group->members_completed = 'yes';
                $group->save();
            }

            return $member;
    }

    public function createGroupOrder($group, $request)
    {

            $product = Product::where('id', $group->product_id)->first();

            $order = new Order();
            $order->user_id = auth()->user()->id;
            $order->order_type = "group";
            $order->total = $product->group_price * $request->quantity;
            $order->payment_id = $request->payment_id;
            $order->payment_status = 'paid';
            $order->group_id = $group->id;
            $order->save();


            if($order){
            $orderItem = new OrderItem();
            $orderItem->order_id = $order->id;
            $orderItem->product_id = $group->product_id;
            $orderItem->quantity = $request->quantity;
            $orderItem->amount = $product->group_price * $request->quantity;
            $orderItem->save();
            }

             if(isset($request->shipment) && $order){
                $this->orderService->addShipmentDetails($request->shipment, $order);
            }

            return $order;
    }


    public function joinGroup($request, $groupId)
    {
            $group = Group::where('id', $groupId)->where([
                ['status','active'],
                ['members_completed','no'],
            ])->with(['product', 'members'])->first();

            if($request && $group){
                $this->addMembersToGroup($group, $request);
                $this->createGroupOrder($group, $request);
            }

            return $group;
    }

   
    public function myGroups()
    {
        

          $my_groups = Group::join('group_members', 'groups.id','=','group_members.group_id')
          ->where('group_members.member_id', auth()->user()->id)
          ->select('groups.id','groups.group_name','groups.product_id','groups.start_date','groups.end_date','groups.admin_id','groups.status','groups.members_completed')
          ->with(['product', 'members', 'orders'])->get();
          return $my_groups;
            
    }

     public function getMyGroup($groupId)
    {

          $my_group = Group::join('group_members', 'groups.id','=','group_members.group_id')
          ->where('group_members.member_id', auth()->user()->id)
          ->where('groups.id', $groupId)
          ->select('groups.id','groups.group_name','groups.product_id','groups.start_date','groups.end_date','groups.admin_id','groups.status','groups.members_completed')
          ->with(['product', 'members', 'orders'])->first();

          return $my_group;
            
    }

    public function listAllActiveGroups()
    {
            return Group::where([
                ['members_completed', 'no'],
                ['status', 'active']
            ])->with(['product', 'members', 'admin'])->get();
    }

     public function showActiveGroup($groupId)
    {
            return Group::where('id', $groupId)->where([
                ['members_completed', 'no'],
                ['status', 'active']
            ])->with(['product', 'members', 'admin'])->first();
    }

    public function listGroups()
    {
            return Group::with(['admin','product', 'members', 'orders'])->orderBy('id','desc')->get();
    }

     public function showGroup($groupId)
    {
            return Group::where('id', $groupId)->with(['admin','product', 'members', 'orders'])->first();
    }

     public function shareGroup($groupId, $referenceId)
    {
            return Group::where([
                ['members_completed', 'no'],
                ['status', 'active'],
                ['id', $groupId],
                ['reference_id', $referenceId],
            ])->with(['product', 'members'])->first();
    }

 public function updateGroupStatus()
    {
            $groups = Group::where([
                ['status', 'active'],
                ['end_date', '<=', Carbon::now()->timezone('Africa/Lagos')],
            ])->get();

            if(count($groups) >= 1){
                foreach ($groups as $group) {
                  $group->status = 'expired';
                  $group->save();
                }
            }

            return 'Done';
    }
  
}
