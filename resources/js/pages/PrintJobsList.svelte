<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import type { BreadcrumbItem } from '@/types';
    import { dashboard } from '@/routes';
    import { Trash2 } from '@lucide/svelte';

    let { print_jobs, pagination } = $props();

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: dashboard().url,
        },
        {
            title: 'Print Jobs',
            href: '/print-jobs',
        },
    ];
</script>

<AppHead title="Print Jobs List" />

<AppLayout {breadcrumbs}>
    <div class="h-full overflow-x-auto rounded-xl p-4">
        <div class="overflow-x-auto">
            <table class="table table-zebra table-compact w-full table-sm">
                <!-- head -->
                <thead>
                    <tr>
                        <th></th>
                        <th>Shop Name</th>
                        <th>Attachements</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    {#if print_jobs.length === 0}
                        <tr>
                            <td colspan="5" class="text-center">
                                No records found.
                            </td>
                        </tr>
                    {/if}
                    {#each print_jobs as print_job, index}
                        <tr>
                            <th>{index + 1}</th>
                            <td>{print_job.shop.name}</td>
                            <td>
                                <div class="flex gap-2">
                                    {#each print_job.attachments as attachment}
                                        <span
                                            class="badge badge-soft badge-primary text-xs px-2"
                                            class:badge-secondary={'pdf' ===
                                                attachment.filetype ||
                                                'application/pdf' ===
                                                    attachment.filetype}
                                            >{attachment.filename}</span
                                        >
                                    {/each}
                                </div>
                            </td>
                            <td>
                                <a
                                    href={'#'}
                                    class="text-red-700 tooltip tooltip-left"
                                    data-tip="Remove Print Job Files"
                                >
                                    <Trash2 size={18} />
                                </a>
                            </td>
                        </tr>
                    {/each}
                </tbody>
            </table>
        </div>
    </div>
</AppLayout>
