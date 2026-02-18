# API Role (Core)

Quản lý vai trò (role): thống kê, danh sách, chi tiết, CRUD, xóa/bulk status, đổi trạng thái, xuất/nhập Excel. Role gắn với team và danh sách permission.

**Base path:** `/api/roles`

---

## Thống kê

| | |
|---|---|
| **Method** | GET |
| **Path** | `/api/roles/stats` |
| **Query** | `search` (name, guard_name), `status` (active \| inactive), `from_date` (Y-m-d), `to_date` (Y-m-d), `sort_by`, `sort_order`, `limit` (1-100). Cùng bộ lọc với index. |
| **Response** | `{ "total": 20, "active": 18, "inactive": 2 }`. |

---

## Danh sách role

| | |
|---|---|
| **Method** | GET |
| **Path** | `/api/roles` |
| **Query** | `search`, `status`, `from_date`, `to_date`, `sort_by` (id \| name \| guard_name \| status \| created_at \| updated_at), `sort_order` (asc \| desc), `limit` (1-100). |
| **Response** | Paginated collection (RoleResource), mỗi item có `team`, `permissions`. |

---

## Chi tiết role

| | |
|---|---|
| **Method** | GET |
| **Path** | `/api/roles/{id}` |
| **UrlParam** | `id` — ID role. |
| **Response** | Object role (RoleResource), kèm `team`, `permissions`. |

---

## Tạo role

| | |
|---|---|
| **Method** | POST |
| **Path** | `/api/roles` |
| **Body** | `name` (required), `guard_name` (optional), `team_id` (optional), `status` (optional: active \| inactive), `permission_ids` (optional, array ID permission). |
| **Response** | 201, object role + `"message": "Vai trò đã được tạo thành công!"`. |

---

## Cập nhật role

| | |
|---|---|
| **Method** | PUT / PATCH |
| **Path** | `/api/roles/{id}` |
| **Body** | `name`, `guard_name`, `team_id`, `status`, `permission_ids` (sync danh sách quyền). |
| **Response** | Object role + `"message": "Vai trò đã được cập nhật!"`. |

---

## Xóa role

| | |
|---|---|
| **Method** | DELETE |
| **Path** | `/api/roles/{id}` |
| **Response** | `{ "message": "Vai trò đã được xóa!" }`. |

---

## Xóa hàng loạt

| | |
|---|---|
| **Method** | POST |
| **Path** | `/api/roles/bulk-delete` |
| **Body** | `ids` (array) — danh sách ID role. |
| **Response** | `{ "message": "Đã xóa thành công các vai trò được chọn!" }`. |

---

## Cập nhật trạng thái hàng loạt

| | |
|---|---|
| **Method** | PATCH |
| **Path** | `/api/roles/bulk-status` |
| **Body** | `ids` (array), `status` (required: active \| inactive). |
| **Response** | `{ "message": "Cập nhật trạng thái vai trò thành công." }`. |

---

## Đổi trạng thái role

| | |
|---|---|
| **Method** | PATCH |
| **Path** | `/api/roles/{id}/status` |
| **Body** | `status` (required: active \| inactive). |
| **Response** | `{ "message": "Cập nhật trạng thái thành công!", "data": RoleResource }`. |

---

## Xuất Excel

| | |
|---|---|
| **Method** | GET |
| **Path** | `/api/roles/export` |
| **Query** | Cùng bộ lọc với index: search, status, from_date, to_date, sort_by, sort_order, limit. |
| **Response** | File `roles.xlsx`. |

---

## Nhập Excel

| | |
|---|---|
| **Method** | POST |
| **Path** | `/api/roles/import` |
| **Body** | `file` (required) — xlsx, xls, csv. Cột: name, guard_name, team_id, status. |
| **Response** | `{ "message": "Import vai trò thành công." }`. |

---

## Response mẫu (RoleResource)

```json
{
  "id": 1,
  "name": "admin",
  "guard_name": "web",
  "team_id": 1,
  "team": { "id": 1, "name": "Công ty A" },
  "status": "active",
  "permissions": ["posts.create", "posts.update"],
  "created_at": "14:30:00 17/02/2026",
  "updated_at": "14:30:00 17/02/2026"
}
```
