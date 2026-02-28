<script lang="ts">
    import type { Snippet } from 'svelte';
    import Breadcrumbs from '@/components/Breadcrumbs.svelte';
    import { SidebarTrigger } from '@/components/ui/sidebar';
    import type { BreadcrumbItem } from '@/types';

    let {
        breadcrumbs = [],
        pageActions,
    }: {
        breadcrumbs?: BreadcrumbItem[];
        pageActions?: Snippet;
    } = $props();
</script>

<header
    class="flex h-16 shrink-0 items-center justify-between gap-2 border-b border-sidebar-border/70 px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
>
    <div class="flex items-center gap-2">
        <SidebarTrigger class="-ml-1" />
        {#if breadcrumbs && breadcrumbs.length > 0}
            <Breadcrumbs {breadcrumbs} />
        {/if}
    </div>
    {#if pageActions}
        <div class="flex items-center gap-2">
            {@render pageActions()}
        </div>
    {/if}
</header>
