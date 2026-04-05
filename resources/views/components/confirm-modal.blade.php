{{-- Confirm Delete Modal --}}
<div id="confirmModal"
     class="fixed inset-0 z-50 hidden items-center justify-center"
     aria-modal="true" role="dialog">

    {{-- Backdrop --}}
    <div id="confirmBackdrop"
         class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity duration-200 opacity-0"></div>

    {{-- Dialog --}}
    <div id="confirmDialog"
         class="relative z-10 w-full max-w-sm mx-4 bg-surface rounded-[1.75rem] shadow-2xl
                border border-outline-variant/20 p-6
                translate-y-4 opacity-0 transition-all duration-200">

        <div class="flex items-start gap-4 mb-5">
            <div class="w-10 h-10 rounded-full bg-error-container flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-error" style="font-size:20px">delete_forever</span>
            </div>
            <div>
                <h3 class="font-headline font-bold text-lg text-on-surface">Konfirmasi Hapus</h3>
                <p id="confirmMessage" class="text-sm text-on-surface-variant mt-0.5"></p>
            </div>
        </div>

        <div class="flex justify-end gap-2">
            <button id="confirmCancel"
                    class="px-4 py-2 text-sm font-semibold rounded-xl text-on-surface-variant
                           hover:bg-surface-container transition-colors">
                Batal
            </button>
            <button id="confirmOk"
                    class="px-5 py-2 text-sm font-semibold rounded-xl bg-error text-on-error
                           hover:opacity-90 active:scale-95 transition-all">
                Hapus
            </button>
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
(function () {
    const modal    = document.getElementById('confirmModal');
    const backdrop = document.getElementById('confirmBackdrop');
    const dialog   = document.getElementById('confirmDialog');
    const message  = document.getElementById('confirmMessage');
    const btnOk    = document.getElementById('confirmOk');
    const btnCancel= document.getElementById('confirmCancel');
    let pendingForm = null;

    function openModal(msg, form) {
        pendingForm = form;
        message.textContent = msg;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        requestAnimationFrame(() => {
            backdrop.classList.remove('opacity-0');
            dialog.classList.remove('translate-y-4', 'opacity-0');
        });
    }

    function closeModal() {
        backdrop.classList.add('opacity-0');
        dialog.classList.add('translate-y-4', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            pendingForm = null;
        }, 200);
    }

    // Intercept all delete forms with data-confirm
    document.addEventListener('submit', function (e) {
        const form = e.target;
        const msg  = form.dataset.confirm;
        if (!msg) return;
        e.preventDefault();
        openModal(msg, form);
    });

    btnOk.addEventListener('click', () => {
        if (pendingForm) {
            pendingForm.removeAttribute('data-confirm');
            pendingForm.submit();
        }
        closeModal();
    });

    btnCancel.addEventListener('click', closeModal);
    backdrop.addEventListener('click', closeModal);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeModal();
    });
})();
</script>
@endpush
@endonce
