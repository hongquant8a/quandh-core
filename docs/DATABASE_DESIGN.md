# Sơ đồ thiết kế cơ sở dữ liệu

## Bảng liên quan Bài viết & Danh mục

### `posts`
| Cột | Kiểu | Mô tả |
|-----|------|-------|
| id | bigint PK | |
| title | string | Tiêu đề |
| content | text | Nội dung |
| status | string | draft, published, archived |
| view_count | unsigned integer, default 0 | Lượt xem bài viết |
| created_by, updated_by | FK nullable → users.id | |
| created_at, updated_at | timestamp | |

Quan hệ với danh mục: n-n qua bảng `post_post_category`.

### `post_categories`
(Xem module PostCategory — cấu trúc cây Nested Set.)

### `post_post_category` (pivot)
Bài viết thuộc nhiều danh mục.

| Cột | Kiểu | Mô tả |
|-----|------|-------|
| id | bigint PK | |
| post_id | FK → posts.id (cascade on delete) | |
| post_category_id | FK → post_categories.id (cascade on delete) | |
| created_at, updated_at | timestamp | |
| unique(post_id, post_category_id) | | |

---

*File này được cập nhật khi có migration mới liên quan thiết kế bảng.*
