@extends(config('app.template_name').'/index')

@section('content')

<section class="mx-auto max-w-[440px] px-5 py-16 sm:py-20">
    <div class="mb-8 text-center">
        <h1 class="font-serif text-3xl font-semibold tracking-tight text-[var(--text)]">@lang('email.verify_page_headline')</h1>
    </div>

    <x-card>
        @if (session('resent'))
            <x-alert variant="success" class="mb-6">
                @lang('email.fresh_link')
            </x-alert>
        @endif

        <p class="text-sm text-[var(--text-muted)]">
            @lang('email.check_email')
            @lang('email.not_receive_email'),
            <a href="{{ route('verification.resend') }}"
               class="font-medium text-[var(--primary)] transition hover:text-[var(--primary-hover)]">@lang('email.request_other_email')</a>.
        </p>
    </x-card>
</section>

@endsection
