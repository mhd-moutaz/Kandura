<?php

namespace App\Http\Services\Admins;

use App\Models\User;

class UserService
{
    public function index(array $filters){
        $users = User::where('role', 'user')->filter($filters)->paginate(5)->withQueryString();
        return $users;
    }
    public function update($data, $user){
        $updateData = [
            'is_active' => $data['is_active'],
        ];
        $user->update($updateData);
        return $user;
    }
}
