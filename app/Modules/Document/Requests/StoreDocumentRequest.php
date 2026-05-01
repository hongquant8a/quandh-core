<?php

namespace App\Modules\Document\Requests;

use App\Modules\Document\Enums\DocumentStatusEnum;
use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'so_ky_hieu' => 'required|string|max:255|unique:documents,so_ky_hieu',
            'ten_van_ban' => 'required|string|max:255',
            'noi_dung' => 'nullable|string',
            'issuing_agency_id' => 'nullable|integer|exists:document_issuing_agencies,id',
            'issuing_level_id' => 'nullable|integer|exists:document_issuing_levels,id',
            'signer_id' => 'nullable|integer|exists:document_signers,id',
            'document_type_ids' => 'nullable|array|max:50',
            'document_type_ids.*' => 'integer|exists:document_types,id',
            'document_field_ids' => 'nullable|array|max:50',
            'document_field_ids.*' => 'integer|exists:document_fields,id',
            'ngay_ban_hanh' => 'nullable|date',
            'ngay_xuat_ban' => 'nullable|date',
            'ngay_hieu_luc' => 'nullable|date',
            'ngay_het_hieu_luc' => 'nullable|date|after_or_equal:ngay_hieu_luc',
            'status' => ['required', DocumentStatusEnum::rule()],
            'attachments' => 'nullable|array|max:20',
            'attachments.*' => 'file|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute là trường bắt buộc.',
            'string' => ':attribute phải là chuỗi.',
            'integer' => ':attribute phải là số nguyên.',
            'numeric' => ':attribute phải là số.',
            'boolean' => ':attribute phải là giá trị đúng/sai.',
            'array' => ':attribute phải là mảng.',
            'file' => ':attribute phải là tệp hợp lệ.',
            'mimes' => ':attribute phải đúng định dạng tệp cho phép.',
            'max' => ':attribute không được vượt quá :max ký tự/phần tử/dung lượng.',
            'min' => ':attribute phải lớn hơn hoặc bằng :min.',
            'date' => ':attribute phải là ngày hợp lệ.',
            'after_or_equal' => ':attribute phải sau hoặc bằng :date.',
            'in' => ':attribute không hợp lệ.',
            'exists' => ':attribute không tồn tại trong hệ thống.',
            'unique' => ':attribute đã tồn tại.',
        ];
    }
    public function attributes(): array
    {
        return [
            'so_ky_hieu' => 'so ky hieu',
            'ten_van_ban' => 'ten van ban',
            'noi_dung' => 'noi dung',
            'issuing_agency_id' => 'ID issuing agency',
            'issuing_level_id' => 'ID issuing level',
            'signer_id' => 'ID signer',
            'document_type_ids' => 'document type ids',
            'document_type_ids.*' => 'Phần tử của document type ids',
            'document_field_ids' => 'document field ids',
            'document_field_ids.*' => 'Phần tử của document field ids',
            'ngay_ban_hanh' => 'ngay ban hanh',
            'ngay_xuat_ban' => 'ngay xuat ban',
            'ngay_hieu_luc' => 'ngay hieu luc',
            'ngay_het_hieu_luc' => 'ngay het hieu luc',
            'status' => 'Trạng thái',
            'attachments' => 'Tệp đính kèm',
            'attachments.*' => 'Phần tử của Tệp đính kèm',
        ];
    }
    public function bodyParameters(): array
    {
        return [];
    }
}
