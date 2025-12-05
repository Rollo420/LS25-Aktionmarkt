<div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl p-6 flex flex-col justify-center items-center transform hover:scale-[1.02] transition duration-150 !important">
    @if($title)
        <div class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase tracking-wider !important">{{ $title }}</div>
    @endif

    @if($value)
        <div class="text-4xl font-extrabold mt-2 {{ $color }} !important">{{ $value }}</div>
    @endif

    @if($subtitle)
        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 !important">{{ $subtitle }}</div>
    @endif

    {!! $extra !!}
</div>
