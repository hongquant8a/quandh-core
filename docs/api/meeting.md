# API Meeting

## Base path
- `/api/meetings`

## Header & phạm vi dữ liệu

- Bắt buộc: `Authorization: Bearer {token}` và `X-Organization-Id: {organization_id}`.
- Tất cả endpoint chỉ thao tác dữ liệu cuộc họp thuộc tổ chức hiện tại.

## Endpoints

| Method | Path | Mô tả |
|---|---|---|
| GET | `/api/meetings/stats` | Thống kê cuộc họp |
| GET | `/api/meetings` | Danh sách cuộc họp |
| GET | `/api/meetings/{meeting}` | Chi tiết cuộc họp |
| POST | `/api/meetings` | Tạo cuộc họp |
| PUT/PATCH | `/api/meetings/{meeting}` | Cập nhật cuộc họp |
| DELETE | `/api/meetings/{meeting}` | Xóa cuộc họp |
| POST | `/api/meetings/bulk-delete` | Xóa hàng loạt |
| PATCH | `/api/meetings/bulk-status` | Cập nhật trạng thái hàng loạt |
| PATCH | `/api/meetings/{meeting}/status` | Đổi trạng thái |

## Endpoint liên quan module meeting

- `/api/meeting-types`, `/api/meeting-locations`, `/api/meeting-document-types`
- `/api/meeting-attendee-groups`, `/api/meeting-attendees`
- `/api/meeting-agendas` (có `PATCH /reorder`)
- `/api/meeting-documents` (có upload file qua multipart `file`, `PATCH /reorder`)
- `/api/meeting-participants`
- `/api/meeting-attendances`
- `/api/meeting-vote-topics` (có `PATCH /{id}/open`, `PATCH /{id}/close`)
- `/api/meeting-vote-responses`
- `/api/meeting-conclusions` (upload file kết luận qua multipart `file`)
- `/api/meeting-discussion-registrations` (có `PATCH /reorder`)
- `/api/meeting-personal-notes` (có `PATCH /reorder`)
- `/api/meeting-personal-note-attachments` (upload `file`, có `PATCH /reorder`)
- Public meetings: `/api/meetings/public`, `/api/meetings/public/{meeting}`
- Public documents: `/api/meeting-documents/public`, `/api/meeting-documents/public/{meetingDocument}`

## Request body chính (store/update)

```json
{
  "meeting_type_id": 1,
  "meeting_location_id": 1,
  "title": "Họp giao ban tuần",
  "is_public": false,
  "content": "Nội dung họp...",
  "start_time": "2026-05-01 08:00:00",
  "end_time": "2026-05-01 10:00:00",
  "status": "draft",
  "published_at": null
}
```
