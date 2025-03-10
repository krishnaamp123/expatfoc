<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\City;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use App\Http\Resources\Admin\UserAdminResource;

class UserAdminController extends Controller
{
    public function getUser()
    {
        $users = User::with('city')
            ->when(Auth::user()->role !== 'super_admin', function ($query) {
                $query->where('role', '!=', 'super_admin');
            })
            ->get();

        return UserAdminResource::collection($users);
    }

    public function storeUser(Request $request)
    {

        if (Auth::user()->role !== 'super_admin' && $request->role === 'super_admin') {
            return response()->json([
                'error' => 'You are not authorized to create this type of user.'
            ], 403);
        }

        $validated = $request->validate([
            'id_city' => 'required|exists:cities,id',
            'employee_id' => 'nullable|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'fullname' => 'required|string|max:255',
            'nickname' => 'required|string|max:100',
            'phone' => 'required|string|max:15',
            'address' => 'required|string|max:500',
            'birth_date' => 'required',
            'gender' => 'required|in:male,female',
            'role' => 'required|in:super_admin,human_resource,outlet,user_admin,partner,manager,supervisor,employee',
            'file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = new User();

        if ($request->hasFile('file')) {
            $filename = $this->generateRandomString();
            $extension = $request->file('file')->getClientOriginalExtension();
            $profilePictName = $filename . '.' . $extension;

            $destinationPath = public_path('storage/images');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $request->file('file')->move($destinationPath, $profilePictName);

            $user->profile_pict = 'storage/images/' . $profilePictName;
        }

        $user->id_city = $validated['id_city'];
        $user->employee_id = $validated['employee_id'];
        $user->email = $validated['email'];
        $user->password = Hash::make($validated['password']);
        $user->fullname = $validated['fullname'];
        $user->nickname = $validated['nickname'];
        $user->phone = $validated['phone'];
        $user->address = $validated['address'];
        $user->birth_date = $validated['birth_date'];
        $user->gender = $validated['gender'];
        $user->role = $validated['role'];

        // Simpan user baru ke database
        $user->save();

        return new UserAdminResource($user);
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);

        if ((Auth::user()->role === 'recruiter' && $user->role === 'hiring_manager') ||
        (Auth::user()->role !== 'super_admin' && $user->role === 'super_admin')) {
        return redirect()->route('getUser')->with('error', 'You are not authorized to edit this user.');
    }

        $cities = City::all();
        return view('admin.user.update', compact('user', 'cities'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ((Auth::user()->role === 'recruiter' && ($user->role === 'hiring_manager' || $request->role === 'hiring_manager')) ||
            (Auth::user()->role !== 'super_admin' && $request->role === 'super_admin')) {
            return redirect()->route('getUser')->with('error', 'You are not authorized to update this user.');
        }


        $validated = $request->validate([
            'id_city' => 'required|exists:cities,id',
            'employee_id' => 'nullable|string',
            'fullname' => 'required|string|max:255',
            'nickname' => 'required|string|max:100',
            'phone' => 'required|string|max:15',
            'address' => 'required|string|max:500',
            'birth_date' => 'required',
            'gender' => 'required|in:male,female',
            'role' => 'required|in:super_admin,hiring_manager,recruiter,interviewer,applicant',
            'email_verified_at' => 'required',
            'file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Perbarui gambar jika ada
        if ($request->hasFile('file')) {
            // Generate nama file unik
            $filename = $this->generateRandomString();
            $extension = $request->file('file')->getClientOriginalExtension();
            $profilePictName = $filename . '.' . $extension;

            // Tentukan lokasi penyimpanan di public/storage/images
            $destinationPath = public_path('storage/images');

            // Buat folder jika belum ada
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            // Pindahkan file ke lokasi tujuan
            $request->file('file')->move($destinationPath, $profilePictName);

            // Hapus file lama jika ada
            if ($user->profile_pict) {
                $oldFilePath = public_path($user->profile_pict);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }

            // Simpan nama file ke database
            $user->profile_pict = 'storage/images/' . $profilePictName;
        }

        // Update hanya field yang divalidasi
        $user->id_city = $validated['id_city'];
        $user->employee_id = $validated['employee_id'];
        $user->fullname = $validated['fullname'];
        $user->nickname = $validated['nickname'];
        $user->phone = $validated['phone'];
        $user->address = $validated['address'];
        $user->birth_date = $validated['birth_date'];
        $user->gender = $validated['gender'];
        $user->role = $validated['role'];
        $user->email_verified_at = $validated['email_verified_at'];

        $user->save();

        return redirect()->route('getUser')->with('message', 'User Updated Successfully');
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);

        if ((Auth::user()->role === 'recruiter' && $user->role === 'hiring_manager') ||
            (Auth::user()->role !== 'super_admin' && $user->role === 'super_admin')) {
            return redirect()->route('getUser')->with('error', 'You are not authorized to delete this user.');
        }

        $user->delete();
        return redirect()->back()->with('message', 'User deleted successfully.');
    }

    // Helper method to generate a random string for the file name
    function generateRandomString($length = 30)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}
