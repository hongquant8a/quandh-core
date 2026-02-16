# API Bài viết (Post)

Quản lý bài viết tin tức: CRUD, lọc theo danh mục, đính kèm hình ảnh. Một bài viết có thể thuộc nhiều danh mục; hỗ trợ lượt xem (view_count).

---

## Danh sách bài viết

- **Method:** GET  
- **Path:** `/api/posts`  
- **Query:** `search`, `status`, `category_id` (lọc bài viết thuộc danh mục này), `sort_by` (id | title | created_at | view_count), `sort_order`, `limit`
- **Response:** Paginated collection; mỗi item có `categories` (mảng danh mục), `view_count`.

---

## Chi tiết bài viết

- **Method:** GET  
- **Path:** `/api/posts/{id}`  
- **Response:** Object bài viết kèm `categories`, `attachments`, `view_count`.

---

## Tăng lượt xem bài viết

- **Method:** POST  
- **Path:** `/api/posts/{id}/view`  
- **Response:** `{ "message": "Đã cập nhật lượt xem.", "view_count": 123 }`

---

## Tạo bài viết

- **Method:** POST  
- **Path:** `/api/posts`  
- **Request Body (form-data hoặc JSON):**
  - `title` (required), `content` (required), `status` (required: draft | published | archived)
  - `category_ids` (optional, mảng ID danh mục, tối đa 20 — một bài thuộc nhiều danh mục)
  - `images[]` (optional, mảng file ảnh: jpeg/png/gif/webp, tối đa 10 ảnh, mỗi ảnh ≤ 5MB)  
- **Response:** 201 + object bài viết (kèm categories, attachments, view_count).

---

## Cập nhật bài viết

- **Method:** PUT / PATCH  
- **Path:** `/api/posts/{id}`  
- **Request Body:** Giống tạo (các field tùy chọn). Thêm:
  - `category_ids`: mảng ID danh mục (ghi đè toàn bộ danh mục của bài).
  - `images[]`: ảnh mới (append).
  - `remove_attachment_ids[]`: mảng ID đính kèm cần xóa.  
- **Response:** Object bài viết đã cập nhật (kèm categories, attachments, view_count).
