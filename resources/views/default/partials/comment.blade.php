<?php
/**
 * LaraPress CMS — single comment partial (Phase 4).
 *
 * Rendered inside the x-data="commentThread(...)" scope on the post page, so
 * the reply / edit / delete buttons can call its Alpine methods directly.
 *
 * @param object   $comment
 * @param int|null $user_id   Current user id (null when guest).
 * @param bool     $is_reply  Whether this is a nested reply.
 */
$avatar = image_src($comment->user->avatar ?? null, true);
?>
<article data-comment-card class="flex gap-4">
    <img src="{{$avatar}}" {!! image_fallback(true) !!} alt="{{$comment->user->name}}" class="h-10 w-10 shrink-0 rounded-full object-cover ring-1 ring-ink-100">
    <div class="min-w-0 flex-1">
        <div class="flex flex-wrap items-baseline gap-x-3 gap-y-1">
            <a href="{{route('show_user',['username' => $comment->user->username])}}" class="font-serif text-base font-medium text-ink-900 hover:text-brand-700">{{$comment->user->name}}</a>
            <span class="text-xs text-ink-400">{{$comment->created_at->format('d.m.Y')}}</span>
        </div>
        <p class="mt-1.5 text-base leading-relaxed text-ink-700">{{$comment->comment}}</p>

        @auth
            <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm">
                @if($user_id !== $comment->user->id)
                    <button type="button"
                            data-comment-id="{{$comment->id}}"
                            data-username="{{$comment->user->name}}"
                            @click="reply('{{ addslashes($comment->user->name) }}')"
                            class="font-medium text-ink-500 transition-colors hover:text-brand-700">@lang('default/post.reply')</button>
                @endif
                @if($comment->user->id === $user_id)
                    <button type="button"
                            data-comment-id="{{$comment->id}}"
                            data-comment="{{ $comment->comment }}"
                            @click="edit()"
                            class="font-medium text-ink-500 transition-colors hover:text-brand-700">@lang('default/post.edit')</button>
                @endif
                @if( (\Auth()->user()->role->id == 1) || ($comment->user->id === $user_id) )
                    <button type="button"
                            data-comment-id="{{$comment->id}}"
                            data-username="{{$comment->user->name}}"
                            @click="remove()"
                            class="font-medium text-ink-400 transition-colors hover:text-brand-700">@lang('default/post.delete')</button>
                @endif
            </div>
        @endauth
    </div>
</article>
