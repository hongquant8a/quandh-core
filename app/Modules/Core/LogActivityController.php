<?php

namespace App\Modules\Core;

use App\Http\Controllers\Controller;
use App\Modules\Core\Models\LogActivity;
use App\Modules\Core\Requests\FilterRequest;
use App\Modules\Core\Requests\BulkDestroyLogActivityRequest;
use App\Modules\Core\Requests\DestroyByDateLogActivityRequest;
use App\Modules\Core\Resources\LogActivityResource;
use App\Modules\Core\Resources\LogActivityCollection;

/**
 * @group Core - LogActivity
 *
 * Quản lý nhật ký truy cập: danh sách, chi tiết, xóa, xóa hàng loạt, xóa theo thời gian, xóa toàn bộ.
 */
class LogActivityController extends Controller
{
    /**
     * Thống kê nhật ký
     *
     * Tổng số bản ghi sau khi áp dụng bộ lọc.
     *
     * @queryParam search string Tìm kiếm (description, route, ip_address, country, user_type). Example: 127.0.0.1
     * @queryParam from_date date Lọc từ ngày (Y-m-d). Example: 2026-01-01
     * @queryParam to_date date Lọc đến ngày (Y-m-d). Example: 2026-12-31
     * @queryParam method_type string GET, POST, PUT, PATCH, DELETE. Example: GET
     * @queryParam status_code integer Mã HTTP (200, 400, 500...). Example: 200
     * @queryParam sort_by string id, description, route, method_type, status_code, ip_address, country, created_at. Example: created_at
     * @queryParam sort_order string asc, desc. Example: desc
     * @queryParam limit integer 1-100. Example: 10
     */
    public function stats(FilterRequest $request)
    {
        $base = LogActivity::filter($request->all());
        return $this->success(['total' => (clone $base)->count()]);
    }

    /**
     * Danh sách nhật ký
     *
     * @queryParam search string Tìm kiếm. Example: login
     * @queryParam from_date date Từ ngày. Example: 2026-01-01
     * @queryParam to_date date Đến ngày. Example: 2026-12-31
     * @queryParam method_type string GET, POST, PUT, PATCH, DELETE.
     * @queryParam status_code integer Mã HTTP.
     * @queryParam sort_by string Example: created_at
     * @queryParam sort_order string asc, desc. Example: desc
     * @queryParam limit integer Example: 10
     */
    public function index(FilterRequest $request)
    {
        $logs = LogActivity::with('user', 'organization')
            ->filter($request->all())
            ->paginate($request->limit ?? 10);
        return $this->successCollection(new LogActivityCollection($logs));
    }

    /**
     * Chi tiết nhật ký
     *
     * @urlParam logActivity integer required ID nhật ký. Example: 1
     */
    public function show(LogActivity $logActivity)
    {
        $logActivity->load('user', 'organization');
        return $this->successResource(new LogActivityResource($logActivity));
    }

    /**
     * Xóa nhật ký
     *
     * @urlParam logActivity integer required ID nhật ký. Example: 1
     */
    public function destroy(LogActivity $logActivity)
    {
        $logActivity->delete();
        return $this->success(null, 'Đã xóa nhật ký thành công!');
    }

    /**
     * Xóa hàng loạt nhật ký
     *
     * @bodyParam ids array required Danh sách ID. Example: [1, 2, 3]
     */
    public function bulkDestroy(BulkDestroyLogActivityRequest $request)
    {
        $count = LogActivity::whereIn('id', $request->ids)->delete();
        return $this->success(null, "Đã xóa thành công {$count} nhật ký!");
    }

    /**
     * Xóa nhật ký theo khoảng thời gian
     *
     * @bodyParam from_date date required Từ ngày (Y-m-d). Example: 2026-01-01
     * @bodyParam to_date date required Đến ngày (Y-m-d). Example: 2026-01-31
     */
    public function destroyByDate(DestroyByDateLogActivityRequest $request)
    {
        $count = LogActivity::whereDate('created_at', '>=', $request->from_date)
            ->whereDate('created_at', '<=', $request->to_date)
            ->delete();
        return $this->success(null, "Đã xóa thành công {$count} nhật ký trong khoảng thời gian!");
    }

    /**
     * Xóa toàn bộ nhật ký
     */
    public function destroyAll()
    {
        $count = LogActivity::count();
        LogActivity::truncate();
        return $this->success(null, "Đã xóa toàn bộ {$count} nhật ký!");
    }
}
