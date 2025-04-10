<?php
namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository implements UserRepositoryInterface
{
    public function all(): Collection
    {
        return User::all(); // Get all users
    }

    public function find(int $id): ?User
    {
        return User::find($id); // Find a user by ID
    }

    public function create(array $data): User
    {
        return User::create($data); // Create a new user
    }

    public function update(int $id, array $data): bool
    {
        $user = User::find($id);
        if ($user) {
            return $user->update($data); // Update user details
        }

        return false;
    }

    public function delete(int $id): bool
    {
        $user = User::find($id);
        if ($user) {
            return $user->delete(); // Delete the user
        }

        return false;
    }
}
