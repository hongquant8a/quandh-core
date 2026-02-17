<?php

namespace App\Modules\User\Exports;

use App\Modules\User\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithHeadings
{
    public function __construct(
        protected array $filters = []
    ) {}

    /**
     * Xuất theo bộ lọc của index, đầy đủ trường như UserResource.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $users = User::with(['creator', 'editor'])
            ->filter($this->filters)
            ->get();

        return $users->map(fn ($user) => [
            'id'         => $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'status'     => $user->status,
            'created_by' => $user->creator?->name ?? 'N/A',
            'updated_by' => $user->editor?->name ?? 'N/A',
            'created_at' => $user->created_at?->format('d/m/Y H:i:s'),
            'updated_at' => $user->updated_at?->format('d/m/Y H:i:s'),
        ]);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Status',
            'Created By',
            'Updated By',
            'Created At',
            'Updated At',
        ];
    }
}
