<script lang="ts">
    import type { Snippet } from 'svelte';
    import AppLayout from '@/layouts/app/AppSidebarLayout.svelte';
    import type { BreadcrumbItem } from '@/types';

    let {
        breadcrumbs = [],
        children,
        pageActions,
    }: {
        breadcrumbs?: BreadcrumbItem[];
        children?: Snippet;
        pageActions?: Snippet;
    } = $props();
</script>

<AppLayout {breadcrumbs} {pageActions}>
    {@render children?.()}
</AppLayout>
