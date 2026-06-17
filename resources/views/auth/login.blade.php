@extends(config('app.template_name').'/index')


@section('content')

<section class="mx-auto max-w-md px-5 py-16 sm:py-20">
    <div class="mb-8 text-center">
        <h1 class="font-serif text-3xl font-semibold tracking-tight text-ink-900">@lang('login.login_page_headline')</h1>
        <p class="mt-2 text-sm text-ink-400">@lang('login.login')</p>
    </div>

    <div class="rounded-2xl border border-ink-100 bg-white p-6 shadow-card sm:p-8">
        {{-- Social sign-in --}}
        <p class="mb-3 text-center text-xs font-medium uppercase tracking-wider text-ink-400">@lang('login.login_with')</p>
        @include('auth.social')

        <div class="my-6 flex items-center gap-3 text-xs uppercase tracking-wider text-ink-300">
            <span class="h-px flex-1 bg-ink-100"></span>
            <span>@lang('login.or')</span>
            <span class="h-px flex-1 bg-ink-100"></span>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="field-label">@lang('login.username_or_email')</label>
                <input id="email" type="text"
                       class="field-input @error('username') border-brand-400 @enderror @error('email') border-brand-400 @enderror"
                       name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                @error('email')
                    <p class="mt-1.5 text-sm text-brand-700" role="alert">{{ $message }}</p>
                @enderror
                @error('username')
                    <p class="mt-1.5 text-sm text-brand-700" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="field-label">@lang('login.password')</label>
                <input id="password" type="password"
                       class="field-input @error('password') border-brand-400 @enderror"
                       name="password" required autocomplete="current-password">
                @error('password')
                    <p class="mt-1.5 text-sm text-brand-700" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <label for="remember" class="flex cursor-pointer items-center gap-2.5 text-sm text-ink-600">
                <input class="rounded border-ink-300 text-brand-600 focus:ring-brand-500"
                       type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                @lang('login.remember_me')
            </label>

            <div class="flex items-center justify-between gap-4 pt-2">
                <button type="submit" class="btn-primary">@lang('login.login')</button>

                @if (Route::has('password.request'))
                    <a class="text-sm font-medium text-ink-500 transition hover:text-brand-700"
                       href="{{ route('password.request') }}">@lang('login.forgot_password')</a>
                @endif
            </div>
        </form>
    </div>
</section>
@endsection
