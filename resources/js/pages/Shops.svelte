<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import PlaceholderPattern from '@/components/PlaceholderPattern.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import type { BreadcrumbItem } from '@/types';
    import { dashboard } from '@/routes';
    import { QrCode } from '@lucide/svelte';

    let { shops, pagination } = $props();

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: dashboard().url,
        },
        {
            title: 'Shops',
            href: '/shops',
        },
    ];
</script>

<AppHead title="Shops List" />

<AppLayout {breadcrumbs}>
    <div class="h-full overflow-x-auto rounded-xl p-4">
        <div class="overflow-x-auto">
            <table class="table table-zebra table-compact w-full table-sm">
                <!-- head -->
                <thead>
                    <tr>
                        <th></th>
                        <th>Shop Name</th>
                        <th>Shop Owner</th>
                        <th>Registered Email</th>
                        <th>Registered Mobile No</th>
                        <th>Today Stat</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    {#if shops.length === 0}
                        <tr>
                            <td colspan="5" class="text-center">
                                No shops found.
                            </td>
                        </tr>
                    {/if}
                    {#each shops as shop, index}
                        <tr>
                            <th>{index + 1}</th>
                            <td>{shop.name}</td>
                            <td>{shop.owner_name}</td>
                            <td>{shop.email}</td>
                            <td>{shop.mobile_number}</td>
                            <td class="text-center">
                                <div class="text-lg font-semibold">
                                    {shop.today_print_jobs_count}
                                </div></td
                            >
                            <td>
                                <a
                                    href={'#'}
                                    class="tooltip btn btn-sm btn-ghost tooltip-left p-2"
                                    data-tip="View Print QR Code"
                                >
                                    <QrCode size={18} />
                                </a>
                            </td>
                        </tr>
                    {/each}
                </tbody>
            </table>
        </div>
    </div>
</AppLayout>
