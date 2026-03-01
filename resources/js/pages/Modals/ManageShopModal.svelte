<script lang="ts">
    import { page } from '@inertiajs/svelte';
    import { X } from '@lucide/svelte';
    import { onMount } from 'svelte';

    let { shop, onClose } = $props();

    onMount(async () => {
        console.log('Shop data in ManageShopModal:', shop);
    });
</script>

<dialog class="modal modal-open">
    <div class="modal-box max-w-md">
        <button
            class="btn btn-sm btn-circle btn-ghost absolute right-3 top-3"
            onclick={onClose}
        >
            <X class="h-4 w-4" />
        </button>

        <div class="p-2">
            <div class="w-full my-2">
                <div class="">Name Of the Shop</div>
                <input
                    type="text"
                    placeholder="Type here"
                    class="input w-full"
                />
            </div>

            <div class="w-full my-2">
                <div class="">Shop Number</div>
                <input
                    type="text"
                    placeholder="Type here"
                    class="input w-full"
                />
            </div>

            <div class="w-full my-2">
                <div class="">Shop Email</div>
                <input
                    type="text"
                    placeholder="Type here"
                    class="input w-full"
                />
            </div>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button onclick={onClose}>close</button>
    </form>
</dialog>
