<script lang="ts">
    import { inertia } from '@inertiajs/svelte';

    let { links } = $props();
</script>

{#if links.length > 3}
    <nav aria-label="pagination navigation">
        <div class="join">
            {#each links as link}
                <button
                    class="join-item btn btn-sm mx-1"
                    class:btn-active={link.active}
                    use:inertia
                    onclick={() => (window.location.href = link.url)}
                    aria-label={`Goto page ${link.label}`}
                    disabled={!link.url}
                >
                    {@html link.label}
                </button>
            {/each}
        </div>
    </nav>
{/if}
<!--
<style>
    .btn-active {
        background-color: #c01c7b;
        border-color: #c01c7b;
        color: #fff;
    }
</style> -->
