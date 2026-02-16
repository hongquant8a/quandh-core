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
     * Xuất theo bộ lọc của index.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return User::filter($this->filters)->get(['id', 'name', 'email', 'created_at']);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Created At',
        ];
    }
}
