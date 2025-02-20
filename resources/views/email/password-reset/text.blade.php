Reset Account Password
Hello {{ $user->name }},
You are receiving this email because we received a password reset request for your
account with us, If you did not request a password reset please ignore this message,
otherwise, click on the "Link Below" to reset your account password.

{{ url('auth/reset-password/' . $token) }}
