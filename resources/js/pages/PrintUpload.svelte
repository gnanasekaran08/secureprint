<script lang="ts">
    import { page, router } from '@inertiajs/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import {
        Upload,
        FileText,
        Trash2,
        Plus,
        Minus,
        CheckCircle,
        Printer,
        Copy,
        Palette,
        BookOpen,
        ShoppingCart,
        CreditCard,
        Smartphone,
        ArrowLeft,
        Loader2,
        X,
        Check,
        ChevronRight,
        FileStack,
    } from 'lucide-svelte';

    let {
        shop = null,
        shopUuid = null,
    }: {
        shop: { id: number; name: string; uuid: string } | null;
        shopUuid: string | null;
    } = $props();

    // State
    let currentStep = $state<'upload' | 'settings' | 'checkout' | 'success'>('upload');
    let files = $state<File[]>([]);
    let copies = $state(1);
    let isColor = $state(false);
    let isDoubleSided = $state(false);
    let isUploading = $state(false);
    let isProcessingPayment = $state(false);
    let printJob = $state<any>(null);
    let paymentOtp = $state<string | null>(null);

    // Bottom sheet states
    let showColorSheet = $state(false);
    let showDoubleSidedSheet = $state(false);

    // Pricing
    const pricePerPageBW = 0.05;
    const pricePerPageColor = 0.15;
    const doubleSidedDiscount = 0.1;

    // Computed
    const estimatedPages = $derived(
        files.reduce((acc, file) => {
            const ext = file.name.split('.').pop()?.toLowerCase();
            if (['jpg', 'jpeg', 'png'].includes(ext || '')) return acc + 1;
            if (ext === 'pdf') return acc + Math.max(1, Math.ceil(file.size / 1024 / 100));
            return acc + Math.max(1, Math.ceil(file.size / 1024 / 50));
        }, 0)
    );

    const subtotal = $derived(estimatedPages * copies * (isColor ? pricePerPageColor : pricePerPageBW));
    const discount = $derived(isDoubleSided ? subtotal * doubleSidedDiscount : 0);
    const total = $derived(subtotal - discount);

    // Functions
    function handleFileSelect(event: Event) {
        const input = event.target as HTMLInputElement;
        if (input.files) {
            files = [...files, ...Array.from(input.files)];
        }
        input.value = '';
    }

    function removeFile(index: number) {
        files = files.filter((_, i) => i !== index);
    }

    function formatFileSize(bytes: number): string {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }

    async function uploadFiles() {
        if (files.length === 0) return;

        isUploading = true;

        const formData = new FormData();
        files.forEach((file) => formData.append('files[]', file));
        formData.append('shop_uuid', shopUuid || '');
        formData.append('copies', copies.toString());
        formData.append('is_color', isColor ? '1' : '0');
        formData.append('is_double_sided', isDoubleSided ? '1' : '0');

        try {
            const response = await fetch('/print/upload', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content,
                    Accept: 'application/json',
                },
            });

            const data = await response.json();

            if (data.success) {
                printJob = data.print_job;
                currentStep = 'checkout';
            }
        } catch (err) {
            console.error('Upload failed:', err);
        } finally {
            isUploading = false;
        }
    }

    async function processPayment(method: string) {
        if (!printJob) return;

        isProcessingPayment = true;

        try {
            const response = await fetch('/print/pay', {
                method: 'POST',
                body: JSON.stringify({
                    print_job_id: printJob.id,
                    payment_method: method,
                }),
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content,
                    Accept: 'application/json',
                },
            });

            const data = await response.json();

            if (data.success) {
                paymentOtp = data.otp;
                currentStep = 'success';
            }
        } catch (err) {
            console.error('Payment failed:', err);
        } finally {
            isProcessingPayment = false;
        }
    }

    function goBack() {
        if (currentStep === 'settings') currentStep = 'upload';
        else if (currentStep === 'checkout') currentStep = 'settings';
    }
</script>

<AppHead title="Print Documents">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
</AppHead>

<div data-theme="secureprint" class="min-h-screen bg-gradient-to-b from-slate-50 to-white">
    <!-- Header -->
    <header class="sticky top-0 z-50 border-b border-slate-200/50 bg-white/80 px-4 py-3 backdrop-blur-xl">
        <div class="mx-auto flex max-w-lg items-center gap-3">
            {#if currentStep !== 'upload' && currentStep !== 'success'}
                <button onclick={goBack} class="btn btn-circle btn-ghost btn-sm">
                    <ArrowLeft class="h-5 w-5" />
                </button>
            {/if}
            <a href="/" class="flex items-center gap-2">
                <div
                    class="flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-violet-500 to-indigo-600 shadow-md"
                >
                    <Printer class="h-4 w-4 text-white" />
                </div>
                <span class="bg-gradient-to-r from-violet-600 to-indigo-600 bg-clip-text text-lg font-bold text-transparent">
                    SecurePrint
                </span>
            </a>
            {#if shop}
                <div class="ml-auto text-right">
                    <p class="text-xs text-slate-500">Printing at</p>
                    <p class="text-sm font-semibold text-slate-800">{shop.name}</p>
                </div>
            {/if}
        </div>
    </header>

    <main class="mx-auto max-w-lg px-4 py-6">
        <!-- Step Indicator -->
        {#if currentStep !== 'success'}
            <div class="mb-8 flex items-center justify-center gap-2">
                {#each ['upload', 'settings', 'checkout'] as step, i}
                    {@const isActive = ['upload', 'settings', 'checkout'].indexOf(currentStep) >= i}
                    {@const isCurrent = step === currentStep}
                    <div
                        class="flex h-2 w-12 rounded-full transition-all {isActive
                            ? 'bg-gradient-to-r from-violet-500 to-indigo-600'
                            : 'bg-slate-200'} {isCurrent ? 'scale-110' : ''}"
                    ></div>
                {/each}
            </div>
        {/if}

        <!-- Step: Upload Files -->
        {#if currentStep === 'upload'}
            <div class="space-y-6">
                <div class="text-center">
                    <div
                        class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-orange-500 to-pink-500 shadow-lg"
                    >
                        <Upload class="h-8 w-8 text-white" />
                    </div>
                    <h1 class="text-2xl font-bold text-slate-800">Upload Documents</h1>
                    <p class="mt-2 text-slate-600">Select files you want to print</p>
                </div>

                <!-- Upload Area -->
                <label
                    class="flex min-h-40 cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 p-6 transition-colors hover:border-violet-400 hover:bg-violet-50"
                >
                    <input
                        type="file"
                        multiple
                        accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                        onchange={handleFileSelect}
                        class="hidden"
                    />
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-violet-100">
                        <Plus class="h-6 w-6 text-violet-600" />
                    </div>
                    <p class="mt-3 font-medium text-slate-700">Tap to select files</p>
                    <p class="mt-1 text-sm text-slate-500">PDF, DOC, JPG, PNG (max 10MB each)</p>
                </label>

                <!-- File List -->
                {#if files.length > 0}
                    <div class="space-y-3">
                        <h3 class="font-semibold text-slate-800">Selected Files ({files.length})</h3>
                        {#each files as file, index}
                            <div
                                class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white p-3 shadow-sm"
                            >
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-slate-100">
                                    <FileText class="h-5 w-5 text-slate-600" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium text-slate-800">{file.name}</p>
                                    <p class="text-xs text-slate-500">{formatFileSize(file.size)}</p>
                                </div>
                                <button onclick={() => removeFile(index)} class="btn btn-circle btn-ghost btn-sm text-red-500">
                                    <Trash2 class="h-4 w-4" />
                                </button>
                            </div>
                        {/each}
                    </div>

                    <button
                        onclick={() => (currentStep = 'settings')}
                        class="btn btn-lg w-full gap-2 bg-gradient-to-r from-violet-500 to-indigo-600 text-white shadow-lg"
                    >
                        Continue
                        <span class="badge badge-sm bg-white/20 text-white">{estimatedPages} pages</span>
                    </button>
                {/if}
            </div>
        {/if}

        <!-- Step: Print Settings -->
        {#if currentStep === 'settings'}
            <div class="space-y-6">
                <div class="text-center">
                    <div
                        class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-500 shadow-lg"
                    >
                        <Printer class="h-8 w-8 text-white" />
                    </div>
                    <h1 class="text-2xl font-bold text-slate-800">Print Settings</h1>
                    <p class="mt-2 text-slate-600">Customize your print options</p>
                </div>

                <!-- Settings Cards -->
                <div class="space-y-4">
                    <!-- Copies -->
                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-100">
                                <Copy class="h-5 w-5 text-violet-600" />
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-slate-800">Number of Copies</p>
                                <p class="text-sm text-slate-500">How many copies do you need?</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button
                                    onclick={() => (copies = Math.max(1, copies - 1))}
                                    class="btn btn-circle btn-outline btn-sm"
                                >
                                    <Minus class="h-4 w-4" />
                                </button>
                                <span class="w-8 text-center text-lg font-bold">{copies}</span>
                                <button
                                    onclick={() => (copies = Math.min(100, copies + 1))}
                                    class="btn btn-circle btn-outline btn-sm"
                                >
                                    <Plus class="h-4 w-4" />
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Color -->
                    <button
                        onclick={() => (showColorSheet = true)}
                        class="w-full rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition-all hover:border-violet-300 hover:shadow-md active:scale-[0.98]"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl {isColor
                                    ? 'bg-gradient-to-br from-orange-400 to-pink-500'
                                    : 'bg-slate-200'}"
                            >
                                <Palette class="h-5 w-5 {isColor ? 'text-white' : 'text-slate-600'}" />
                            </div>
                            <div class="flex-1 text-left">
                                <p class="font-semibold text-slate-800">Color Printing</p>
                                <p class="text-sm text-slate-500">
                                    {isColor ? 'Color • $0.15/page' : 'Black & White • $0.05/page'}
                                </p>
                            </div>
                            <ChevronRight class="h-5 w-5 text-slate-400" />
                        </div>
                    </button>

                    <!-- Double Sided -->
                    <button
                        onclick={() => (showDoubleSidedSheet = true)}
                        class="w-full rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition-all hover:border-violet-300 hover:shadow-md active:scale-[0.98]"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl {isDoubleSided
                                    ? 'bg-gradient-to-br from-emerald-500 to-teal-500'
                                    : 'bg-slate-200'}"
                            >
                                <BookOpen class="h-5 w-5 {isDoubleSided ? 'text-white' : 'text-slate-600'}" />
                            </div>
                            <div class="flex-1 text-left">
                                <p class="font-semibold text-slate-800">Double Sided</p>
                                <p class="text-sm text-slate-500">
                                    {isDoubleSided ? 'Double sided • 10% off' : 'Single sided'}
                                </p>
                            </div>
                            <ChevronRight class="h-5 w-5 text-slate-400" />
                        </div>
                    </button>
                </div>

                <!-- Summary Preview -->
                <div class="rounded-2xl bg-gradient-to-br from-slate-100 to-slate-50 p-4">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">Estimated Total</span>
                        <span class="text-2xl font-bold text-slate-800">${total.toFixed(2)}</span>
                    </div>
                    <p class="mt-1 text-sm text-slate-500">
                        {estimatedPages} pages × {copies} copies
                    </p>
                </div>

                <button
                    onclick={uploadFiles}
                    disabled={isUploading}
                    class="btn btn-lg w-full gap-2 bg-gradient-to-r from-violet-500 to-indigo-600 text-white shadow-lg disabled:opacity-50"
                >
                    {#if isUploading}
                        <Loader2 class="h-5 w-5 animate-spin" />
                        Uploading...
                    {:else}
                        <ShoppingCart class="h-5 w-5" />
                        Proceed to Checkout
                    {/if}
                </button>
            </div>
        {/if}

        <!-- Step: Checkout -->
        {#if currentStep === 'checkout' && printJob}
            <div class="space-y-6">
                <div class="text-center">
                    <div
                        class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-violet-500 to-indigo-600 shadow-lg"
                    >
                        <ShoppingCart class="h-8 w-8 text-white" />
                    </div>
                    <h1 class="text-2xl font-bold text-slate-800">Checkout</h1>
                    <p class="mt-2 text-slate-600">Review and pay for your print job</p>
                </div>

                <!-- Order Summary -->
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 class="mb-4 font-semibold text-slate-800">Order Summary</h3>

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-600">Files</span>
                            <span class="font-medium">{printJob.attachments?.length || files.length} file(s)</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-600">Total Pages</span>
                            <span class="font-medium">{printJob.total_pages}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-600">Copies</span>
                            <span class="font-medium">{printJob.total_copies}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-600">Print Type</span>
                            <span class="font-medium">{printJob.is_color ? 'Color' : 'Black & White'}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-600">Double Sided</span>
                            <span class="font-medium">{printJob.is_double_sided ? 'Yes' : 'No'}</span>
                        </div>

                        <div class="my-3 border-t border-slate-200"></div>

                        {#if printJob.is_double_sided}
                            <div class="flex justify-between text-slate-600">
                                <span>Subtotal</span>
                                <span>${(printJob.total_cost / 0.9).toFixed(2)}</span>
                            </div>
                            <div class="flex justify-between text-emerald-600">
                                <span>Double-sided Discount (10%)</span>
                                <span>-${((printJob.total_cost / 0.9) * 0.1).toFixed(2)}</span>
                            </div>
                        {/if}

                        <div class="flex justify-between text-lg font-bold">
                            <span>Total</span>
                            <span class="text-violet-600">${printJob.total_cost}</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="space-y-3">
                    <h3 class="font-semibold text-slate-800">Select Payment Method</h3>

                    <!-- GPay Button -->
                    <button
                        onclick={() => processPayment('gpay')}
                        disabled={isProcessingPayment}
                        class="btn btn-lg w-full gap-3 border-2 border-slate-200 bg-white text-slate-800 hover:border-violet-300 hover:bg-violet-50"
                    >
                        {#if isProcessingPayment}
                            <Loader2 class="h-5 w-5 animate-spin" />
                        {:else}
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                                    fill="#4285F4"
                                />
                                <path
                                    d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                                    fill="#34A853"
                                />
                                <path
                                    d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                                    fill="#FBBC05"
                                />
                                <path
                                    d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                                    fill="#EA4335"
                                />
                            </svg>
                            Pay with Google Pay
                        {/if}
                    </button>

                    <!-- UPI / Phone Pay -->
                    <button
                        onclick={() => processPayment('gpay')}
                        disabled={isProcessingPayment}
                        class="btn btn-lg w-full gap-3 border-2 border-slate-200 bg-white text-slate-800 hover:border-violet-300 hover:bg-violet-50"
                    >
                        <Smartphone class="h-5 w-5 text-violet-600" />
                        Pay with UPI
                    </button>

                    <!-- Card -->
                    <button
                        onclick={() => processPayment('card')}
                        disabled={isProcessingPayment}
                        class="btn btn-lg w-full gap-3 border-2 border-slate-200 bg-white text-slate-800 hover:border-violet-300 hover:bg-violet-50"
                    >
                        <CreditCard class="h-5 w-5 text-slate-600" />
                        Pay with Card
                    </button>

                    {#if shop}
                        <button
                            onclick={() => processPayment('cash')}
                            disabled={isProcessingPayment}
                            class="btn btn-outline btn-lg w-full gap-3"
                        >
                            Pay at Shop (Cash)
                        </button>
                    {/if}
                </div>
            </div>
        {/if}

        <!-- Step: Success -->
        {#if currentStep === 'success'}
            <div class="space-y-8 py-8 text-center">
                <div
                    class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-teal-500 shadow-xl"
                >
                    <CheckCircle class="h-12 w-12 text-white" />
                </div>

                <div>
                    <h1 class="text-3xl font-bold text-slate-800">Payment Successful!</h1>
                    <p class="mt-2 text-slate-600">Your print job has been submitted</p>
                </div>

                <!-- OTP Display -->
                <div class="rounded-2xl bg-gradient-to-br from-violet-500 to-indigo-600 p-6 text-white shadow-xl">
                    <p class="mb-2 text-sm text-white/80">Your Collection Code</p>
                    <div class="flex justify-center gap-3">
                        {#each (paymentOtp || '0000').split('') as digit}
                            <span class="flex h-14 w-12 items-center justify-center rounded-xl bg-white/20 text-3xl font-bold">
                                {digit}
                            </span>
                        {/each}
                    </div>
                    <p class="mt-4 text-sm text-white/80">Show this code at the shop to collect your prints</p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-left text-sm">
                    <h4 class="mb-2 font-semibold text-slate-800">What's Next?</h4>
                    <ul class="space-y-2 text-slate-600">
                        <li class="flex items-start gap-2">
                            <span class="mt-0.5 h-5 w-5 rounded-full bg-violet-100 text-center text-xs font-bold leading-5 text-violet-600">1</span>
                            <span>Go to the print shop</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-0.5 h-5 w-5 rounded-full bg-violet-100 text-center text-xs font-bold leading-5 text-violet-600">2</span>
                            <span>Show your collection code</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-0.5 h-5 w-5 rounded-full bg-violet-100 text-center text-xs font-bold leading-5 text-violet-600">3</span>
                            <span>Collect your printed documents</span>
                        </li>
                    </ul>
                </div>

                <div class="space-y-3">
                    <a href="/" class="btn btn-lg w-full bg-gradient-to-r from-violet-500 to-indigo-600 text-white shadow-lg">
                        Back to Home
                    </a>
                    <button onclick={() => (currentStep = 'upload')} class="btn btn-ghost w-full">
                        Print More Documents
                    </button>
                </div>
            </div>
        {/if}
    </main>

    <!-- Color Selection Bottom Sheet -->
    {#if showColorSheet}
        <div class="fixed inset-0 z-50">
            <!-- Backdrop -->
            <button
                class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity"
                onclick={() => (showColorSheet = false)}
                aria-label="Close"
            ></button>

            <!-- Sheet -->
            <div class="absolute inset-x-0 bottom-0 animate-slide-up rounded-t-3xl bg-white p-6 shadow-2xl">
                <!-- Handle -->
                <div class="mx-auto mb-4 h-1 w-12 rounded-full bg-slate-300"></div>

                <!-- Header -->
                <div class="mb-6 flex items-center justify-between">
                    <h3 class="text-xl font-bold text-slate-800">Print Color</h3>
                    <button
                        onclick={() => (showColorSheet = false)}
                        class="btn btn-circle btn-ghost btn-sm"
                    >
                        <X class="h-5 w-5" />
                    </button>
                </div>

                <!-- Options -->
                <div class="space-y-3">
                    <!-- Black & White Option -->
                    <button
                        onclick={() => { isColor = false; showColorSheet = false; }}
                        class="flex w-full items-center gap-4 rounded-2xl border-2 p-4 transition-all {!isColor
                            ? 'border-violet-500 bg-violet-50'
                            : 'border-slate-200 bg-white hover:border-slate-300'}"
                    >
                        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-slate-200">
                            <FileStack class="h-7 w-7 text-slate-600" />
                        </div>
                        <div class="flex-1 text-left">
                            <p class="text-lg font-semibold text-slate-800">Black & White</p>
                            <p class="text-sm text-slate-500">$0.05 per page</p>
                        </div>
                        {#if !isColor}
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-violet-500">
                                <Check class="h-5 w-5 text-white" />
                            </div>
                        {/if}
                    </button>

                    <!-- Color Option -->
                    <button
                        onclick={() => { isColor = true; showColorSheet = false; }}
                        class="flex w-full items-center gap-4 rounded-2xl border-2 p-4 transition-all {isColor
                            ? 'border-violet-500 bg-violet-50'
                            : 'border-slate-200 bg-white hover:border-slate-300'}"
                    >
                        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-orange-400 to-pink-500">
                            <Palette class="h-7 w-7 text-white" />
                        </div>
                        <div class="flex-1 text-left">
                            <p class="text-lg font-semibold text-slate-800">Full Color</p>
                            <p class="text-sm text-slate-500">$0.15 per page</p>
                        </div>
                        {#if isColor}
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-violet-500">
                                <Check class="h-5 w-5 text-white" />
                            </div>
                        {/if}
                    </button>
                </div>
            </div>
        </div>
    {/if}

    <!-- Double Sided Bottom Sheet -->
    {#if showDoubleSidedSheet}
        <div class="fixed inset-0 z-50">
            <!-- Backdrop -->
            <button
                class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity"
                onclick={() => (showDoubleSidedSheet = false)}
                aria-label="Close"
            ></button>

            <!-- Sheet -->
            <div class="absolute inset-x-0 bottom-0 animate-slide-up rounded-t-3xl bg-white p-6 shadow-2xl">
                <!-- Handle -->
                <div class="mx-auto mb-4 h-1 w-12 rounded-full bg-slate-300"></div>

                <!-- Header -->
                <div class="mb-6 flex items-center justify-between">
                    <h3 class="text-xl font-bold text-slate-800">Page Sides</h3>
                    <button
                        onclick={() => (showDoubleSidedSheet = false)}
                        class="btn btn-circle btn-ghost btn-sm"
                    >
                        <X class="h-5 w-5" />
                    </button>
                </div>

                <!-- Options -->
                <div class="space-y-3">
                    <!-- Single Sided Option -->
                    <button
                        onclick={() => { isDoubleSided = false; showDoubleSidedSheet = false; }}
                        class="flex w-full items-center gap-4 rounded-2xl border-2 p-4 transition-all {!isDoubleSided
                            ? 'border-violet-500 bg-violet-50'
                            : 'border-slate-200 bg-white hover:border-slate-300'}"
                    >
                        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-slate-200">
                            <FileText class="h-7 w-7 text-slate-600" />
                        </div>
                        <div class="flex-1 text-left">
                            <p class="text-lg font-semibold text-slate-800">Single Sided</p>
                            <p class="text-sm text-slate-500">Print on one side only</p>
                        </div>
                        {#if !isDoubleSided}
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-violet-500">
                                <Check class="h-5 w-5 text-white" />
                            </div>
                        {/if}
                    </button>

                    <!-- Double Sided Option -->
                    <button
                        onclick={() => { isDoubleSided = true; showDoubleSidedSheet = false; }}
                        class="flex w-full items-center gap-4 rounded-2xl border-2 p-4 transition-all {isDoubleSided
                            ? 'border-violet-500 bg-violet-50'
                            : 'border-slate-200 bg-white hover:border-slate-300'}"
                    >
                        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500">
                            <BookOpen class="h-7 w-7 text-white" />
                        </div>
                        <div class="flex-1 text-left">
                            <p class="text-lg font-semibold text-slate-800">Double Sided</p>
                            <p class="text-sm text-slate-500">Print on both sides • 10% discount</p>
                        </div>
                        {#if isDoubleSided}
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-violet-500">
                                <Check class="h-5 w-5 text-white" />
                            </div>
                        {/if}
                    </button>
                </div>

                <!-- Savings hint -->
                <div class="mt-4 rounded-xl bg-emerald-50 p-3 text-center">
                    <p class="text-sm text-emerald-700">
                        <span class="font-semibold">Tip:</span> Double sided printing saves paper and gives you 10% off!
                    </p>
                </div>
            </div>
        </div>
    {/if}
</div>

<style>
    @keyframes slide-up {
        from {
            transform: translateY(100%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .animate-slide-up {
        animation: slide-up 0.3s ease-out;
    }
</style>
