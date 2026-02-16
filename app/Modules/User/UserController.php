<?php

namespace App\Modules\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterRequest;
use App\Modules\User\Models\User;
use App\Modules\User\Requests\StoreUserRequest;
use App\Modules\User\Requests\UpdateUserRequest;
use App\Modules\User\Requests\BulkDestroyUserRequest;
use App\Modules\User\Requests\BulkUpdateStatusUserRequest;
use App\Modules\User\Resources\UserResource;
use App\Modules\User\Resources\UserCollection;
use App\Modules\User\Exports\UsersExport;
use App\Modules\User\Imports\UsersImport;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Modules\User\Requests\ImportUserRequest;
use App\Modules\User\Requests\ChangeStatusUserRequest;

/**
 * @group User
 *
 * Quản lý người dùng: danh sách, chi tiết, tạo, cập nhật, xóa, thao tác hàng loạt
 */
class UserController extends Controller
{
    /**
     * Thống kê cơ bản
     *
     * Tổng số bản ghi, bản ghi đang kích hoạt (active), bản ghi không kích hoạt (inactive, banned).
     * Áp dụng cùng bộ lọc với index (search, status, ...).
     */
    public function stats(FilterRequest $request)
    {
        $base = User::filter($request->all());

        return response()->json([
            'total'    => (clone $base)->count(),
            'active'   => (clone $base)->where('status', 'active')->count(),
            'inactive' => (clone $base)->where('status', '!=', 'active')->count(),
        ]);
    }

    /**
     * Danh sách người dùng
     *
     * Lấy danh sách có phân trang, lọc và sắp xếp.
     *
     * @queryParam search string Từ khóa tìm kiếm (name, email). Example: john
     * @queryParam status string Lọc theo trạng thái: active, inactive, banned.
     * @queryParam sort_by string Sắp xếp theo: id, title, name, created_at. Example: created_at
     * @queryParam sort_order string Thứ tự: asc, desc. Example: desc
     * @queryParam limit integer Số bản ghi mỗi trang (1-100). Example: 10
     */
    public function index(FilterRequest $request)
    {
        $users = User::filter($request->all())
            ->paginate($request->limit ?? 10);
        return new UserCollection($users);
    }

    /**
     * Chi tiết người dùng
     *
     * @urlParam user integer required ID người dùng. Example: 1
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    /**
     * Tạo người dùng mới
     *
     * @bodyParam name string required Tên người dùng. Example: Nguyễn Văn A
     * @bodyParam email string required Email (duy nhất). Example: user@example.com
     * @bodyParam password string required Mật khẩu (tối thiểu 6 ký tự). Example: password123
     * @bodyParam password_confirmation string required Xác nhận mật khẩu.
     * @bodyParam status string Trạng thái: active, inactive, banned. Example: active
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        return (new UserResource($user))
            ->additional(['message' => 'Tài khoản đã được tạo thành công!']);
    }

    /**
     * Cập nhật người dùng
     *
     * @urlParam user integer required ID người dùng. Example: 1
     * @bodyParam name string Tên người dùng.
     * @bodyParam email string Email (duy nhất).
     * @bodyParam password string Mật khẩu mới (nếu muốn đổi).
     * @bodyParam password_confirmation string Xác nhận mật khẩu.
     * @bodyParam status string Trạng thái: active, inactive, banned.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        $user->update($data);
        return new UserResource($user);
    }

    /**
     * Xóa người dùng
     *
     * @urlParam user integer required ID người dùng. Example: 1
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['message' => 'Tài khoản đã được xóa thành công!']);
    }

    /**
     * Xóa hàng loạt người dùng
     *
     * @bodyParam ids array required Danh sách ID. Example: [1, 2, 3]
     */
    public function bulkDestroy(BulkDestroyUserRequest $request)
    {
        User::destroy($request->ids);
        return response()->json(['message' => 'Đã xóa thành công các tài khoản được chọn!']);
    }

    /**
     * Cập nhật trạng thái hàng loạt
     *
     * @bodyParam ids array required Danh sách ID. Example: [1, 2, 3]
     * @bodyParam status string required Trạng thái: active, inactive, banned. Example: active
     */
    public function bulkUpdateStatus(BulkUpdateStatusUserRequest $request)
    {
        User::whereIn('id', $request->ids)->update(['status' => $request->status]);
        return response()->json(['message' => 'Cập nhật trạng thái thành công']);
    }

    /**
     * Xuất danh sách người dùng
     *
     * Áp dụng cùng bộ lọc với index (search, status, ...).
     *
     * @authenticated
     */
    public function export(FilterRequest $request)
    {
        return Excel::download(new UsersExport($request->all()), 'users.xlsx');
    }

    /**
     * Nhập danh sách người dùng
     *
     * @authenticated
     * @bodyParam file file required File excel (xlsx, xls, csv).
     * @response 200 {"message": "Users imported successfully."}
     */
    public function import(ImportUserRequest $request)
    {
        Excel::import(new UsersImport, $request->file('file'));

        return response()->json(['message' => 'Users imported successfully.']);
    }

    /**
     * Thay đổi trạng thái người dùng
     *
     * @urlParam user integer required ID người dùng. Example: 1
     * @bodyParam status string required Trạng thái mới: active, inactive, banned. Example: active
     */
    public function changeStatus(ChangeStatusUserRequest $request, User $user)
    {
        $user->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Cập nhật trạng thái thành công!',
            'data' => new UserResource($user)
        ]);
    }
}
