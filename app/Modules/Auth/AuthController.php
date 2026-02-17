<?php

namespace App\Modules\Auth;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Requests\LoginRequest;
use App\Modules\Auth\Requests\ForgotPasswordRequest;
use App\Modules\Auth\Requests\ResetPasswordRequest;
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
     */
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Thông tin đăng nhập không chính xác'], 401);
        }

        if ($user->status !== 'active') {
            return response()->json(['message' => 'Tài khoản của bạn đã bị khóa'], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => new UserResource($user),
        ]);
    }

    /**
     * Đăng xuất
     *
     * Hủy token hiện tại.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Đã đăng xuất']);
    }

    /**
     * Quên mật khẩu
     *
     * Gửi link đặt lại mật khẩu qua email.
     *
     * @unauthenticated
     * @bodyParam email string required Email tài khoản. Example: user@example.com
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Link reset đã được gửi vào Email'])
            : response()->json(['message' => 'Không thể gửi mail'], 400);
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
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $status = Password::reset($request->only('email', 'password', 'token'), function (User $user, string $password) {
            $user->forceFill(['password' => Hash::make($password)])->save();
        });

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => 'Mật khẩu đã được đặt lại'])
            : response()->json(['message' => 'Không thể đặt lại mật khẩu'], 400);
    }
}
