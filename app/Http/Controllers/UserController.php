<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\FilterRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\BulkUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserCollection;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function index(FilterRequest $request) {
        $users = User::filter($request->all())
                ->paginate($request->limit ?? 10);
        return new UserCollection($users);
    }

    public function show(User $user) {
        return new UserResource($user);
    }
    
    public function store(StoreUserRequest $request) {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        return (new UserResource($user))
                ->additional(['message' => 'Tài khoản đã được tạo thành công!']);
    }

    public function update(UpdateUserRequest $request, User $user) {
        $data = $request->validated();
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        $user->update($data);
        return new UserResource($user);
    }

    public function destroy(User $user) {
        $user->delete();
        return response()->json(['message' => 'Bài viết đã được xóa thành công!']);
    }

    public function bulkDestroy(BulkUserRequest $request) {
        User::destroy($request->ids);
        return response()->json(['message' => 'Đã xóa thành công các tài khoản được chọn!']);
    }

    public function bulkUpdateStatus(BulkUserRequest $request) {
        User::whereIn('id', $request->ids)->update(['status' => $request->status]);
        return response()->json(['message' => 'Cập nhật trạng thái thành công']);
    }
}