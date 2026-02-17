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
    public function stats(FilterRequest $request)
    {
        $base = Team::filter($request->all());
        return response()->json([
            'total'    => (clone $base)->count(),
            'active'   => (clone $base)->where('status', 'active')->count(),
            'inactive' => (clone $base)->where('status', '!=', 'active')->count(),
        ]);
    }

    public function index(FilterRequest $request)
    {
        $items = Team::with(['creator', 'editor'])
            ->filter($request->all())
            ->paginate($request->limit ?? 10);
        return new TeamCollection($items);
    }

    public function show(Team $team)
    {
        return new TeamResource($team);
    }

    public function store(StoreTeamRequest $request)
    {
        $team = Team::create($request->validated());
        return (new TeamResource($team))
            ->additional(['message' => 'Team đã được tạo thành công!']);
    }

    public function update(UpdateTeamRequest $request, Team $team)
    {
        $team->update($request->validated());
        return (new TeamResource($team))
            ->additional(['message' => 'Team đã được cập nhật!']);
    }

    public function destroy(Team $team)
    {
        $team->delete();
        return response()->json(['message' => 'Team đã được xóa!']);
    }

    public function bulkDestroy(BulkDestroyTeamRequest $request)
    {
        Team::whereIn('id', $request->ids)->delete();
        return response()->json(['message' => 'Đã xóa thành công các team được chọn!']);
    }

    public function bulkUpdateStatus(BulkUpdateStatusTeamRequest $request)
    {
        Team::whereIn('id', $request->ids)->update(['status' => $request->status]);
        return response()->json(['message' => 'Cập nhật trạng thái team thành công.']);
    }

    public function changeStatus(ChangeStatusTeamRequest $request, Team $team)
    {
        $team->update(['status' => $request->status]);
        return response()->json([
            'message' => 'Cập nhật trạng thái thành công!',
            'data'    => new TeamResource($team),
        ]);
    }

    public function export(FilterRequest $request)
    {
        return Excel::download(new TeamsExport($request->all()), 'teams.xlsx');
    }

    public function import(ImportTeamRequest $request)
    {
        Excel::import(new TeamsImport, $request->file('file'));
        return response()->json(['message' => 'Import team thành công.']);
    }
}
