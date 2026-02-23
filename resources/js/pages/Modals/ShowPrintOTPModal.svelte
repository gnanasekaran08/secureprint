<script lang="ts">
    import { onMount } from 'svelte';
    let { printJobUuid, close } = $props();

    onMount(() => {});
</script>

<dialog class="modal" open>
    <div class="modal-box">
        <form method="dialog">
            <button
                class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2"
                onclick={close}>✕</button
            >
        </form>
        <h3 class="text-lg font-bold">OTP</h3>
        <p class="py-4">Press ESC key or click on ✕ button to close</p>
        <div></div>
    </div>
</dialog>
