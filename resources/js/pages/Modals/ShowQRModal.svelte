<script lang="ts">
    import { X } from 'lucide-svelte';
    import { createQrPngDataUrl } from '@svelte-put/qr';
    import { onMount } from 'svelte';
    import QR from '@svelte-put/qr/svg/QR.svelte';

    let { shop, onClose } = $props();



    const config = {
        data: shop.qr_code_url,
        width: 500,
        height: 500,
        backgroundFill: '#fff',
    };

    let src = '';
    onMount(async () => {
        src = await createQrPngDataUrl(config);
    });
</script>

<dialog class="modal modal-open">
    <div class="modal-box max-w-sm">
        <button
            class="btn btn-sm btn-circle btn-ghost absolute right-3 top-3"
            onclick={onClose}
        >
            <X class="h-4 w-4" />
        </button>
        <div class="pt-2 text-center">
            <h3 class="text-lg font-bold">QR code for uploading doc</h3>
            <p class="text-sm text-gray-500 mt-1">
                Scan this QR code to access the shop's page.
            </p>
            <div class="mt-4 flex justify-center">
                <QR data={shop.qr_code_url} />
            </div>
            <div>
                <a
                    href={src}
                    download="shop-printing-qr.png"
                    class="btn btn-sm mt-4 btn-secondary"
                >
                    Download QR Code
                </a>
            </div>
        </div>
    </div>
</dialog>
