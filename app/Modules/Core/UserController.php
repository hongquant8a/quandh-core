<?php

namespace App\Modules\Core;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterRequest;
use App\Modules\Core\Enums\UserStatusEnum;
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
 *
 * Quản lý người dùng: danh sách, chi tiết, tạo, cập nhật, xóa, thao tác hàng loạt, xuất/nhập Excel, đổi trạng thái.
 */
class UserController extends Controller
{
    /**
     * Thống kê người dùng
     *
     * Tổng số, đang kích hoạt (active), không kích hoạt (inactive, banned). Áp dụng cùng bộ lọc với index.
     *
     * @queryParam search string Từ khóa tìm kiếm (name, email). Example: john
     * @queryParam status string Lọc theo trạng thái: active, inactive, banned.
     * @queryParam sort_by string Sắp xếp theo: id, name, email, created_at. Example: created_at
     * @queryParam sort_order string Thứ tự: asc, desc. Example: desc
     * @queryParam limit integer Số bản ghi mỗi trang (1-100). Example: 10
     */
    public function stats(FilterRequest $request)
    {
        $base = User::filter($request->all());
        return response()->json([
            'total'    => (clone $base)->count(),
            'active'   => (clone $base)->where('status', UserStatusEnum::Active->value)->count(),
            'inactive' => (clone $base)->where('status', '!=', UserStatusEnum::Active->value)->count(),
        ]);
    }

    /**
     * Danh sách người dùng
     *
     * Lấy danh sách có phân trang, lọc và sắp xếp.
     *
     * @queryParam search string Từ khóa tìm kiếm (name, email). Example: john
     * @queryParam status string Lọc theo trạng thái: active, inactive, banned.
     * @queryParam sort_by string Sắp xếp theo: id, name, email, created_at. Example: created_at
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
     * Áp dụng cùng bộ lọc với index. Trả về file Excel.
     *
     * @queryParam search string Từ khóa tìm kiếm (name, email).
     * @queryParam status string Lọc theo trạng thái: active, inactive, banned.
     * @queryParam sort_by string Sắp xếp theo: id, name, email, created_at.
     * @queryParam sort_order string Thứ tự: asc, desc.
     * @queryParam limit integer Số bản ghi (1-100).
     */
    public function export(FilterRequest $request)
    {
        return Excel::download(new UsersExport($request->all()), 'users.xlsx');
    }

    /**
     * Nhập danh sách người dùng
     *
     * @bodyParam file file required File excel (xlsx, xls, csv). Cột: name, email, password, status.
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
            'data'    => new UserResource($user),
        ]);
    }
}
