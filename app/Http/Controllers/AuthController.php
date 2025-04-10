<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Mail\WelcomeUserMail;
use Illuminate\Support\Facades\Mail; 
use App\Repositories\UserRepositoryInterface;

class AuthController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    // Register user
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // $user = User::create([
        //     'name' => $request->name,
        //     'email' => $request->email,
        //     'password' => Hash::make($request->password),
        // ]);
        $user = $this->userRepository->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
      //  maniually send job
        Mail::to($user->email)->send(new WelcomeUserMail($user)); 
       //SendWelcomeEmailJob::dispatch($user);


        return response()->json(['message' => 'User registered successfully'], 201);
    }

    // Login user and issue token
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Generate API token
        $token = $user->createToken('MyApp')->plainTextToken;

        return response()->json(['token' => $token], 200);
    }

    public function getUser($id)
    {
        $user = $this->userRepository->find($id);

        if ($user) {
            return response()->json(['data' => $user]);
        }

        return response()->json(['message' => 'User not found'], 404);
    }

    public function getUsers()
    {
        $user = $this->userRepository->all();

        if ($user) {
            return response()->json(['data' => $user]);
        }

        return response()->json(['message' => 'User not found'], 404);
    }

    public function updateUser(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:8|confirmed',
        ]);

        $updated = $this->userRepository->update($id, $validated);

        if ($updated) {
            return response()->json(['message' => 'User updated successfully']);
        }

        return response()->json(['message' => 'User not found'], 404);
    }

    public function deleteUser($id)
    {
        $deleted = $this->userRepository->delete($id);

        if ($deleted) {
            return response()->json(['message' => 'User deleted successfully']);
        }

        return response()->json(['message' => 'User not found'], 404);
    }

}

