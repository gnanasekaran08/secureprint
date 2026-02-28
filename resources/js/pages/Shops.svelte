<script lang="ts">
    import { Plus, QrCode } from '@lucide/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import Pagination from '@/components/Pagination.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { dashboard } from '@/routes';
    import type { BreadcrumbItem } from '@/types';
    import ShowQRModal from './Modals/ShowQRModal.svelte';
    import { PlusSquare } from 'lucide-svelte';

    let { shops } = $props();
    let selectedShop = $state(null);

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

    const showQRCode = (shop: any) => {
        selectedShop = shop;
        console.log('Show QR code for shop:', shop);
    };
</script>

<AppHead title="Shops List" />

<AppLayout {breadcrumbs}>
    {#snippet pageActions()}
        <button class="btn"><PlusSquare /> Add Shop</button>
    {/snippet}
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
                    {#if shops?.data?.length === 0}
                        <tr>
                            <td colspan="5" class="text-center">
                                No shops found.
                            </td>
                        </tr>
                    {/if}
                    {#each shops?.data || [] as shop, index}
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
                                    onclick={() => showQRCode(shop)}
                                >
                                    <QrCode size={18} />
                                </a>
                            </td>
                        </tr>
                    {/each}
                    {#if shops?.last_page > 1}
                        <tr>
                            <td colspan={7} class="text-end">
                                <Pagination
                                    links={shops?.links || []}
                                    total={shops?.total || 0}
                                />
                            </td>
                        </tr>
                    {/if}
                </tbody>
            </table>
        </div>
    </div>
</AppLayout>

{#if selectedShop}
    <ShowQRModal shop={selectedShop} onClose={() => (selectedShop = null)} />
{/if}
