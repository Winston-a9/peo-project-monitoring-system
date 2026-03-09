@props([
    'id'    => 'modal',
    'title' => 'Modal',
    'type'  => 'default',
    'icon'  => 'fa-circle-info',
    'size'  => 'md',
])

@php
    $maxWidth = match($size) {
        'sm'    => '420px',
        'lg'    => '680px',
        'xl'    => '860px',
        default => '540px',
    };
    $accentColor = match($type) {
        'danger'  => '#dc2626',
        'success' => '#16a34a',
        'indigo'  => '#6366f1',
        default   => '#f97316',
    };
@endphp

{{-- Backdrop --}}
<div id="{{ $id }}-backdrop"
     style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5);
            z-index:9998; transition:opacity 0.2s; opacity:0;"
     onclick="closeModal('{{ $id }}')">
</div>

{{-- Panel --}}
<div id="{{ $id }}-panel"
     style="display:none; position:fixed; top:50%; left:50%;
            transform:translate(-50%,-50%) scale(0.95);
            width:calc(100% - 2rem); max-width:{{ $maxWidth }};
            background:var(--bg-primary, #fff);
            border:1px solid var(--border, rgba(0,0,0,0.1));
            border-radius:16px; overflow:hidden;
            box-shadow:0 20px 60px rgba(0,0,0,0.2);
            z-index:9999; opacity:0;
            transition:opacity 0.22s ease, transform 0.22s ease;">

    {{-- Top accent stripe --}}
    <div style="height:3px; background:{{ $accentColor }};"></div>

    {{-- Header --}}
    <div style="display:flex; align-items:center; gap:0.75rem;
                padding:1rem 1.25rem; border-bottom:1px solid var(--border, rgba(0,0,0,0.1));
                background:var(--bg-secondary, #f9fafb);">
        <i class="fas {{ $icon }}" style="color:{{ $accentColor }}; font-size:0.9rem;"></i>
        <h3 id="{{ $id }}-title"
            style="font-weight:700; font-size:0.95rem; margin:0; flex:1;
                   color:var(--text-primary, #111);">
            {{ $title }}
        </h3>
        <button onclick="closeModal('{{ $id }}')"
                style="background:none; border:none; cursor:pointer;
                       color:var(--text-secondary, #666); font-size:1rem; padding:0.2rem;">
            <i class="fas fa-times"></i>
        </button>
    </div>

    {{-- Body --}}
    <div style="padding:1.25rem; overflow-y:auto; max-height:70vh;">
        {{ $slot }}
    </div>

    {{-- Footer --}}
    @isset($footer)
    <div style="display:flex; justify-content:flex-end; gap:0.6rem;
                padding:0.875rem 1.25rem;
                border-top:1px solid var(--border, rgba(0,0,0,0.1));
                background:var(--bg-secondary, #f9fafb);">
        {{ $footer }}
    </div>
    @endisset
</div>