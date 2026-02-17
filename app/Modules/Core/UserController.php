<?php

namespace App\Modules\Core;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterRequest;
use App\Modules\Core\Models\User;
use App\Modules\Core\Requests\StoreUserRequest;
use App\Modules\Core\Requests\UpdateUserRequest;
use App\Modules\Core\Requests\BulkDestroyUserRequest;
use App\Modules\Core\Requests\BulkUpdateStatusUserRequest;
use App\Modules\Core\Requests\ImportUserRequest;
use App\Modules\Core\Requests\ChangeStatusUserRequest;
use App\Modules\Core\Resources\UserResource;
use App\Modules\Core\Resources\UserCollection;
use App\Modules\Core\Exports\UsersExport;
use App\Modules\Core\Imports\UsersImport;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

/**
 * @group Core - User
 * Quản lý người dùng: stats, index, show, store, update, destroy, bulk delete, bulk status, change status, export, import.
 */
class UserController extends Controller
{
    public function stats(FilterRequest $request)
    {
        $base = User::filter($request->all());
        return response()->json([
            'total'    => (clone $base)->count(),
            'active'   => (clone $base)->where('status', 'active')->count(),
            'inactive' => (clone $base)->where('status', '!=', 'active')->count(),
        ]);
    }

    public function index(FilterRequest $request)
    {
        $users = User::filter($request->all())
            ->paginate($request->limit ?? 10);
        return new UserCollection($users);
    }

    public function show(User $user)
    {
        return new UserResource($user);
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        return (new UserResource($user))
            ->additional(['message' => 'Tài khoản đã được tạo thành công!']);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        $user->update($data);
        return new UserResource($user);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['message' => 'Tài khoản đã được xóa thành công!']);
    }

    public function bulkDestroy(BulkDestroyUserRequest $request)
    {
        User::destroy($request->ids);
        return response()->json(['message' => 'Đã xóa thành công các tài khoản được chọn!']);
    }

    public function bulkUpdateStatus(BulkUpdateStatusUserRequest $request)
    {
        User::whereIn('id', $request->ids)->update(['status' => $request->status]);
        return response()->json(['message' => 'Cập nhật trạng thái thành công']);
    }

    public function export(FilterRequest $request)
    {
        return Excel::download(new UsersExport($request->all()), 'users.xlsx');
    }

    public function import(ImportUserRequest $request)
    {
        Excel::import(new UsersImport, $request->file('file'));
        return response()->json(['message' => 'Users imported successfully.']);
    }

    public function changeStatus(ChangeStatusUserRequest $request, User $user)
    {
        $user->update(['status' => $request->status]);
        return response()->json([
            'message' => 'Cập nhật trạng thái thành công!',
            'data'    => new UserResource($user),
        ]);
    }
}
