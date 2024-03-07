<?php

namespace App\Http\Controllers;

use DB;
use View;
use Config;
use Session;
use App\Blog;
use App\User;
use App\Brand;
use App\Sales;
use App\Orders;
use App\Vendor;
use App\Country;
use App\Category;
use App\PageMeta;
use App\Products;
use App\Supplier;
use App\Attribute;
use App\Subscribe;
use App\Membership;
use App\W2bProduct;
use App\WbWishlist;
use App\VendorRoles;
use App\VendorStore;
use App\W2bCategory;
use App\SupplierRole;
use App\EmailTemplate;

use App\StoreSubscription;
use App\Mail\VendorSuccess;
use Illuminate\Http\Request;
use App\Mail\SupplierSuccess;
use App\Mail\VendorSignupMail;
use App\Mail\SupplierSignupMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CommonController extends Controller
{
    public function __construct()
	{

		$page_meta = PageMeta::pluck('meta_value', 'meta_key')->all();
		View::share('page_meta', $page_meta);

        $wb_wishlist = null;
        if (Auth::guard('w2bcustomer')->user()) {
            $wb_wishlist = WbWishlist::where('user_id', Auth::guard('w2bcustomer')->user()->id)
            ->get();
        }
        View::share('wb_wishlist', $wb_wishlist);

        $categories2 = W2bCategory::whereIn('id', [1, 6, 9,12,20,23])->get();
        $categories = W2bCategory::with('childrens')->get();
        View::share('categories', $categories);
        View::share('categories2', $categories2);

        $categories_new = W2bCategory::where('parent_id', 0)->take(12)->get();
        View::share('categories_new', $categories_new);


        $latest_blogs2 = Blog::latest()->take(3)->get();
        View::share('latest_blogs2', $latest_blogs2);


	}

	protected function getStoreCustomers($sid)
	{
		$order = Orders::selectRaw('DISTINCT customer_id')->where('store_id',$sid)->get()->pluck('customer_id')->toArray();
		$users = User::whereIn('id',$order)->get();
		$store_subscription = StoreSubscription::with('membership')->where('store_id', $sid)->first();
		echo json_encode(['store_subscription' => $store_subscription , 'users' => $users]);
	}

	protected function getStoresByVendorID($vid)
	{
		$stores = VendorStore::select('id','name')->where('vendor_id',$vid)->where('status', 'enable')->get();
		echo json_encode($stores);
	}

	protected function getBrandsByStoreID($sid)
	{
		$brands = Brand::select('id','name')->where('store_id',$sid)->where('status', 'enable')->get();
		echo json_encode($brands);
	}

	protected function getAttributeByStoreID($sid)
	{
		$attribute = Attribute::select('id','name')->where('store_id',$sid)->get();
		echo json_encode($attribute);
	}

	protected function getCategoriesByStoreID($sid)
	{
		$categories = Category::select('id','name')->where('store_id',$sid)->where('status', 'enable')->get();
		echo json_encode($categories);
	}

	protected function getCategoriesDropDownByStoreID($sid)
	{
		$result = [];
		$categories = Category::select('id','name','parent')->where('store_id',$sid)->where('status', 'enable')->get();
 		if($categories->isNotEmpty()){
			$ref   = [];
			$items = [];
			foreach ($categories as $key => $value) {

				$thisRef = &$ref[$value->id];

				$thisRef['id'] = $value->id;
				$thisRef['name'] = $value->name;
				$thisRef['parent'] = $value->parent;

				if($value->parent == 0) {
					$items[$value->id] = &$thisRef;
				} else {
					$ref[$value->parent]['child'][$value->id] = &$thisRef;
				}
			}
			$result = $this->getCategoriesDropDown('', $items);
		}
		return response()->json(['categories'=>$result]);
	}

	protected function getCategoriesDropDown($prefix, $items)
	{
		$str = '';
		$span = '<span>—</span>';
		foreach($items as $key=>$value) {
			$str .= '<option value="'.$value['id'].'">'.$prefix.$value['name'].'</option>';
			if(array_key_exists('child',$value)) {
				$str .= $this->getCategoriesDropDown($prefix.$span, $value['child'],'child');
			}

		}
		return $str;
	}

	protected function getCategoriesHierarchyByStoreID($sid)
	{
		$result = [];
		$categories = Category::select('id','name','parent')->where('store_id',$sid)->where('status', 'enable')->get();
 		if($categories->isNotEmpty()){
			$ref   = [];
			$items = [];
			foreach ($categories as $key => $value) {

				$thisRef = &$ref[$value->id];

				$thisRef['id'] = $value->id;
				$thisRef['name'] = $value->name;
				$thisRef['parent'] = $value->parent;

				if($value->parent == 0) {
					$items[$value->id] = &$thisRef;
				} else {
					$ref[$value->parent]['child'][$value->id] = &$thisRef;
				}
			}
			$result = $this->getCategoriesHierarchy($items);
		}
		return response()->json(['categories'=>$result]);

		//$categories = $this->getCategoriesHierarchy($sid);
		//return response()->json(['categories'=>$categories]);
	}

	protected function getCategoriesHierarchy($items)
	{
		$str = '<ul class="category_heirarchy">';
		foreach($items as $key=>$value) {
			$str .= '<li>';
				$str .= '<div class="icheck-material-primary">';
                    $str .= '<input type="checkbox" name="category[]" id="category_'.$value['id'].'" value="'.$value['id'].'" class="checkbox">';
                    $str .= '<label for="category_'.$value['id'].'">'.$value['name'].'</label>';
                $str .= '</div>';
				/*$str .= '<label>';
					$str .= '<input type="checkbox" name="" value="'.$value['id'].'"> '.$value['name'];
				$str .= '</label>';*/
				if(array_key_exists('child',$value)) {
					$str .= $this->getCategoriesHierarchy($value['child'],'child');
				}
			$str .= '</li>';
		}
		$str .= '</ul>';
		return $str;
	}

	public function getProductByStore($sid)
	{
		$products = Products::where('store_id',$sid)->get();
		$str = '';
		foreach ($products as $key => $value) {
			# code...
			$str .= '<option value="'.$value['id'].'">'.$value['title'].'</option>';
		}
			return response()->json(['products'=>$str]);
		// echo json_encode($str);
	}

	public function getState($id){
		$states = DB::table("states")->where('country_id',$id)->get();
		echo json_encode($states);
	}

	public function getCity($id){
		$cities = DB::table("cities")->where('state_id',$id)->get();
		echo json_encode($cities);
	}

	public function vendorSignup(Request $request)
	{
        // dd($request->all());
		if(empty($request->all())){
                $wb_wishlist = null;


			$memberships = Membership::where('type', 'vendor')->get();
			$countries = Country::all();
			return view('vendor_signup',compact('memberships','countries'));
		}else{

			$request->validate([
				// 'sales_person_name'=>'required',
				// 'sales_person_mobile_number'=>'required',
				'g-recaptcha-response' => 'required|captcha',
				'office_number'=>'nullable|regex:/^([0-9\s\-\+\(\)]*)$/',
				'mobile_number' =>'required|regex:/^([0-9\s\-\+\(\)]*)$/',
				'email'=>'required|email|unique:vendors',
				'password' => 'required|min:6',
    			'confirm_password' => 'required|min:6|same:password',
				'name'=>'required',
				'image' => 'required|mimes:jpeg,png,jpg|max:2048',
				'business_name' => 'required',
				'tax_id' => 'required',
				'terms_condition' =>'accepted',
				'pincode' =>'nullable|digits:5'
			],[
        		'email.unique' => 'The email has already been taken by you or some other vendor.',
        		'pincode.digits' => 'The zip code must be 5 digits.',
        		'g-recaptcha-response.required' => "Google captcha is required.",
        	]);

			$email = $request->input('email');
			$password = $request->input('password');
			$date = date('Y-m-d');
			$vendor_name = $request->input('name');

			if($request->has('sales_person_mobile_number') && $request->get('sales_person_mobile_number') != '') {
				$sales = Sales::updateOrCreate(
					['mobile' => $request->sales_person_mobile_number],
					['name' => $request->sales_person_name]
				);
				$sales_id = $sales->id;
			} else {
				$sales_id = NULL;
			}

			$data = array(
					'sales_id' => $sales_id,
					'registered_date'=>$date,
					// 'expired_date'=> $expiry_date,
					'address' => $request->input('address'),
					'country' => $request->input('country'),
					'state' => $request->input('state'),
					'city' => $request->input('city'),
					'pincode' => $request->input('pincode'),
					'name' => $request->input('name'),
					'phone_number'=> $request->input('office_number'),
					'mobile_number' => $request->input('mobile_number'),
					'email' => $request->input('email'),
					'password' => bcrypt($request->input('password')),
					'website_link'=> $request->input('website_link'),
					'status'    => 'active',
					// 'admin_commision'    => $request->input('admin_commision'),
					'business_name' => $request->input('business_name'),
					'tax_id' => $request->input('tax_id'),
					'verification' => 'yes',
					'is_approved' => 1
				);
			//print_r($data);die();
			if ($files = $request->file('image')){

				$path = 'public/images/vendors';
				$profileImage = date('YmdHis') . "." . $files->getClientOriginalExtension();
				$files->move($path, $profileImage);
				$data['image'] = $profileImage;
			}

			$vendor = Vendor::create($data);

			foreach (main_vendor_roles() as $key => $value) {
				$vendor_roles = new VendorRoles;
				$vendor_roles->vendor_id = $vendor->id;
				$vendor_roles->role_name = $value;
				$vendor_roles->slug = $key;
				$vendor_roles->status = 'active';
				$vendor_roles->save();
			}

			//get email Template
			 $mailTemp = EmailTemplate::where('template','supplier-signup');

			Mail::to($email)->send(new VendorSuccess($email,$password,$vendor_name,$mailTemp));

			$country_name = DB::table('countries')->where('id',$request->input('country'))->first();

			if(empty($country_name)){
				$country = NULL;
			}else{
				$country = $country_name->name;
			}
			$state_name = DB::table('states')->where('id',$request->input('state'))->first();
			if(empty($state_name)){
				$state = NULL;
			}else{
				$state = $state_name->name;
			}
			$city_name = DB::table('cities')->where('id',$request->input('city'))->first();
			if(empty($city_name)){
				$city = NULL;
			}else{
				$city = $city_name->name;
			}
			// $admin_email = 'ankita@addonwebsolutions.com';
			$admin_email = Config::get('app.admin_email');
			$name = $request->input('name');
			$id = $vendor->id;
			$address = $request->input('address');

			$pincode = $request->input('pincode');
			$phone_number = $request->input('phone_number');
			$mobile_number  = $request->input('mobile_number');
			Mail::to($admin_email)->send(new VendorSignupMail($email,$name,$id,$address,$country,$state,$city,$pincode,$phone_number,$mobile_number));
			// return view('thankyou');
			return redirect('/thank-you')->with('success',"Vendor has been successfully registered.");
		}
	}

	public function supplierSignup(Request $request)
	{
		if(empty($request->all())){
			$memberships = Membership::where('type', 'supplier')->get();
			$countries = Country::all();

			return view('supplier_signup',compact('memberships','countries'));
		}else{

			$request->validate([
				// 'sales_person_name'=>'required',
				// 'sales_person_mobile_number'=>'required',
				'g-recaptcha-response' => 'required|captcha',
				'office_number'=>'nullable|regex:/^([0-9\s\-\+\(\)]*)$/',
				'mobile_number' =>'required|regex:/^([0-9\s\-\+\(\)]*)$/',
				'email'=>'required|email|unique:vendors',
				'password' => 'required|min:6',
    			'confirm_password' => 'required|min:6|same:password',
				'name'=>'required',
				'image' => 'required|mimes:jpeg,png,jpg|max:2048',
				'business_name' => 'required',
				'fullfill_type' => 'required',
				'tax_id' => 'required',
				'terms_condition' =>'accepted',
				'pincode' =>'nullable|digits:5'
			] ,[
        		'email.unique' => 'The email has already been taken by you or some other vendor.',
        		'pincode.digits' => 'The zip code must be 5 digits.',
        		'g-recaptcha-response.required' => "Google captcha is required.",
        	]);

			$email = $request->input('email');
			$password = $request->input('password');
			$date = date('Y-m-d');
			$vendor_name = $request->input('name');

			if($request->has('sales_person_mobile_number') && $request->get('sales_person_mobile_number') != '') {
				$sales = Sales::updateOrCreate(
					['mobile' => $request->sales_person_mobile_number],
					['name' => $request->sales_person_name]
				);
				$sales_id = $sales->id;
			} else {
				$sales_id = NULL;
			}

			$data = array(
					'sales_id' => $sales_id,
					'registered_date'=>$date,
					// 'expired_date'=> $expiry_date,
					'address' => $request->input('address'),
					'country' => $request->input('country'),
					'state' => $request->input('state'),
					'city' => $request->input('city'),
					'pincode' => $request->input('pincode'),
					'name' => $request->input('name'),
					'phone_number'=> $request->input('office_number'),
					'mobile_number' => $request->input('mobile_number'),
					'email' => $request->input('email'),
					'password' => bcrypt($request->input('password')),
					'website_link'=> $request->input('website_link'),
					'status'    => 'active',
					// 'admin_commision'    => $request->input('admin_commision'),
					'business_name' => $request->input('business_name'),
					'tax_id' => $request->input('tax_id'),
					'verification' => 'yes',
                    'seller_type' => 'supplier',
					'is_approved' => 1
				);

			if ($files = $request->file('image')){

				$path = 'public/images/suppliers';
				$profileImage = date('YmdHis') . "." . $files->getClientOriginalExtension();
				$files->move($path, $profileImage);
				$data['image'] = $profileImage;
			}
			// $data['fullfill_type'] = "seller_fullfill";

			$vendor = Vendor::create($data);

			foreach (main_supplier_roles() as $key => $value) {
				$vendor_roles = new VendorRoles();
				$vendor_roles->vendor_id = $vendor->id;
				$vendor_roles->role_name = $value;
				$vendor_roles->slug = $key;
				$vendor_roles->status = 'active';
				$vendor_roles->save();
			}

            $mailTemp = EmailTemplate::where('template','supplier-signup');

			Mail::to($email)->send(new SupplierSuccess($email,$password,$vendor_name,$mailTemp));

			$country_name = DB::table('countries')->where('id',$request->input('country'))->first();

			if(empty($country_name)){
				$country = NULL;
			}else{
				$country = $country_name->name;
			}
			$state_name = DB::table('states')->where('id',$request->input('state'))->first();
			if(empty($state_name)){
				$state = NULL;
			}else{
				$state = $state_name->name;
			}
			$city_name = DB::table('cities')->where('id',$request->input('city'))->first();
			if(empty($city_name)){
				$city = NULL;
			}else{
				$city = $city_name->name;
			}
			// $admin_email = 'ankita@addonwebsolutions.com';
			// $admin_email = 'ankita@addonwebsolutions.com';
			$admin_email = Config::get('app.admin_email');
			$name = $request->input('name');
			$id = $vendor->id;
			$address = $request->input('address');

			$pincode = $request->input('pincode');
			$phone_number = $request->input('phone_number');
			$mobile_number  = $request->input('mobile_number');
            // if ($admin_email) {
            //     $data = [
            //         'email' => $email,
            //         'name' => $request->input('name'),
            //         'id' => $vendor->id,
            //         'address' => $request->input('address'),
            //         'country' => $country,
            //         'state' => $state,
            //         'city' => $city,
            //         'pincode' => $request->input('pincode'),
            //         'phone_number' => $request->input('phone_number'),
            //         'mobile_number' => $request->input('mobile_number'),
            //     ];
            // }
            Mail::to($admin_email)->send(new SupplierSignupMail($email,$name,$id,$address,$country,$state,$city,$pincode,$phone_number,$mobile_number));

			// return view('thankyou');
			return redirect('/thank-you')->with('success',"Supplier has been successfully registered.");
		}
	}

	public function thankYou()
	{
		return view('thankyou');
	}

	public function subscribeNewsletter(Request $request,$email)
	{
		$subscribe = Subscribe::where('email',$email)->exists();
		if($subscribe){
			echo json_encode(array('error'=>'You have already subscribed.'));
		}else{
			$subscribe = new Subscribe;
			$subscribe->email = $email;
			$subscribe->status = 'subscribed';
			$subscribe->save();

			echo json_encode(array('success'=>'You have subscribed newsletter.'));
		}
	}

	public function ajaxCheck()
    {

    	$maxIdleBeforeLogout = 10 * 1;

        $idleTime = date('U') - Session::get('userLastActivity');

        $lastactivity_time = Session::get('userLastActivity');

		$max = 60;

        if ((Session::has('lastActive') && $max < (time() - Session::get('lastActive')))) {

            return response()->json([
				'logout' => 'yes',
				'currnt-time' =>time(),
				'last-activity-time' => Session::get('lastActive')
			]);


        }else{
        	return response()->json([
				'logout' => 'no',
				'currnt-time' => time(),
				'last-activity-time' => Session::get('lastActive')
			]);
        }
    }
}
