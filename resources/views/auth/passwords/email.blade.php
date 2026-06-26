@extends(config('app.template_name').'/index')

@section('content')

<section class="mx-auto max-w-[440px] px-5 py-16 sm:py-20">
    <div class="mb-8 text-center">
        <h1 class="font-serif text-3xl font-semibold tracking-tight text-[var(--text)]">@lang('custom-passwords.reset_page_headline')</h1>
        <p class="mt-2 text-sm text-[var(--text-muted)]">@lang('custom-passwords.reset_password')</p>
    </div>

    <x-card>
        @if (session('status'))
            <x-alert variant="success" class="mb-6">
                {{ session('status') }}
            </x-alert>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5" novalidate>
            @csrf

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
                    value="{{ old('email') }}"
                    required
                    autocomplete="email"
                    autofocus
                    @if($errors->has('email')) aria-invalid="true" aria-describedby="email-error" @endif
                    class="field-input @error('email') border-[var(--error)] @enderror"
                >
            </x-field>

            <x-button type="submit" variant="primary" class="w-full">
                @lang('custom-passwords.send_password_link')
            </x-button>
        </form>

        <div class="mt-6 text-center text-sm text-[var(--text-muted)]">
            <a href="{{ route('login') }}" class="font-medium text-[var(--primary)] transition hover:text-[var(--primary-hover)]">@lang('login.login')</a>
        </div>
    </x-card>
</section>

@endsection
