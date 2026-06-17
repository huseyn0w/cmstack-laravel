<?php
/**
 * Laravella CMS
 * File: footer.blade.php
 * Created by Elman (https://linkedin.com/in/huseyn0w)
 * Date: 21.07.2019
 */
?>
<footer class="border-t border-ink-200 px-4 py-5 sm:px-6 lg:px-8">
    <p class="text-center text-xs text-ink-500">
        &copy; {{ now()->year }}
        @lang('cpanel/nav/bottom.made')
        <a href="https://www.linkedin.com/in/huseyn0w/" class="font-medium text-brand-700 hover:text-brand-800">Huseyn0w</a>
        <span class="text-ink-300">&middot;</span>
        Developed by
        <a href="https://elman.group" target="_blank" rel="noopener" class="font-medium text-brand-700 hover:text-brand-800">Elman Group</a>
    </p>
</footer>
@include('cpanel.core.footer-scripts')
