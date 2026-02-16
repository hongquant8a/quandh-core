# API Bài viết (Post)

Quản lý bài viết tin tức: CRUD, lọc theo danh mục, đính kèm hình ảnh.

---

## Danh sách bài viết

- **Method:** GET  
- **Path:** `/api/posts`  
- **Query:** `search`, `status`, `category_id`, `sort_by`, `sort_order`, `limit`  
- **Response:** Paginated collection; mỗi item có `category` (object) khi load.

---

## Chi tiết bài viết

- **Method:** GET  
- **Path:** `/api/posts/{id}`  
- **Response:** Object bài viết kèm `category` và `attachments` (mảng: id, url, original_name, mime_type, size, sort_order).

---

## Tạo bài viết

- **Method:** POST  
- **Path:** `/api/posts`  
- **Request Body (form-data hoặc JSON):**
  - `title` (required), `content` (required), `status` (required: draft | published | archived)
  - `category_id` (optional, ID danh mục)
  - `images[]` (optional, mảng file ảnh: jpeg/png/gif/webp, tối đa 10 ảnh, mỗi ảnh ≤ 5MB)  
- **Response:** 201 + object bài viết (kèm category, attachments).

---

## Cập nhật bài viết

- **Method:** PUT / PATCH  
- **Path:** `/api/posts/{id}`  
- **Request Body:** Giống tạo (các field tùy chọn). Thêm:
  - `images[]`: ảnh mới (append).
  - `remove_attachment_ids[]`: mảng ID đính kèm cần xóa.  
- **Response:** Object bài viết đã cập nhật (kèm category, attachments).
