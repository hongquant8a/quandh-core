# API Danh mục tin tức (Post Category)

Quản lý danh mục tin tức phân cấp theo cấu trúc cây (Nested Set). Cấu trúc và action đồng bộ với User / Post.

---

## Xuất Excel

- **Method:** GET  
- **Path:** `/api/post-categories/export`  
- **Response:** File `post-categories.xlsx` (danh mục theo thứ tự cây: cha trước con).

---

## Nhập Excel

- **Method:** POST  
- **Path:** `/api/post-categories/import`  
- **Body:** `file` (required) — xlsx, xls, csv. Cột: name, slug, description, status, sort_order, parent_slug.  
- **Response:** `{"message": "Post categories imported successfully."}`

---

## Xóa hàng loạt

- **Method:** POST  
- **Path:** `/api/post-categories/bulk-delete`  
- **Body:** `ids` (array) — danh sách ID danh mục.  
- **Response:** `{"message": "Đã xóa thành công các danh mục được chọn!"}`  
- **Lưu ý:** Mỗi danh mục bị xóa sẽ xóa luôn toàn bộ con (nested set).

---

## Cập nhật trạng thái hàng loạt

- **Method:** PATCH  
- **Path:** `/api/post-categories/bulk-status`  
- **Body:** `ids` (array), `status` (required: active | inactive).  
- **Response:** `{"message": "Cập nhật trạng thái thành công các danh mục được chọn!"}`

---

## Danh sách danh mục (phẳng, phân trang)

- **Method:** GET  
- **Path:** `/api/post-categories`  
- **Query:** `search`, `status`, `sort_by` (id, name, sort_order, created_at), `sort_order` (asc, desc), `limit` (1-100).  
- **Response:** Paginated collection (cùng format User/Post).

---

## Cây danh mục (toàn bộ, không phân trang)

- **Method:** GET  
- **Path:** `/api/post-categories/tree`  
- **Query:** `status` (optional).  
- **Response:** Mảng danh mục dạng cây (mỗi node có `children`).

---

## Chi tiết danh mục

- **Method:** GET  
- **Path:** `/api/post-categories/{id}`  
- **Response:** Object danh mục kèm `parent`, `children`.

---

## Tạo danh mục

- **Method:** POST  
- **Path:** `/api/post-categories`  
- **Body:** `name` (required), `slug`, `description`, `status` (required: active | inactive), `sort_order`, `parent_id` (null = gốc).  
- **Response:** 201 + object danh mục đã tạo.

---

## Cập nhật danh mục

- **Method:** PUT / PATCH  
- **Path:** `/api/post-categories/{id}`  
- **Body:** Giống tạo (các field tùy chọn). Gửi `parent_id: 0` để chuyển danh mục thành gốc.  
- **Response:** Object danh mục đã cập nhật.

---

## Xóa danh mục

- **Method:** DELETE  
- **Path:** `/api/post-categories/{id}`  
- **Response:** `{"message": "Danh mục đã được xóa!"}`  
- **Lưu ý:** Xóa cả toàn bộ danh mục con (nested set).

---

## Đổi trạng thái danh mục

- **Method:** PATCH  
- **Path:** `/api/post-categories/{id}/status`  
- **Body:** `status` (required: active | inactive).  
- **Response:** `{"message": "Cập nhật trạng thái thành công!", "data": { ... }}`
