@extends(config('app.template_name').'/index')

@section('content')

<section class="mx-auto max-w-[440px] px-5 py-16 sm:py-20">
    <div class="mb-8 text-center">
        <h1 class="font-serif text-3xl font-semibold tracking-tight text-[var(--text)]">@lang('registration.register_page_headline')</h1>
        <p class="mt-2 text-sm text-[var(--text-muted)]">@lang('registration.register')</p>
    </div>

    <x-card>
        <form method="POST" action="{{ route('register') }}" class="space-y-5" novalidate>
            @csrf

            <x-field
                name="name"
                :label="__('registration.name')"
                :error="$errors->first('name')"
                :required="true"
            >
                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    autocomplete="name"
                    autofocus
                    @if($errors->has('name')) aria-invalid="true" aria-describedby="name-error" @endif
                    class="field-input @error('name') border-[var(--error)] @enderror"
                >
            </x-field>

            <x-field
                name="email"
                :label="__('registration.email')"
                :error="$errors->first('email')"
                :required="true"
            >
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="email"
                    @if($errors->has('email')) aria-invalid="true" aria-describedby="email-error" @endif
                    class="field-input @error('email') border-[var(--error)] @enderror"
                >
            </x-field>

            <x-field
                name="username"
                :label="__('registration.username')"
                :error="$errors->first('username')"
                :required="true"
            >
                <input
                    id="username"
                    type="text"
                    name="username"
                    value="{{ old('username') }}"
                    required
                    @if($errors->has('username')) aria-invalid="true" aria-describedby="username-error" @endif
                    class="field-input @error('username') border-[var(--error)] @enderror"
                >
            </x-field>

            <x-field
                name="password"
                :label="__('registration.password')"
                :error="$errors->first('password')"
                :required="true"
            >
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="new-password"
                    @if($errors->has('password')) aria-invalid="true" aria-describedby="password-error" @endif
                    class="field-input @error('password') border-[var(--error)] @enderror"
                >
            </x-field>

            <x-field
                name="password_confirmation"
                :label="__('registration.confirm_password')"
                :required="true"
            >
                <input
                    id="password-confirm"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    class="field-input"
                >
            </x-field>

            <x-button type="submit" variant="primary" class="w-full">
                @lang('registration.register_btn')
            </x-button>
        </form>

        <div class="mt-6 text-center text-sm text-[var(--text-muted)]">
            <a href="{{ route('login') }}" class="font-medium text-[var(--primary)] transition hover:text-[var(--primary-hover)]">@lang('login.login')</a>
        </div>
    </x-card>
</section>

@endsection
