<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\JobType;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Drivers\Gd\Driver;

class AccountController extends Controller
{
    //This method will return the view of the registration page
    public function registration()
    {
        return view('Front.account.registration');
    }

    //This method will save user
    public function processRegistration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:5|same:confirm_password',
            'confirm_password' => 'required',
        ]);
        if ($validator->passes()) {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();

            session()->flash('success', 'You have registered successfully');

            return response()->json([
                'status' => true,
                'errors' => []
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    //This method will return the view of the login page
    public function login()
    {
        return view('Front.account.login');
    }

    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if ($validator->passes()) {

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                return redirect()->route('account.profile');
            } else {
                return redirect()->route('account.login')
                    ->with('error', 'Either email or password is incorrect');
            }
        } else {
            return redirect()->route('account.login')
                ->withErrors($validator)
                ->withInput($request->only('email'));
        }
    }

    //This method will return the view of the profile page
    public function profile()
    {
        $id = Auth::user()->id;
        $user = User::where('id', $id)->first();
        return view('Front.account.profile', [
            'user' => $user
        ]);
    }

    public function updateProfile(Request $request)
    {
        $id = Auth::user()->id;
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:5|max:20',
            'email' => 'required|email|unique:users,email,' . $id . ',id',
        ]);
        if ($validator->passes()) {
            $user = User::find($id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->designation = $request->designation;
            $user->phone = $request->mobile;
            $user->save();

            session()->flash('success', 'Profile Updated Successfully.');

            return response()->json([
                'status' => true,
                'errors' => []

            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()

            ]);
        }
    }

    public function updateProfilepic(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'image' => 'required|image'
        ]);
        $id = Auth::user()->id;
        if ($validator->passes()) {

            $image = $request->image;
            $ext = $image->getClientOriginalExtension();
            $imageName = $id . '-' . time() . '.' . $ext;
            $image->move(public_path('storage/profile_pic/'), $imageName);

            User::where('id', $id)->update(['image' => $imageName]);


            //Create a small thumbnail
            $sourcePath=public_path('storage/profile_pic/'.$imageName);
            $manager = new ImageManager(Driver::class);
            $image = $manager->read($sourcePath);

            // resize to 150x150 pixel
            $image->cover(150, 150);
            $image->toPng()->save(public_path('storage/profile_pic/thumb/'.$imageName));

            //Delete old file
            File::Delete(public_path('storage/profile_pic/thumb/'.Auth::user()->image));
            File::Delete(public_path('storage/profile_pic/'.Auth::user()->image));

            session()->flash('success', 'Profile Picture updated successfully.');

            return response()->json([
                'status' => true,
                'errors' => []
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function createJob(){

        $categories=Category::orderBy('name','ASC')->where('status',1)->get();
        $jobTypes=JobType::orderBy('name','ASC')->where('status',1)->get();

        return view('Front.account.job.create',[
            'categories' => $categories,
            'jobTypes' => $jobTypes,
            ]);
    }

    public function saveJob(Request $request){
        $validator = Validator::make($request->all(),[
            'title'=> 'required|min:5|max:200',
            'category'=> 'required',
            'jobType'=> 'required',
            'vacancy'=> 'required|integer',
            'location'=> 'required|max:50',
            'description'=> 'required',
            'company_name'=> 'required|min:3|max:75',
   
        ]);

        if($validator->passes()){
            
        }else{
            return response()->json([
                'status'=>false,
                'errors'=> $validator->errors()
            ]);
        }

    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('account.login');
    }
}
