<?php

namespace App\Modules\Auth;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Requests\LoginRequest;
use App\Modules\Auth\Requests\ForgotPasswordRequest;
use App\Modules\Auth\Requests\ResetPasswordRequest;
use Illuminate\Http\Request;
use App\Modules\Auth\Services\AuthService;

/**
 * @group Auth
 *
 * Xác thực: đăng nhập, đăng xuất, quên mật khẩu, đặt lại mật khẩu
 */
class AuthController extends Controller
{
    public function __construct(private AuthService $authService)
    {
    }

    /**
     * Đăng nhập
     *
     * Trả về access_token và thông tin user dùng cho các request cần xác thực.
     *
     * @unauthenticated
     * @bodyParam email string required Email hoặc tên đăng nhập (user_name). Example: admin@example.com
     * @bodyParam password string required Mật khẩu. Example: password
     * @response 200 {"success": true, "message": "Đăng nhập thành công.", "data": {"access_token": "1|xxx...", "token_type": "Bearer", "user": {"id": 1, "name": "Admin", "email": "admin@example.com", "status": "active"}}}
     */
    public function login(LoginRequest $request)
    {
        $result = $this->authService->login($request->email, $request->password);

        if (! $result['ok']) {
            if ($result['type'] === 'unauthorized') {
                return $this->unauthorized($result['message']);
            }

            return $this->forbidden($result['message']);
        }

        return $this->success($result['data'], 'Đăng nhập thành công.');
    }

    /**
     * Đăng xuất
     *
     * Hủy token hiện tại.
     *
     * @response 200 {"success": true, "message": "Đã đăng xuất"}
     */
    public function logout(Request $request)
    {
        $this->authService->logout($request->user());
        return $this->success(null, 'Đã đăng xuất');
    }

    /**
     * Quên mật khẩu
     *
     * Gửi link đặt lại mật khẩu qua email.
     *
     * @unauthenticated
     * @bodyParam email string required Email tài khoản. Example: user@example.com
     * @response 200 {"success": true, "message": "Link reset đã được gửi vào Email"}
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $ok = $this->authService->forgotPassword($request->email);

        return $ok
            ? $this->success(null, 'Link reset đã được gửi vào Email')
            : $this->error('Không thể gửi mail', 400);
    }

    /**
     * Đặt lại mật khẩu
     *
     * Đặt mật khẩu mới dùng token từ email reset.
     *
     * @unauthenticated
     * @bodyParam email string required Email tài khoản. Example: user@example.com
     * @bodyParam password string required Mật khẩu mới (tối thiểu 6 ký tự, có xác nhận). Example: newpassword123
     * @bodyParam password_confirmation string required Xác nhận mật khẩu.
     * @bodyParam token string required Token từ email reset.
     * @response 200 {"success": true, "message": "Mật khẩu đã được đặt lại"}
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $ok = $this->authService->resetPassword($request->email, $request->password, $request->token);

        return $ok
            ? $this->success(null, 'Mật khẩu đã được đặt lại')
            : $this->error('Không thể đặt lại mật khẩu', 400);
    }
}
