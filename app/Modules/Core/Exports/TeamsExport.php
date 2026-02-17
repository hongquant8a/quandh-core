<?php

namespace App\Modules\Core\Exports;

use App\Modules\Core\Models\Team;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TeamsExport implements FromCollection, WithHeadings
{
    public function __construct(
        protected array $filters = []
    ) {}

    public function collection()
    {
        $items = Team::with(['creator', 'editor'])->filter($this->filters)->get();
        return $items->map(fn ($t) => [
            'id'         => $t->id,
            'name'       => $t->name,
            'slug'       => $t->slug,
            'description' => $t->description,
            'status'     => $t->status,
            'created_by' => $t->creator?->name ?? 'N/A',
            'updated_by' => $t->editor?->name ?? 'N/A',
            'created_at' => $t->created_at?->format('H:i:s d/m/Y'),
            'updated_at' => $t->updated_at?->format('H:i:s d/m/Y'),
        ]);
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Slug', 'Description', 'Status', 'Created By', 'Updated By', 'Created At', 'Updated At'];
    }
}
