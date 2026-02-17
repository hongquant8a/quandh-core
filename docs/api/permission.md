# API Permission (Core)

Quản lý quyền (permission): stats, index, show, store, update, destroy, bulk delete, export, import.

---

## Xuất Excel

- **Method:** GET
- **Path:** `/api/permissions/export`
- **Query:** Cùng bộ lọc với index: search, from_date, to_date, sort_by, sort_order, limit.
- **Response:** File permissions.xlsx.

---

## Nhập Excel

- **Method:** POST
- **Path:** `/api/permissions/import`
- **Body:** file (required) — xlsx, xls, csv. Cột: name, guard_name.
- **Response:** `{"message": "Import quyền thành công."}`

---

## Xóa hàng loạt

- **Method:** POST
- **Path:** `/api/permissions/bulk-delete`
- **Body:** ids (array) — danh sách ID permission.
- **Response:** `{"message": "Đã xóa thành công các quyền được chọn!"}`

---

## Thống kê

- **Method:** GET
- **Path:** `/api/permissions/stats`
- **Query:** Cùng bộ lọc với index.
- **Response:** `{ "total": 50 }`

---

## Danh sách permission

- **Method:** GET
- **Path:** `/api/permissions`
- **Query:** search (name, guard_name), from_date, to_date, sort_by (id, name, guard_name, created_at, updated_at), sort_order, limit (1-100).
- **Response:** Paginated collection (PermissionResource).

---

## Chi tiết permission

- **Method:** GET
- **Path:** `/api/permissions/{id}`
- **Response:** Object permission (PermissionResource).

---

## Tạo permission

- **Method:** POST
- **Path:** `/api/permissions`
- **Body:** name (required), guard_name (optional, mặc định guard web).
- **Response:** 201 + object permission + message: "Quyền đã được tạo thành công!".

---

## Cập nhật permission

- **Method:** PUT / PATCH
- **Path:** `/api/permissions/{id}`
- **Body:** name, guard_name.
- **Response:** Object permission + message: "Quyền đã được cập nhật!".

---

## Xóa permission

- **Method:** DELETE
- **Path:** `/api/permissions/{id}`
- **Response:** `{"message": "Quyền đã được xóa!"}`

---

## Response mẫu (PermissionResource)

```json
{
  "id": 1,
  "name": "posts.create",
  "guard_name": "web",
  "created_at": "14:30:00 17/02/2026",
  "updated_at": "14:30:00 17/02/2026"
}
```
