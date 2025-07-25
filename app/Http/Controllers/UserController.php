<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Jobs\CreateUserJob;
use App\Models\Balls;
use App\Models\Position;
use App\Models\User;
use App\Services\UserService;
use App\Traits\UserTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }


    use UserTrait;

    public function index()
    {
        $users = User::with('position')->get();
        $positions = Position::all();

        return view('Admin.users.index', [
            'users' => $users,
            'positions' => $positions,
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        $this->userService->userStore($data);
        return redirect()->back()->with(['user successful create']);
    }

    public function edit($id)
    {
        $user = $this->getUser($id);
        $positions = Position::all();
        return view('Admin.users.edit', [
            'user' => $user,
            'positions' => $positions,
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = $this->getUser($id);
        if (!$user) {
            return redirect()->back()->withErrors(['User not found']);
        }
        $rules = [
            'name' => 'sometimes|required|string',
            'position_id' => 'sometimes|required|exists:positions,id',
            'username' => 'sometimes|required|unique:users,username,' . $user->id,
            'password' => 'nullable|string|min:6',
            'phone' => 'sometimes|required|string',
            'passport' => 'sometimes|required|string',
            'jshshir' => 'sometimes|required|string|max:14|min:14',
        ];

        $validatedData = $request->validate($rules);

        foreach ($validatedData as $key => $value) {
            if ($key === 'password' && !empty($value)) {
                $user->password = Hash::make($value);
            } else if ($key !== 'password') {
                $user->$key = $value;
            }
        }
        $user->save();
        return redirect()->route('users')->with('success', 'User updated successfully.');
    }

    public function delete($id)
    {
        $user = $this->getUser($id);
        if (!$user) {
            return redirect()->back()->with(['user not found'], 404);
        }
        $user->delete();
        return redirect()->back()->with(['user successful delete'], 200);
    }
}
