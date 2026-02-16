# API Người dùng (User)

Quản lý tài khoản người dùng: CRUD, xuất/nhập Excel, thao tác hàng loạt, đổi trạng thái. Cấu trúc action đồng bộ với Post / Post Category.

---

## Xuất Excel

- **Method:** GET  
- **Path:** `/api/users/export`  
- **Query:** Cùng bộ lọc với index — `search`, `status`, `sort_by`, `sort_order`, `limit`.  
- **Response:** File `users.xlsx` (dữ liệu đã lọc).

---

## Nhập Excel

- **Method:** POST  
- **Path:** `/api/users/import`  
- **Body:** `file` (required) — xlsx, xls, csv.  
- **Response:** `{"message": "Users imported successfully."}`

---

## Xóa hàng loạt

- **Method:** POST  
- **Path:** `/api/users/bulk-delete`  
- **Body:** `ids` (array) — danh sách ID người dùng.  
- **Response:** `{"message": "Đã xóa thành công các tài khoản được chọn!"}`

---

## Cập nhật trạng thái hàng loạt

- **Method:** PATCH  
- **Path:** `/api/users/bulk-status`  
- **Body:** `ids` (array), `status` (required: active | inactive | banned).  
- **Response:** `{"message": "Cập nhật trạng thái thành công"}`

---

## Thống kê

- **Method:** GET  
- **Path:** `/api/users/stats`  
- **Query:** Cùng bộ lọc với index — `search`, `status`, `sort_by`, `sort_order`, `limit`.  
- **Response:** `{ "total": 100, "active": 80, "inactive": 20 }` — tổng số bản ghi (sau khi lọc), đang kích hoạt (status=active), không kích hoạt (inactive, banned).

---

## Danh sách người dùng

- **Method:** GET  
- **Path:** `/api/users`  
- **Query:** `search` (name, email), `status` (active | inactive | banned), `sort_by` (id | title | name | created_at), `sort_order` (asc | desc), `limit` (1-100).  
- **Response:** Paginated collection (UserResource).

---

## Chi tiết người dùng

- **Method:** GET  
- **Path:** `/api/users/{id}`  
- **Response:** Object người dùng (UserResource).

---

## Tạo người dùng

- **Method:** POST  
- **Path:** `/api/users`  
- **Body:** `name` (required), `email` (required, unique), `password` (required, min 6, confirmed), `password_confirmation` (required), `status` (optional: active | inactive | banned).  
- **Response:** 201 + object người dùng + `message`: "Tài khoản đã được tạo thành công!".

---

## Cập nhật người dùng

- **Method:** PUT / PATCH  
- **Path:** `/api/users/{id}`  
- **Body:** `name`, `email` (unique nếu đổi), `password` (optional, min 6, confirmed), `password_confirmation`, `status`.  
- **Response:** Object người dùng đã cập nhật.

---

## Xóa người dùng

- **Method:** DELETE  
- **Path:** `/api/users/{id}`  
- **Response:** `{"message": "Tài khoản đã được xóa thành công!"}`

---

## Đổi trạng thái người dùng

- **Method:** PATCH  
- **Path:** `/api/users/{id}/status`  
- **Body:** `status` (required: active | inactive | banned).  
- **Response:** `{"message": "Cập nhật trạng thái thành công!", "data": { ... }}`
