@extends(config('app.template_name').'/index')

@section('content')

<section class="mx-auto max-w-[440px] px-5 py-16 sm:py-20">
    <div class="mb-8 text-center">
        <h1 class="font-serif text-3xl font-semibold tracking-tight text-[var(--text)]">@lang('custom-passwords.reset_page_headline')</h1>
        <p class="mt-2 text-sm text-[var(--text-muted)]">@lang('custom-passwords.reset_password')</p>
    </div>

    <x-card>
        <form method="POST" action="{{ route('password.update') }}" class="space-y-5" novalidate>
            @csrf

            {{-- Hidden token — MUST be preserved for the password-reset flow --}}
            <input type="hidden" name="token" value="{{ $token }}">

            <x-field
                name="email"
                :label="__('custom-passwords.email')"
                :error="$errors->first('email')"
                :required="true"
            >
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ $email ?? old('email') }}"
                    required
                    autocomplete="email"
                    autofocus
                    @if($errors->has('email')) aria-invalid="true" aria-describedby="email-error" @endif
                    class="field-input @error('email') border-[var(--error)] @enderror"
                >
            </x-field>

            <x-field
                name="password"
                :label="__('custom-passwords.password')"
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
                :label="__('custom-passwords.confirm_password')"
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
                @lang('custom-passwords.reset_password_btn')
            </x-button>
        </form>
    </x-card>
</section>

@endsection
