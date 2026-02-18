<?php

namespace App\Modules\Core;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterRequest;
use App\Modules\Core\Models\Team;
use App\Modules\Core\Requests\StoreTeamRequest;
use App\Modules\Core\Requests\UpdateTeamRequest;
use App\Modules\Core\Requests\BulkDestroyTeamRequest;
use App\Modules\Core\Requests\BulkUpdateStatusTeamRequest;
use App\Modules\Core\Requests\ChangeStatusTeamRequest;
use App\Modules\Core\Requests\ImportTeamRequest;
use App\Modules\Core\Resources\TeamResource;
use App\Modules\Core\Resources\TeamCollection;
use App\Modules\Core\Resources\TeamTreeResource;
use Illuminate\Http\Request;
use App\Modules\Core\Exports\TeamsExport;
use App\Modules\Core\Imports\TeamsImport;
use Maatwebsite\Excel\Facades\Excel;

/**
 * @group Core - Team
 *
 * Quản lý team (nhóm): stats, index, show, store, update, destroy, bulk delete, bulk status, change status, export, import.
 */
class TeamController extends Controller
{
    /**
     * Thống kê team
     *
     * Tổng số, đang kích hoạt (active), không kích hoạt (inactive). Áp dụng cùng bộ lọc với index.
     *
     * @queryParam search string Từ khóa tìm kiếm (name, slug). Example: cong-ty
     * @queryParam status string Lọc theo trạng thái: active, inactive.
     * @queryParam from_date date Lọc từ ngày tạo (created_at) (Y-m-d). Example: 2026-02-01
     * @queryParam to_date date Lọc đến ngày tạo (created_at) (Y-m-d). Example: 2026-02-17
     * @queryParam sort_by string Sắp xếp theo: id, name, slug, status, created_at, updated_at. Example: created_at
     * @queryParam sort_order string Thứ tự: asc, desc. Example: desc
     * @queryParam limit integer Số bản ghi mỗi trang (1-100). Example: 10
     */
    public function stats(FilterRequest $request)
    {
        $base = Team::filter($request->all());
        return response()->json([
            'total'    => (clone $base)->count(),
            'active'   => (clone $base)->where('status', 'active')->count(),
            'inactive' => (clone $base)->where('status', '!=', 'active')->count(),
        ]);
    }

    /**
     * Danh sách team
     *
     * Lấy danh sách có phân trang, lọc và sắp xếp.
     *
     * @queryParam search string Từ khóa tìm kiếm (name, slug). Example: cong-ty
     * @queryParam status string Lọc theo trạng thái: active, inactive.
     * @queryParam from_date date Lọc từ ngày tạo (created_at) (Y-m-d). Example: 2026-02-01
     * @queryParam to_date date Lọc đến ngày tạo (created_at) (Y-m-d). Example: 2026-02-17
     * @queryParam sort_by string Sắp xếp theo: id, name, slug, status, created_at, updated_at. Example: id
     * @queryParam sort_order string Thứ tự: asc, desc. Example: desc
     * @queryParam limit integer Số bản ghi mỗi trang (1-100). Example: 10
     */
    public function index(FilterRequest $request)
    {
        $items = Team::with(['creator', 'editor', 'parent'])
            ->filter($request->all())
            ->treeOrder()
            ->paginate($request->limit ?? 10);
        return new TeamCollection($items);
    }

    /**
     * Cây team (toàn bộ cây, không phân trang). Cấu trúc parent_id.
     *
     * @queryParam status string Lọc theo trạng thái: active, inactive.
     */
    public function tree(Request $request)
    {
        $query = Team::query()
            ->when($request->status, fn ($q, $v) => $q->where('status', $v));
        $items = $query->orderBy('sort_order')->orderBy('id')->get();
        $tree = Team::buildTree($items);
        return TeamTreeResource::collection($tree);
    }

    /**
     * Chi tiết team
     *
     * @urlParam team integer required ID team. Example: 1
     */
    public function show(Team $team)
    {
        $team->load(['creator', 'editor', 'parent', 'children' => fn ($q) => $q->orderBy('sort_order')]);
        return new TeamResource($team);
    }

    /**
     * Tạo team mới
     *
     * @bodyParam name string required Tên team. Example: Công ty A
     * @bodyParam slug string Slug (nếu không gửi sẽ tự sinh từ name). Example: cong-ty-a
     * @bodyParam description string Mô tả. Example: Team quản trị
     * @bodyParam status string required Trạng thái: active, inactive. Example: active
     * @bodyParam parent_id integer ID team cha (null = gốc). Example: null
     * @bodyParam sort_order integer Thứ tự. Example: 0
     */
    public function store(StoreTeamRequest $request)
    {
        $team = Team::create($request->validated());
        return (new TeamResource($team))
            ->additional(['message' => 'Team đã được tạo thành công!']);
    }

    /**
     * Cập nhật team
     *
     * @urlParam team integer required ID team. Example: 1
     * @bodyParam name string Tên team. Example: Công ty A
     * @bodyParam slug string Slug. Example: cong-ty-a
     * @bodyParam description string Mô tả. Example: Team quản trị
     * @bodyParam status string Trạng thái: active, inactive. Example: inactive
     * @bodyParam parent_id integer ID team cha (null = gốc). Example: null
     * @bodyParam sort_order integer Thứ tự. Example: 0
     */
    public function update(UpdateTeamRequest $request, Team $team)
    {
        $data = $request->validated();
        if (isset($data['parent_id']) && (int) $data['parent_id'] !== 0) {
            if (static::isDescendantOf($data['parent_id'], $team->id)) {
                return response()->json(['message' => 'Không thể chọn team con làm team cha.'], 422);
            }
        }
        if (array_key_exists('parent_id', $data) && (int) $data['parent_id'] === 0) {
            $data['parent_id'] = null;
        }
        $team->update($data);
        return (new TeamResource($team->fresh(['parent', 'children'])))
            ->additional(['message' => 'Team đã được cập nhật!']);
    }

    protected static function isDescendantOf(int $candidateId, int $id): bool
    {
        $current = Team::find($id);
        while ($current && $current->parent_id) {
            if ($current->parent_id === $candidateId) {
                return true;
            }
            $current = Team::find($current->parent_id);
        }
        return false;
    }

    /**
     * Xóa team
     *
     * @urlParam team integer required ID team. Example: 1
     */
    public function destroy(Team $team)
    {
        $team->delete();
        return response()->json(['message' => 'Team đã được xóa!']);
    }

    /**
     * Xóa hàng loạt team
     *
     * @bodyParam ids array required Danh sách ID. Example: [1, 2, 3]
     */
    public function bulkDestroy(BulkDestroyTeamRequest $request)
    {
        Team::whereIn('id', $request->ids)->delete();
        return response()->json(['message' => 'Đã xóa thành công các team được chọn!']);
    }

    /**
     * Cập nhật trạng thái team hàng loạt
     *
     * @bodyParam ids array required Danh sách ID. Example: [1, 2, 3]
     * @bodyParam status string required Trạng thái: active, inactive. Example: active
     */
    public function bulkUpdateStatus(BulkUpdateStatusTeamRequest $request)
    {
        Team::whereIn('id', $request->ids)->update(['status' => $request->status]);
        return response()->json(['message' => 'Cập nhật trạng thái team thành công.']);
    }

    /**
     * Thay đổi trạng thái team
     *
     * @urlParam team integer required ID team. Example: 1
     * @bodyParam status string required Trạng thái mới: active, inactive. Example: inactive
     */
    public function changeStatus(ChangeStatusTeamRequest $request, Team $team)
    {
        $team->update(['status' => $request->status]);
        return response()->json([
            'message' => 'Cập nhật trạng thái thành công!',
            'data'    => new TeamResource($team),
        ]);
    }

    /**
     * Xuất danh sách team
     *
     * Áp dụng cùng bộ lọc với index. Trả về file Excel.
     *
     * @queryParam search string Từ khóa tìm kiếm (name, slug).
     * @queryParam status string Lọc theo trạng thái: active, inactive.
     * @queryParam from_date date Lọc từ ngày tạo (created_at) (Y-m-d).
     * @queryParam to_date date Lọc đến ngày tạo (created_at) (Y-m-d).
     * @queryParam sort_by string Sắp xếp theo: id, name, slug, status, created_at, updated_at.
     * @queryParam sort_order string Thứ tự: asc, desc.
     */
    public function export(FilterRequest $request)
    {
        return Excel::download(new TeamsExport($request->all()), 'teams.xlsx');
    }

    /**
     * Nhập danh sách team
     *
     * @bodyParam file file required File excel (xlsx, xls, csv). Cột: name, slug, description, status.
     */
    public function import(ImportTeamRequest $request)
    {
        Excel::import(new TeamsImport, $request->file('file'));
        return response()->json(['message' => 'Import team thành công.']);
    }
}
