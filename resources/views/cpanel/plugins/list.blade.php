@extends('cpanel.core.index')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="mb-6">
            <h1 class="text-xl font-semibold text-ink-900">@lang('cpanel/plugins.headline')</h1>
            <p class="mt-1 text-sm text-ink-500">@lang('cpanel/plugins.intro')</p>
        </div>

        @include('cpanel.core.flash')

        <div class="card">
            <div class="card-body divide-y divide-ink-100">
                @forelse($plugins as $plugin)
                    <div class="flex items-center justify-between gap-4 py-4 first:pt-0 last:pb-0">
                        <div>
                            <div class="font-medium text-ink-900">{{ $plugin['name'] }}</div>
                            <div class="text-sm text-ink-500">{{ $plugin['description'] }}</div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-medium {{ $plugin['enabled'] ? 'text-green-600' : 'text-ink-400' }}">
                                {{ $plugin['enabled'] ? __('cpanel/plugins.enabled') : __('cpanel/plugins.disabled') }}
                            </span>
                            <form action="{{ route('cpanel_toggle_plugin') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="slug" value="{{ $plugin['slug'] }}">
                                <input type="hidden" name="enabled" value="{{ $plugin['enabled'] ? 0 : 1 }}">
                                <button type="submit" class="{{ $plugin['enabled'] ? 'btn-ghost' : 'btn-primary' }} px-4 py-2 text-sm">
                                    {{ $plugin['enabled'] ? __('cpanel/plugins.disable') : __('cpanel/plugins.enable') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="py-4 text-sm text-ink-500">@lang('cpanel/plugins.empty')</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
