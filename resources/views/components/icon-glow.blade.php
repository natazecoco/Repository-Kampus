{{-- resources/views/components/icon-glow.blade.php --}}
@props(['color' => 'purple'])

@php
    // Mengatur warna ikon dan warna background untuk efek glow
    $theme = match($color) {
        'purple' => ['text' => 'text-purple-600', 'bg' => 'bg-purple-100', 'glow' => 'bg-purple-400'],
        'orange' => ['text' => 'text-orange-600', 'bg' => 'bg-orange-100', 'glow' => 'bg-orange-400'],
        'blue'   => ['text' => 'text-blue-600', 'bg' => 'bg-blue-100', 'glow' => 'bg-blue-400'],
        'gray'   => ['text' => 'text-gray-600', 'bg' => 'bg-gray-100', 'glow' => 'bg-gray-300'],
        default  => ['text' => 'text-gray-600', 'bg' => 'bg-gray-100', 'glow' => 'bg-gray-300'],
    };
@endphp

<div class="relative flex items-center justify-center w-10 h-10 rounded-full {{ $theme['bg'] }} transition-transform duration-300 hover:scale-110">
    {{-- Efek Soft Glow di belakang --}}
    <div class="absolute inset-0 rounded-full blur-md opacity-40 {{ $theme['glow'] }}"></div>
    
    {{-- Tempat Ikon SVG berada --}}
    <div class="relative z-10 w-5 h-5 {{ $theme['text'] }}">
        {{ $slot }}
    </div>
</div>