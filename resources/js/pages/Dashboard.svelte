<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import PlaceholderPattern from '@/components/PlaceholderPattern.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import type { BreadcrumbItem } from '@/types';
    import { dashboard } from '@/routes';

    let { data } = $props();

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: dashboard().url,
        },
    ];
</script>

<AppHead title="Dashboard" />

<AppLayout {breadcrumbs}>
    <div
        class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
    >
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="card card-sm shadow-sm">
                <div class="card-body">
                    <h2 class="card-title">Overall Shops</h2>
                    <div class="justify-end card-actions">
                        <button class="text-4xl"
                            >{data?.overall_shops_count || 0}</button
                        >
                    </div>
                </div>
            </div>
            <div class="card card-sm shadow-sm">
                <div class="card-body">
                    <h2 class="card-title">Todays Prints</h2>
                    <div class="justify-end card-actions">
                        <button class="text-4xl"
                            >{data?.today_prints_count || 0}</button
                        >
                    </div>
                </div>
            </div>
        </div>
        <div
            class="relative min-h-screen flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border"
        >
            <PlaceholderPattern />
        </div>
    </div>
</AppLayout>
