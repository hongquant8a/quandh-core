# Đưa Model, Export, Import vào từng module

**Ngày tạo:** 2025-02-16  
**Mục đích:** Ghi lại thay đổi cấu trúc: chuyển Model, Export, Import từ thư mục chung sang từng module (User, Post).

---

## 1. Cấu trúc mới

### Module User

- **Model:** `app/Modules/User/Models/User.php` — namespace `App\Modules\User\Models`
- **Export:** `app/Modules/User/Exports/UsersExport.php` — namespace `App\Modules\User\Exports`
- **Import:** `app/Modules/User/Imports/UsersImport.php` — namespace `App\Modules\User\Imports`

### Module Post

- **Model:** `app/Modules/Post/Models/Post.php` — namespace `App\Modules\Post\Models`
- **Export:** `app/Modules/Post/Exports/PostsExport.php` — namespace `App\Modules\Post\Exports`
- **Import:** `app/Modules/Post/Imports/PostsImport.php` — namespace `App\Modules\Post\Imports`

### Đã xóa (không còn dùng)

- `app/Models/User.php`, `app/Models/Post.php`
- `app/Exports/UsersExport.php`, `app/Exports/PostsExport.php`
- `app/Imports/UsersImport.php`, `app/Imports/PostsImport.php`

---

## 2. Cập nhật tham chiếu

| Vị trí | Thay đổi |
|--------|----------|
| `UserController` | `use App\Modules\User\Models\User`, `UsersExport`, `UsersImport` từ namespace module |
| `PostController` | `use App\Modules\Post\Models\Post`, `PostsExport`, `PostsImport` từ namespace module |
| `AuthController` | `use App\Modules\User\Models\User` |
| `config/auth.php` | `model` => `App\Modules\User\Models\User::class` |
| `DatabaseSeeder` | `use` User và Post từ namespace module |
| `UserFactory` | `$model = \App\Modules\User\Models\User::class` |
| `PostFactory` | `$model = Post::class` (Post từ `App\Modules\Post\Models`), `use App\Modules\User\Models\User` |

---

## 3. Quan hệ giữa các model

- **Post** (`App\Modules\Post\Models\Post`): quan hệ `creator()`, `editor()` trỏ tới `App\Modules\User\Models\User`.
- **User** (`App\Modules\User\Models\User`): quan hệ `creator()`, `editor()` trỏ tới chính `User` (cùng namespace).

Route model binding và Auth (Sanctum) vẫn hoạt động vì controller type-hint đúng class và `config/auth.php` trỏ đúng model User.

---

## 4. Ghi chú

- Không thêm autoload mới trong `composer.json`; namespace nằm trong `App\` (PSR-4 `app/`).
- Factory vẫn đặt trong `database/factories/`, chỉ cần thuộc tính `$model` trỏ tới model mới.
- Khi tạo module mới có Model/Export/Import, nên tạo thư mục `Models/`, `Exports/`, `Imports/` trong module và dùng namespace tương ứng.
