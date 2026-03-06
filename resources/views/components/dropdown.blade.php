<<<<<<< HEAD
@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-1 bg-white dark:bg-gray-700'])
=======
@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700'])
>>>>>>> 89caed72e1a46b970403232f253207870b3ea870

@php
$alignmentClasses = match ($align) {
    'left' => 'ltr:origin-top-left rtl:origin-top-right start-0',
    'top' => 'origin-top',
    default => 'ltr:origin-top-right rtl:origin-top-left end-0',
};

$width = match ($width) {
    '48' => 'w-48',
    default => $width,
};
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    <div @click="open = ! open">
        {{ $trigger }}
    </div>

    <div x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
<<<<<<< HEAD
            class="absolute z-50 mt-2 {{ $width }} rounded-md shadow-lg {{ $alignmentClasses }}"
            style="display: none;"
            @click="open = false">
        <div class="rounded-md ring-1 ring-black ring-opacity-5 {{ $contentClasses }}">
=======
            class="absolute z-50 mt-2 {{ $width }} rounded-lg shadow-xl {{ $alignmentClasses }}"
            style="display: none;"
            @click="open = false">
        <div class="rounded-lg ring-1 ring-gray-300 dark:ring-gray-600 {{ $contentClasses }}">
>>>>>>> 89caed72e1a46b970403232f253207870b3ea870
            {{ $content }}
        </div>
    </div>
</div>
