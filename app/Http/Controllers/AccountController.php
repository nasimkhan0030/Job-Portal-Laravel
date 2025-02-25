<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
        $id= Auth::user()->id;
        $user = User::where('id',$id)->first();
        return view('Front.account.profile',[
            'user' => $user
        ]);
    }

    public function updateProfile(Request $request){
        $id= Auth::user()->id;
        $validator= Validator::make($request->all(),[
            'name' => 'required|min:5|max:20',
            'email' => 'required|email|unique:users,email,'.$id.',id',
        ]);
        if($validator->passes()){
            $user = User::find($id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->designation = $request->designation;
            $user->phone = $request->mobile;
            $user->save();

            session()->flash('success','Profile Updated Successfully.');

            return response()->json([
                'status'=>true,
                'errors'=>[]

            ]);
        }else{
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors()

            ]);
        }

    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('account.login');
    }
}
