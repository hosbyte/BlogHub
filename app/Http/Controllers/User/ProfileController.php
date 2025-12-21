<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $user = Auth::user();
        return view('user.profile.edit' , compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // اعتبارسنجی
        $validated = $request->validate([
            'name' => ['required' , 'string' , 'max:255'],
            'email' => ['required' , 'string' , 'email' , 'max:255' ,
                Rule::unique('users')->ignore($user->id)],
            'bio' => ['nullable' , 'string' , 'max:500'],
            'avatar' => ['nullable' , 'image' , 'mimes:jpeg,png,jpg,gif' , 'max:2048'],
            'current_password' => ['nullable' , 'required_with:new_password'],
            'new_password' => ['nullable' , 'min:8' , 'confirmed'],
        ]);

        // آپلود آواتار
        if($request->hasFile('avatar'))
        {
            // حذف آواتار قدیمی اگر وجود دارد
            if($user->avatar && Storage::exists($user->avatar))
            {
                Storage::delete($user->avatar);
            }

            $path = $request->file('avatar')->store('avatars' , 'public');
            $user->avatar = $path;
        }

        // بروزرسانی اطلاعات اصلی
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->bio = $validated['bio'] ?? null;

        // تغییر رمز عبور
        if($request->filled('current_password') && $request->filled('new_password'))
        {
            if(Hash::check($request->current_password , $user->password))
            {
                $user->password = Hash::make($request->new_password);
            }
            else
            {
                return back()->withErrors([
                    'current_password' => 'رمز عبور فعلی نادرست است.'
                ]);
            }
        }

        $user->save();
        
        return redirect()->route('user.profile.edit')->with('success', 'پروفایل با موفقیت بروزرسانی شد.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
