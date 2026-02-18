# API Team (Core)

Quản lý team (nhóm) phân cấp theo `parent_id`: thống kê, danh sách, cây, CRUD, xóa/bulk status, đổi trạng thái, xuất/nhập Excel.

**Base path:** `/api/teams`

---

## Thống kê

| | |
|---|---|
| **Method** | GET |
| **Path** | `/api/teams/stats` |
| **Query** | `search` (name, slug), `status` (active \| inactive), `from_date` (Y-m-d), `to_date` (Y-m-d), `sort_by`, `sort_order`, `limit` (1-100). Cùng bộ lọc với index. |
| **Response** | `{ "total": 10, "active": 8, "inactive": 2 }` |

---

## Danh sách team

| | |
|---|---|
| **Method** | GET |
| **Path** | `/api/teams` |
| **Query** | `search`, `status`, `from_date`, `to_date`, `sort_by` (id \| name \| slug \| status \| created_at \| updated_at), `sort_order` (asc \| desc), `limit` (1-100). Thứ tự theo cây (treeOrder). |
| **Response** | Paginated collection (TeamResource), mỗi item có `creator`, `editor`, `parent`. |

---

## Cây team

| | |
|---|---|
| **Method** | GET |
| **Path** | `/api/teams/tree` |
| **Query** | `status` (active \| inactive). |
| **Response** | Mảng cây (không phân trang), mỗi node có `children` đệ quy — TeamTreeResource. |

---

## Chi tiết team

| | |
|---|---|
| **Method** | GET |
| **Path** | `/api/teams/{id}` |
| **UrlParam** | `id` — ID team. |
| **Response** | Object team (TeamResource), kèm `parent`, `children`. |

---

## Tạo team

| | |
|---|---|
| **Method** | POST |
| **Path** | `/api/teams` |
| **Body** | `name` (required), `slug` (optional, tự sinh từ name), `description` (optional), `status` (required: active \| inactive), `parent_id` (optional, null = gốc), `sort_order` (optional). |
| **Response** | 201, object team + `"message": "Team đã được tạo thành công!"`. |

---

## Cập nhật team

| | |
|---|---|
| **Method** | PUT / PATCH |
| **Path** | `/api/teams/{id}` |
| **Body** | `name`, `slug`, `description`, `status`, `parent_id` (null hoặc 0 = gốc), `sort_order`. Không được chọn team con làm team cha. |
| **Response** | Object team + `"message": "Team đã được cập nhật!"`. |

---

## Xóa team

| | |
|---|---|
| **Method** | DELETE |
| **Path** | `/api/teams/{id}` |
| **Response** | `{ "message": "Team đã được xóa!" }`. |

---

## Xóa hàng loạt

| | |
|---|---|
| **Method** | POST |
| **Path** | `/api/teams/bulk-delete` |
| **Body** | `ids` (array) — danh sách ID team. |
| **Response** | `{ "message": "Đã xóa thành công các team được chọn!" }`. |

---

## Cập nhật trạng thái hàng loạt

| | |
|---|---|
| **Method** | PATCH |
| **Path** | `/api/teams/bulk-status` |
| **Body** | `ids` (array), `status` (required: active \| inactive). |
| **Response** | `{ "message": "Cập nhật trạng thái team thành công." }`. |

---

## Đổi trạng thái team

| | |
|---|---|
| **Method** | PATCH |
| **Path** | `/api/teams/{id}/status` |
| **Body** | `status` (required: active \| inactive). |
| **Response** | `{ "message": "Cập nhật trạng thái thành công!", "data": TeamResource }`. |

---

## Xuất Excel

| | |
|---|---|
| **Method** | GET |
| **Path** | `/api/teams/export` |
| **Query** | Cùng bộ lọc với index: `search`, `status`, `from_date`, `to_date`, `sort_by`, `sort_order`. |
| **Response** | File `teams.xlsx`. |

---

## Nhập Excel

| | |
|---|---|
| **Method** | POST |
| **Path** | `/api/teams/import` |
| **Body** | `file` (required) — xlsx, xls, csv. Cột: name, slug, description, status. |
| **Response** | `{ "message": "Import team thành công." }`. |

---

## Response mẫu (TeamResource)

```json
{
  "id": 1,
  "name": "Công ty A",
  "slug": "cong-ty-a",
  "description": "Mô tả team",
  "status": "active",
  "parent_id": null,
  "sort_order": 0,
  "depth": 0,
  "created_by": "Admin",
  "updated_by": "Admin",
  "created_at": "14:30:00 17/02/2026",
  "updated_at": "14:30:00 17/02/2026"
}
```
