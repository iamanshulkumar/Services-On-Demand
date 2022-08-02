<?php

namespace App\Http\Controllers\Api;

use App\Notifications\TicketNotificationSeller;
use App\Actions\Media\MediaHelper;
use App\Country;
use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use App\Mail\BasicMail;
use App\Order;
use App\ServiceArea;
use App\ServiceCity;
use App\SupportTicket;
use App\SupportTicketMessage;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Auth;
use App\PayoutRequest;
use App\AmountSettings;
use App\SellerVerify;
use App\Accountdeactive;
use App\Service;

class SellerController extends Controller
{
    public function dashboardInfo(){
        
        $total_earnings = 0;
        $seller_id = Auth::guard('sanctum')->user()->id;
        $pending_order = Order::where(['status'=>0,'seller_id'=>$seller_id])->count();
        $complete_order = Order::where(['status'=>2,'seller_id'=>$seller_id])->count();
        
        //
        $get_sum = Order::where(['status'=>2,'seller_id'=>$seller_id]);
        $complete_order_balance_with_tax = $get_sum->sum('total');
        $complete_order_tax = $get_sum->sum('tax');
        $complete_order_balance_without_tax = $complete_order_balance_with_tax - $complete_order_tax;
        $admin_commission_amount = $get_sum->sum('commission_amount');
        $remaning_balance = $complete_order_balance_without_tax-$admin_commission_amount;
        
        //
        
        $total_earnings = PayoutRequest::where('seller_id',$seller_id)->sum('amount');
        
        $remaning_balance -= $total_earnings;
        
        return response()->success([
            'pending_order' => $pending_order ?? null, 
            'completed_order' => $complete_order ?? null, 
            'total_withdrawn_money' => $total_earnings, 
            'remaining_balance' => $remaning_balance,
            'seller_id' => $seller_id
        ]);

    }
    
    public function recentOrders(Request $request){
        
        $total_earnings = 0;
        $seller_id = Auth::guard('sanctum')->user()->id;
        $item = 5;
        if($request->has('item')){
            $item = $request->item;
        }
        $recent_order = Order::select('id','name','status','email','total')->where('seller_id',$seller_id)->latest()->take($item)->get()->transform(function($info){
            $info->order_status = $this->orderStatusText($info->status);
            $info->total = number_format($info->total,2,'.','');
            return $info;
        });
        
        return response()->success([
            'recent_orders' => $recent_order ?? __('No order found')
        ]);

    }
    
    public function myOrders($id=null)
    {
        $uesr_info = auth('sanctum')->user()->id;
        $my_orders = Order::where('buyer_id',$uesr_info)->paginate(10)->through(function ($item) {
           $item->payment_status =  !empty($item->payment_status) ? $item->payment_status : 'pending';
            return $item;
       });
   
        return response()->success([
            'my_orders' => $my_orders,
            'user_id' => $uesr_info,
        ]);
    }
    
    public function singleOrder(Request $request){
        
        if(empty($request->id)){
            return response()->error(['message' => __('no order found')]);
        }
        
        $orderInfo = Order::where('id',$request->id)->first();
        $orderInfo->payment_status = !empty($orderInfo->payment_status) ? $orderInfo->payment_status : 'pending';
        $orderInfo->total = amount_with_currency_symbol($orderInfo->total);
        $orderInfo->tax = amount_with_currency_symbol($orderInfo->tax);
        $orderInfo->sub_total = amount_with_currency_symbol($orderInfo->sub_total);
        $orderInfo->extra_service = amount_with_currency_symbol($orderInfo->extra_service);
        $orderInfo->package_fee = amount_with_currency_symbol($orderInfo->package_fee);
        
        //append buyer infomation 
        $orderInfo->buyer_details = $orderInfo->buyer ?? null; 
        
        
        if(is_null($orderInfo)){
            return response()->success([
                'message'=>__('Order Not Found')
            ]);
        }
        
        return response()->success([
                'orderInfo'=> $orderInfo
        ]);
    }
    
    public function allTickets()
    {
        $all_tickets = SupportTicket::select('id','title','description','subject','priority','status')
        ->where('seller_id',auth('sanctum')->id())->orderBy('id','Desc')
        ->paginate(10)
        ->withQueryString();
        
        return response()->success([ 
            'seller_id'=> auth('sanctum')->id(),
            'tickets' =>$all_tickets,
        ]);
    }
    
    public function viewTickets(Request $request,$id= null)
    {
        $all_messages = SupportTicketMessage::where(['support_ticket_id'=>$id])->get()->transform(function($item){
            $item->attachment = !empty($item->attachment) ? asset('assets/uploads/ticket/'.$item->attachment) : null;
            return $item;
        });
        $q = $request->q ?? '';
        return response()->success([
            'ticket_id'=>$id,
            'all_messages' =>$all_messages,
            'q' =>$q,
        ]);
    }
    
    public function sendMessage(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required',
            'user_type' => 'required|string|max:191',
            'message' => 'required',
            'file' => 'nullable|mimes:jpg,png,jpeg,gif',
        ]);

        $ticket_info = SupportTicketMessage::create([
            'support_ticket_id' => $request->ticket_id,
            'type' => $request->user_type,
            'message' => $request->message,
        ]);
        
        if ($request->hasFile('file')){
            
            $uploaded_file = $request->file;
            $file_extension = $uploaded_file->extension();
            $file_name =  pathinfo($uploaded_file->getClientOriginalName(),PATHINFO_FILENAME).time().'.'.$file_extension;
            $uploaded_file->move('assets/uploads/ticket',$file_name);
            $ticket_info->attachment = $file_name;
            $ticket_info->save();
        }

        return response()->success([
            'message'=>__('Message Send Success'),
            'ticket_id'=>$request->ticket_id,
            'user_type' =>$request->user_type,
            'ticket_info' => $ticket_info,
        ]);
    } 
    
     public function paymentRequestDetails($id)
    {
        if(empty($id)){
            return response()->error([
                'message'=> __('id field is required'),
            ]);  
        }
        
        $payout_details = PayoutRequest::where('id',$id)->first();     
        $payout_details->payment_receipt = get_attachment_image_by_id($payout_details->payment_receipt) ?? null;     
        $payout_details->status = $this->payoutStatusText($payout_details->status);
        
        
         return response()->success([
            'payout_details'=> $payout_details,
        ]);  
    }
    
    public function createPaymentRequest(Request $request)
    {
        $request->validate([
                'amount' => 'required',
                'seller_note' => 'required',
                'payment_gateway' => 'required|string|max:191',
             ],[
                 'amount.required' => __('Amount required'),
                 'amount.numeric' => __('Amount must be numeric'),
                 'payment_gateway.required' =>  __('Payment Gateway required'),
             ]);

            $seller_id = Auth::guard('sanctum')->user()->id;

            $complete_order_balance_with_tax = Order::where(['status'=>2,'seller_id'=>$seller_id])->sum('total');
            $complete_order_tax = Order::where(['status'=>2,'seller_id'=>$seller_id])->sum('tax');
            $complete_order_balance_without_tax = $complete_order_balance_with_tax - $complete_order_tax;
            $admin_commission_amount = Order::where(['status'=>2,'seller_id'=>$seller_id])->sum('commission_amount');
            $remaning_balance = $complete_order_balance_without_tax-$admin_commission_amount;
            $total_earnings = PayoutRequest::where('seller_id',$seller_id)->sum('amount');

             $available_balance = $remaning_balance - $total_earnings;
             if($request->amount<=0 || $request->amount >$available_balance){
                return response()->error(['message' => __('Enter a valid amount')]); 
             }  
             
             $min_amount = AmountSettings::select('min_amount')->first();
             $max_amount = AmountSettings::select('max_amount')->first();
             if($request->amount < $min_amount->min_amount){
                 $msg = sprintf(__('Withdraw amount not less than %s'),float_amount_with_currency_symbol($min_amount->min_amount));
                 return response()->error(['message' => $msg]); 
             } 
             if($request->amount > $max_amount->max_amount){
                $msg = sprintf(__('Withdraw amount must less or equal to %s'),float_amount_with_currency_symbol($max_amount->max_amount));
                return response()->error(['message' => $msg]); 
            }

            $payout_info = PayoutRequest::create([
                'seller_id' => Auth::guard('sanctum')->user()->id,
                'amount' => $request->amount,
                'payment_gateway' => $request->payment_gateway,
                'seller_note' => $request->seller_note,
                'status' => 0,
            ]);

            $last_payout_request_id = DB::getPdo()->lastInsertId();
            try {
                $message_body = __('Hello,<br> admin new payout request is just created. Please check , thanks').'</br>'.'<span class="verify-code">'.__('Payout Request ID: ').$last_payout_request_id.'</span>';
                Mail::to(get_static_option('site_global_email'))->send(new BasicMail([
                    'subject' => __('New Payout Request'),
                    'message' => $message_body
                ]));
            } catch (\Exception $e) {
               
            }
       
        return response()->success([
            'message'=>__('Payout request success'),
            'payout_info'=>$payout_info
        ]);
    }
    
    
    public function profileVerify(Request $request){
        $user = Auth::guard('sanctum')->user()->id;
        
            $request->validate([
                'national_id' => 'required|mimes:jpg,jpeg,png|max:200000',
                'address' => 'nullable|mimes:jpg,jpeg,png|max:200000',
            ]);

            $old_image = SellerVerify::select('national_id','address')->where('seller_id',$user)->first();
          
            if($request->file('national_id')){
                MediaHelper::insert_media_image($request,'web','national_id');
                $national_image_id = DB::getPdo()->lastInsertId();
            }
            if($request->file('address')){
                MediaHelper::insert_media_image($request,'web','address');
                $address_image_id = DB::getPdo()->lastInsertId();
            }
            if(is_null($old_image)){
                SellerVerify::create([
                    'seller_id' => $user,
                    'national_id' => $national_image_id ?? optional($old_image)->national_id,
                    'address' => $address_image_id ?? optional($old_image)->address,
                ]);
            }else{
                SellerVerify::where('seller_id', $user)
                ->update([
                    'seller_id' => $user,
                    'national_id' => $national_image_id ?? optional($old_image)->national_id,
                    'address' => $address_image_id ?? optional($old_image)->address,
                ]);
            }

            try {
                $message_body = __('You have a new request for seller verification');
                Mail::to(get_static_option('site_global_email'))->send(new BasicMail([
                    'subject' => __('New Seller Verification Request'),
                    'message' => $message_body
                ]));
            } catch (\Exception $e) {
                //
            }
            
            return response()->success([
                'message'=>__('Verify Info Update Success---')
            ]);
    }
    
    public function profileInfo(){
        
        $user_id = auth('sanctum')->id();
        
        $user = User::with('country','city','area')->with('sellerVerify')
        ->select('id','name','email','phone','address','about','country_id','service_city','service_area','post_code','image','country_code')
        ->where('id',$user_id)->first();
        
        $profile_image =  get_attachment_image_by_id($user->image);

        return response()->success([
            'user_details' => $user,
            'profile_image' => $profile_image,
        ]);
    }
    public function profileDeactivate(Request $request){
        
        $request->validate([
                'reason' => 'required',
                'description' => 'required|max:150',
            ]);
            Accountdeactive::create([
                'user_id' => Auth::guard('sanctum')->user()->id,
                'reason' => $request['reason'],
                'description' => $request['description'],
                'status' => 0,
                'account_status' => 0,
            ]);
            
            Service::where('seller_id',Auth::guard('sanctum')->user()->id)->update(['status'=>0]);


        return response()->success([
            'message' => __('Your Account Successfully Deactive')
        ]);
    }
    
    public function profileEdit(Request $request)
    {
        $user = auth('sanctum')->user();
        $user_id = auth('sanctum')->user()->id;

        $request->validate([
            'name' => 'required|max:191',
            'email' => 'required|max:191|email|unique:users,email,'.$user_id,
            'phone' => 'required|max:191',
            'service_area' => 'required|max:191',
            'address' => 'required|max:191',
        ]);

        if($request->file('file')){
            MediaHelper::insert_media_image($request,'web');
            $last_image_id = DB::getPdo()->lastInsertId();
        }
        $old_image = User::select('image')->where('id',$user_id)->first();
        $user_update = User::where('id',$user_id)
            ->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'image' => $last_image_id ?? $old_image->image,
                'service_city' => $request->service_city ?? $user->service_city,
                'service_area' => $request->service_area ?? $user->service_area,
                'country_id' => $request->country_id ?? $user->country_id,
                'post_code' => $request->post_code,
                'country_code' => $request->country_code,
                'address' => $request->address,
                'about' => $request->about,
                'state' => $request->service_city,
            ]);

        if($user_update){
            return response()->success([
                'message' =>__('Profile Updated Success'),
            ]);
        }
    }
    
    public function paymentHistory($id=null)
    {
        $seller_id = auth('sanctum')->user()->id;
        $all_history = $all_payout_request = PayoutRequest::where('seller_id',$seller_id)->paginate(10);
        return response()->success([
            'payment_history' => $all_history
        ]);
    }
    
    private function orderStatusText($order_status_id)
    {
        $status_text = __('Pending');
        //0=pending, 1=active, 2=completed, 3=delivered, 4=cancelled
        
        switch($order_status_id){
            case(1):
                $status_text = __('Active');
                break;
            case(2):
                $status_text = __('Completed');
                break;
            case(3):
                $status_text = __('Delivered');
                break;
            case(4):
                $status_text = __('Cancelled');
                break;
            default: 
                break;
        }
        
        return $status_text;
    }
    
    private function payoutStatusText($order_status_id)
    {
        $status_text = __('Pending');
        //0=pending, 1=active, 2=completed, 3=delivered, 4=cancelled
        
        switch($order_status_id){
            case(1):
                $status_text = __('Completed');
                break;
            case(2):
                $status_text = __('Cancelled');
                break;
            default: 
                break;
        }
        
        return $status_text;
    }
    
}