<?php

namespace App\Modules\Core\Services;

use App\Modules\Core\Enums\UserStatusEnum;
use App\Modules\Core\Models\User;
use App\Modules\Core\Exports\UsersExport;
use App\Modules\Core\Imports\UsersImport;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class UserService
{
    public function stats(array $filters): array
    {
        $base = User::filter($filters);

        return [
            'total' => (clone $base)->count(),
            'active' => (clone $base)->where('status', UserStatusEnum::Active->value)->count(),
            'inactive' => (clone $base)->where('status', '!=', UserStatusEnum::Active->value)->count(),
        ];
    }

    public function index(array $filters, int $limit)
    {
        return User::filter($filters)->paginate($limit);
    }

    public function store(array $data): User
    {
        $data['password'] = Hash::make($data['password']);

        return User::create($data);
    }

    public function update(User $user, array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return $user;
    }

    public function destroy(User $user): void
    {
        $user->delete();
    }

    public function bulkDestroy(array $ids): void
    {
        User::destroy($ids);
    }

    public function bulkUpdateStatus(array $ids, string $status): void
    {
        User::whereIn('id', $ids)->update(['status' => $status]);
    }

    public function changeStatus(User $user, string $status): User
    {
        $user->update(['status' => $status]);

        return $user;
    }

    public function export(array $filters): BinaryFileResponse
    {
        return Excel::download(new UsersExport($filters), 'users.xlsx');
    }

    public function import($file): void
    {
        Excel::import(new UsersImport(), $file);
    }
}
