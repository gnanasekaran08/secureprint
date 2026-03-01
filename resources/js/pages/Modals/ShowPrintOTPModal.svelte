<script lang="ts">
    import { page } from '@inertiajs/svelte';
    import { CheckCircle, Files, Loader2, Printer, X } from 'lucide-svelte';
    import mammoth from 'mammoth';
    import { onMount } from 'svelte';

    let { printJobUuid, onClose, triggerToast } = $props();

    let otpDigits = $state(['', '', '', '']);
    let otpInputs: HTMLInputElement[] = [];
    let isVerifying = $state(false);
    let isVerified = $state(false);
    let error = $state<string | null>(null);
    let files = $state<any[]>([]);
    let isPrinting = $state<string | null>(null);

    const otp = $derived(otpDigits.join(''));
    const isComplete = $derived(otpDigits.every((d) => d !== ''));

    onMount(() => {
        // Focus first input on mount
        setTimeout(() => otpInputs[0]?.focus(), 100);
    });

    function handleInput(index: number, event: Event) {
        const input = event.target as HTMLInputElement;
        const value = input.value;

        // Only allow single digit
        if (value.length > 1) {
            otpDigits[index] = value.slice(-1);
        } else {
            otpDigits[index] = value;
        }

        // Clear error on input
        error = null;

        // Auto-focus next input
        if (value && index < 3) {
            otpInputs[index + 1]?.focus();
        }
    }

    function handleKeydown(index: number, event: KeyboardEvent) {
        if (event.key === 'Backspace' && !otpDigits[index] && index > 0) {
            otpInputs[index - 1]?.focus();
        }

        if (event.key === 'ArrowLeft' && index > 0) {
            otpInputs[index - 1]?.focus();
        }

        if (event.key === 'ArrowRight' && index < 3) {
            otpInputs[index + 1]?.focus();
        }
    }

    function handlePaste(event: ClipboardEvent) {
        event.preventDefault();
        const pastedData = event.clipboardData?.getData('text') || '';
        const digits = pastedData.replace(/\D/g, '').slice(0, 4).split('');

        digits.forEach((digit, i) => {
            if (i < 4) {
                otpDigits[i] = digit;
            }
        });

        // Focus the next empty input or the last one
        const nextEmptyIndex = otpDigits.findIndex((d) => d === '');
        if (nextEmptyIndex !== -1) {
            otpInputs[nextEmptyIndex]?.focus();
        } else {
            otpInputs[3]?.focus();
        }
    }

    async function verifyOtp() {
        if (!isComplete) return;

        isVerifying = true;
        error = null;

        try {
            const request = await fetch('/print/verify-otp', {
                method: 'POST',
                body: JSON.stringify({
                    print_job_uuid: printJobUuid,
                    otp: otp,
                }),
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $page.props.csrf_token,
                    Accept: 'application/json',
                },
            });

            const response = await request.json();
            console.log('response', response);

            if ('error' === response.status) {
                error = response.message || 'Invalid OTP. Please try again.';
                // Clear inputs on error
                otpDigits = ['', '', '', ''];
                otpInputs[0]?.focus();
                triggerToast(
                    response.message || 'Invalid OTP. Please try again.',
                    'error',
                );
                return;
            }

            isVerified = true;
            triggerToast(
                response.message || 'OTP verified successfully!',
                'success',
            );

            files = response?.data?.files || [];
        } catch (err) {
            error = 'Something went wrong. Please try again.';
        } finally {
            isVerifying = false;
        }
    }

    const isWordDocument = (filetype: string): boolean => {
        const wordTypes = [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'doc',
            'docx',
        ];
        return wordTypes.some(
            (type) =>
                filetype?.toLowerCase().includes(type) ||
                type.includes(filetype?.toLowerCase()),
        );
    };

    const printWordDocument = async (file: any) => {
        isPrinting = file.filename;
        try {
            // Fetch the Word document as ArrayBuffer
            const response = await fetch(file.filepath);
            const arrayBuffer = await response.arrayBuffer();

            // Convert DOCX to HTML using mammoth
            const result = await mammoth.convertToHtml({ arrayBuffer });
            const html = result.value;

            // Create a print-friendly HTML document
            const printContent = `
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="utf-8">
                    <title>${file.filename}</title>
                    <style>
                        @media print {
                            body { margin: 0; padding: 20px; }
                        }
                        body {
                            font-family: 'Times New Roman', Times, serif;
                            font-size: 12pt;
                            line-height: 1.5;
                            max-width: 800px;
                            margin: 0 auto;
                            padding: 20px;
                        }
                        img { max-width: 100%; height: auto; }
                        table { border-collapse: collapse; width: 100%; }
                        td, th { border: 1px solid #ddd; padding: 8px; }
                    </style>
                </head>
                <body>${html}</body>
                </html>
            `;

            // Open print window with the HTML content
            const printWindow = window.open('', '_blank');
            if (printWindow) {
                printWindow.document.write(printContent);
                printWindow.document.close();
                printWindow.onload = () => {
                    printWindow.focus();
                    printWindow.print();
                    printWindow.onafterprint = () => printWindow.close();
                };
            }
        } catch (err) {
            console.error('Error converting Word document:', err);
            triggerToast('Failed to convert Word document for printing', 'error');
        } finally {
            isPrinting = null;
        }
    };

    const printPdfOrImage = (file: any) => {
        isPrinting = file.filename;
        const iframe = document.createElement('iframe');
        iframe.style.position = 'fixed';
        iframe.style.right = '0';
        iframe.style.bottom = '0';
        iframe.style.width = '0';
        iframe.style.height = '0';
        iframe.style.border = 'none';
        iframe.src = file.filepath;

        iframe.onload = () => {
            try {
                iframe.contentWindow?.focus();
                iframe.contentWindow?.print();
            } catch {
                const printWindow = window.open(file.filepath, '_blank');
                if (printWindow) {
                    printWindow.onload = () => {
                        printWindow.print();
                    };
                }
            }

            setTimeout(() => {
                document.body.removeChild(iframe);
                isPrinting = null;
            }, 1000);
        };

        document.body.appendChild(iframe);
    };

    const printFile = (file: any) => {
        if (isWordDocument(file.filetype)) {
            printWordDocument(file);
        } else {
            printPdfOrImage(file);
        }
    };
</script>

<dialog class="modal modal-open">
    <div class="modal-box max-w-sm">
        <button
            class="btn btn-sm btn-circle btn-ghost absolute right-3 top-3"
            onclick={onClose}
        >
            <X class="h-4 w-4" />
        </button>

        {#if isVerified}
            <!-- Success State -->
            <div class="py-6 text-center">
                <div
                    class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-teal-500"
                >
                    <CheckCircle class="h-8 w-8 text-white" />
                </div>
                <h3 class="text-xl font-bold text-slate-800">OTP Verified!</h3>
                <p class="mt-2 text-sm text-slate-600">
                    Your documents are ready to print.
                </p>
                <button onclick={close} class="btn btn-primary mt-6 w-full">
                    Done
                </button>
            </div>
        {:else}
            <div class="pt-2 text-center">
                <div
                    class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-violet-500 to-indigo-600"
                >
                    <Printer class="h-7 w-7 text-white" />
                </div>
                <h3 class="text-xl font-bold text-slate-800">
                    Enter Collection Code
                </h3>
                <p class="mt-2 text-sm text-slate-600">
                    Enter the 4-digit code to verify your print job
                </p>
            </div>

            <!-- OTP Input Fields -->
            <div class="mt-6 flex justify-center gap-3">
                {#each otpDigits as digit, index}
                    <input
                        bind:this={otpInputs[index]}
                        type="text"
                        inputmode="numeric"
                        maxlength="1"
                        value={digit}
                        oninput={(e) => handleInput(index, e)}
                        onkeydown={(e) => handleKeydown(index, e)}
                        onpaste={handlePaste}
                        class="h-14 w-12 rounded-xl border-2 text-center text-2xl font-bold transition-all focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 {error
                            ? 'border-red-300 bg-red-50'
                            : digit
                              ? 'border-violet-500 bg-violet-50'
                              : 'border-slate-200 bg-slate-50'}"
                    />
                {/each}
            </div>

            <!-- Error Message -->
            {#if error}
                <p class="mt-3 text-center text-sm text-red-500">{error}</p>
            {/if}

            <!-- Verify Button -->
            <button
                onclick={verifyOtp}
                disabled={!isComplete || isVerifying}
                class="btn btn-lg mt-6 w-full gap-2 bg-gradient-to-r from-violet-500 to-indigo-600 text-white disabled:opacity-50"
            >
                {#if isVerifying}
                    <Loader2 class="h-5 w-5 animate-spin" />
                    Verifying...
                {:else}
                    Verify Code & Print
                {/if}
            </button>

            <p class="mt-4 text-center text-xs text-slate-500">
                The code was provided after payment
            </p>
        {/if}

        {#if files.length > 0 && isVerified}
            <div class="mt-6">
                <h4 class="mb-2 text-sm font-medium text-slate-700">
                    Your Files:
                </h4>
                <ul class="space-y-2">
                    {#each files as file}
                        <li
                            class="flex items-center gap-3 rounded-lg border p-3"
                        >
                            <div class="flex gap-1">
                                <Files class="h-5 w-5 text-slate-500" />
                                <span
                                    class="text-sm text-slate-600 text-truncate block max-w-xs"
                                >
                                    {file.filename}
                                </span>
                            </div>
                            <button
                                type="button"
                                class="ml-auto flex items-center gap-1 text-sm text-blue-600 hover:underline disabled:opacity-50"
                                onclick={() => printFile(file)}
                                disabled={isPrinting !== null}
                            >
                                {#if isPrinting === file.filename}
                                    <Loader2 class="h-4 w-4 animate-spin" />
                                    Preparing...
                                {:else}
                                    <Printer class="h-4 w-4" />
                                    Print
                                {/if}
                            </button>
                        </li>
                    {/each}
                </ul>
            </div>
        {/if}
    </div>
    <form method="dialog" class="modal-backdrop">
        <button onclick={close}>close</button>
    </form>
</dialog>
