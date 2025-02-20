Hello {{ $user->name }},
You are receiving this email because someone at {{$organization->organization->name}}
sent you an invitation to join the organization on chatibase, If this was made in error, please ignore this message,
otherwise, click on the "Link Below" to join the organization.

{{ url('auth/join/' . $token) }}
