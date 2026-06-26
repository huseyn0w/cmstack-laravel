@extends(config('app.template_name').'/index')


@section('content')

<section class="mx-auto max-w-[440px] px-5 py-16 sm:py-20">
    <div class="mb-8 text-center">
        <h1 class="font-serif text-3xl font-semibold tracking-tight text-[var(--text)]">@lang('login.login_page_headline')</h1>
        <p class="mt-2 text-sm text-[var(--text-muted)]">@lang('login.login')</p>
    </div>

    <x-card>
        {{-- Social sign-in --}}
        <p class="mb-3 text-center text-xs font-medium uppercase tracking-wider text-[var(--text-muted)]">@lang('login.login_with')</p>
        @include('auth.social')

        <div class="my-6 flex items-center gap-3 text-xs uppercase tracking-wider text-[var(--text-subtle)]">
            <span class="h-px flex-1 bg-[var(--border)]"></span>
            <span>@lang('login.or')</span>
            <span class="h-px flex-1 bg-[var(--border)]"></span>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-5" novalidate>
            @csrf

            <x-field
                name="email"
                :label="__('login.username_or_email')"
                :error="$errors->first('email') ?? $errors->first('username')"
            >
                <input
                    id="email"
                    type="text"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="email"
                    autofocus
                    data-testid="login-username"
                    @if($errors->has('email') || $errors->has('username')) aria-invalid="true" aria-describedby="email-error" @endif
                    class="field-input @error('email') border-[var(--error)] @enderror @error('username') border-[var(--error)] @enderror"
                >
            </x-field>

            <x-field
                name="password"
                :label="__('login.password')"
                :error="$errors->first('password')"
            >
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    data-testid="login-password"
                    @if($errors->has('password')) aria-invalid="true" aria-describedby="password-error" @endif
                    class="field-input @error('password') border-[var(--error)] @enderror"
                >
            </x-field>

            <label for="remember" class="flex cursor-pointer items-center gap-2.5 text-sm text-[var(--text-muted)]">
                <input
                    id="remember"
                    type="checkbox"
                    name="remember"
                    class="rounded border-[var(--border-strong)] text-[var(--primary)] focus:ring-[var(--ring)]"
                    {{ old('remember') ? 'checked' : '' }}
                >
                @lang('login.remember_me')
            </label>

            <div class="flex items-center justify-between gap-4 pt-1">
                <x-button type="submit" variant="primary" data-testid="login-submit">
                    @lang('login.login')
                </x-button>

                @if (Route::has('password.request'))
                    <a class="text-sm font-medium text-[var(--text-muted)] transition hover:text-[var(--primary)]"
                       href="{{ route('password.request') }}">@lang('login.forgot_password')</a>
                @endif
            </div>
        </form>
    </x-card>
</section>

@endsection
