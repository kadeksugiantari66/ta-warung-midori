@if (session('success'))
    <div class="mb-6 flex items-center gap-3 p-4 bg-primary-fixed text-on-primary-fixed rounded-2xl text-sm font-medium">
        <span class="material-symbols-outlined text-base" style="font-variation-settings:'FILL' 1">check_circle</span>
        {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div class="mb-6 flex items-center gap-3 p-4 bg-error-container text-on-error-container rounded-2xl text-sm font-medium">
        <span class="material-symbols-outlined text-base" style="font-variation-settings:'FILL' 1">error</span>
        {{ session('error') }}
    </div>
@endif
