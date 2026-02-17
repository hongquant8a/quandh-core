<?php

namespace App\Modules\Core\Imports;

use App\Modules\Core\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $password = $row['password'] ?? 'password';
        return new User([
            'name'     => $row['name'] ?? $row['name_'] ?? '',
            'email'    => $row['email'] ?? '',
            'password' => Hash::make($password),
            'status'   => $row['status'] ?? 'active',
        ]);
    }
}
