<?php

namespace App\Modules\Auth;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Requests\LoginRequest;
use App\Modules\Auth\Requests\ForgotPasswordRequest;
use App\Modules\Auth\Requests\ResetPasswordRequest;
use App\Modules\Core\Enums\UserStatusEnum;
use App\Modules\Core\Models\User;
use App\Modules\Core\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

/**
 * @group Auth
 *
 * Xác thực: đăng nhập, đăng xuất, quên mật khẩu, đặt lại mật khẩu
 */
class AuthController extends Controller
{
    /**
     * Đăng nhập
     *
     * Trả về access_token và thông tin user dùng cho các request cần xác thực.
     *
     * @unauthenticated
     * @bodyParam email string required Email đăng nhập. Example: admin@example.com
     * @bodyParam password string required Mật khẩu. Example: password
     * @response 200 {"success": true, "message": "Đăng nhập thành công.", "data": {"access_token": "1|xxx...", "token_type": "Bearer", "user": {"id": 1, "name": "Admin", "email": "admin@example.com", "status": "active"}}}
     */
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return $this->unauthorized('Thông tin đăng nhập không chính xác');
        }

        if ($user->status !== UserStatusEnum::Active->value) {
            return $this->forbidden('Tài khoản của bạn đã bị khóa');
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        $data = [
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => (new UserResource($user))->resolve(),
        ];
        return $this->success($data, 'Đăng nhập thành công.');
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
        $request->user()->currentAccessToken()->delete();
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
        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
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
        $status = Password::reset($request->only('email', 'password', 'token'), function (User $user, string $password) {
            $user->forceFill(['password' => Hash::make($password)])->save();
        });

        return $status === Password::PASSWORD_RESET
            ? $this->success(null, 'Mật khẩu đã được đặt lại')
            : $this->error('Không thể đặt lại mật khẩu', 400);
    }
}
