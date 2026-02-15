# Authenticating requests

To authenticate requests, include an **`Authorization`** header with the value **`"Bearer Bearer {YOUR_ACCESS_TOKEN}"`**.

All authenticated endpoints are marked with a `requires authentication` badge in the documentation below.

Đăng nhập qua <code>POST /api/auth/login</code> với email và password để nhận <code>access_token</code>. Gửi token trong header <code>Authorization: Bearer {token}</code> cho các endpoint cần xác thực.
