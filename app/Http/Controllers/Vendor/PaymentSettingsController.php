<?php
namespace App\Http\Controllers\Vendor;
use App\Http\Controllers\Controller;
use App\Models\VendorPaymentMethod;
use Illuminate\Http\Request;
class PaymentSettingsController extends Controller {
 public function index(){ return view('vendor.payment-settings',['methods'=>auth()->user()->paymentMethods()->orderBy('provider')->get()]); }
 public function save(Request $request){
  $data=$request->validate(['airtel_name'=>'required|string|max:150','airtel_number'=>'required|string|max:30','moov_name'=>'required|string|max:150','moov_number'=>'required|string|max:30']);
  foreach([['airtel_money',$data['airtel_name'],$data['airtel_number']],['moov_money',$data['moov_name'],$data['moov_number']]] as [$provider,$name,$number]) VendorPaymentMethod::updateOrCreate(['vendor_id'=>auth()->id(),'provider'=>$provider],['account_name'=>$name,'transfer_number'=>$number,'is_active'=>true]);
  return back()->with('success','Vos numéros de transfert ont été enregistrés.');
 }
}
