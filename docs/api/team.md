# API Team (Core)

Quản lý team (nhóm): stats, index, show, store, update, destroy, bulk delete, bulk status, change status, export, import.

---

## Xuất Excel

- **Method:** GET  
- **Path:** `/api/teams/export`  
- **Query:** Cùng bộ lọc với index — `search`, `status`, `from_date`, `to_date`, `sort_by`, `sort_order`, `limit`.  
- **Response:** File `teams.xlsx`.

---

## Nhập Excel

- **Method:** POST  
- **Path:** `/api/teams/import`  
- **Body:** `file` (required) — xlsx, xls, csv. Cột: name, slug, description, status.  
- **Response:** `{"message": "Import team thành công."}`

---

## Xóa hàng loạt

- **Method:** POST  
- **Path:** `/api/teams/bulk-delete`  
- **Body:** `ids` (array).  
- **Response:** `{"message": "Đã xóa thành công các team được chọn!"}`

---

## Cập nhật trạng thái hàng loạt

- **Method:** PATCH  
- **Path:** `/api/teams/bulk-status`  
- **Body:** `ids` (array), `status` (required: active | inactive).  
- **Response:** `{"message": "Cập nhật trạng thái team thành công."}`

---

## Thống kê

- **Method:** GET  
- **Path:** `/api/teams/stats`  
- **Query:** Cùng bộ lọc với index.  
- **Response:** `{ "total": 10, "active": 8, "inactive": 2 }`

---

## Danh sách team

- **Method:** GET  
- **Path:** `/api/teams`  
- **Query:** `search` (name, slug), `status`, `from_date`, `to_date`, `sort_by` (id | name | slug | status | created_at | updated_at), `sort_order`, `limit`.  
- **Response:** Paginated collection (TeamResource).

---

## Chi tiết team

- **Method:** GET  
- **Path:** `/api/teams/{id}`  
- **Response:** Object team (TeamResource).

---

## Tạo team

- **Method:** POST  
- **Path:** `/api/teams`  
- **Body:** `name` (required), `slug` (optional), `description` (optional), `status` (required: active | inactive).  
- **Response:** 201 + object team + `message`: "Team đã được tạo thành công!".

---

## Cập nhật team

- **Method:** PUT / PATCH  
- **Path:** `/api/teams/{id}`  
- **Body:** `name`, `slug`, `description`, `status`.  
- **Response:** Object team + `message`: "Team đã được cập nhật!".

---

## Xóa team

- **Method:** DELETE  
- **Path:** `/api/teams/{id}`  
- **Response:** `{"message": "Team đã được xóa!"}`

---

## Đổi trạng thái team

- **Method:** PATCH  
- **Path:** `/api/teams/{id}/status`  
- **Body:** `status` (required: active | inactive).  
- **Response:** `{"message": "Cập nhật trạng thái thành công!", "data": TeamResource}`

---

## Response mẫu (TeamResource)

```json
{
  "id": 1,
  "name": "Công ty A",
  "slug": "cong-ty-a",
  "description": "Mô tả team",
  "status": "active",
  "created_by": "Admin",
  "updated_by": "Admin",
  "created_at": "14:30:00 17/02/2026",
  "updated_at": "14:30:00 17/02/2026"
}
```
