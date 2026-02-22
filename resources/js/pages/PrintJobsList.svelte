<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import type { BreadcrumbItem } from '@/types';
    import { dashboard } from '@/routes';
    import { Trash2 } from '@lucide/svelte';
    import { page, router } from '@inertiajs/svelte';

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

    const destroyPrintJob = async (uuid: string) => {
        if (!confirm('Are you sure you want to remove this print job?')) {
            return;
        }

        try {
            const response = await fetch(`/print-jobs/${uuid}/delete-files`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $page.props.csrf_token,
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                },
            });

            if (!response.ok) {
                const errorData = await response.json();
                alert(
                    errorData.message ||
                        'Failed to remove print job files. Please try again.',
                );
                return;
            }

            const data = await response.json();

            if (data.status === 'success') {
                alert(data.message);
                router.reload();
            } else {
                alert(
                    data.message ||
                        'Failed to remove print job files. Please try again.',
                );
            }
        } catch (error) {
            console.error('Error removing print job files:', error);
            alert(
                'An error occurred while trying to remove the print job files.',
            );
        }
    };
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
                        <th>Print Code</th>
                        <th>Shop Name</th>
                        <th>Submitted At</th>
                        <th>Attachments</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    {#if print_jobs.length === 0}
                        <tr>
                            <td colspan="6" class="text-center">
                                No records found.
                            </td>
                        </tr>
                    {/if}
                    {#each print_jobs as print_job, index}
                        <tr>
                            <th>{index + 1}</th>
                            <td class="text-md font-semibold">{print_job.print_code}</td>
                            <td>{print_job.shop.name}</td>
                            <td>{print_job.created_at}</td>
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
                                    onclick={() =>
                                        destroyPrintJob(print_job.job_uuid)}
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
