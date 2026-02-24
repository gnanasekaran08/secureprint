<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import { onMount, onDestroy } from 'svelte';
    import { Html5Qrcode, Html5QrcodeScannerState } from 'html5-qrcode';
    import {
        QrCode,
        Camera,
        CameraOff,
        Printer,
        ArrowLeft,
        AlertCircle,
        Loader2,
        FlashlightOff,
        Flashlight,
        Upload,
    } from 'lucide-svelte';

    // State
    let scannerElement: HTMLDivElement | null = $state(null);
    let scanner: Html5Qrcode | null = $state(null);
    let isScanning = $state(false);
    let hasPermission = $state<boolean | null>(null);
    let errorMessage = $state<string | null>(null);
    let scannedShopUuid = $state<string | null>(null);
    let isRedirecting = $state(false);
    let torchEnabled = $state(false);
    let cameraId = $state<string | null>(null);

    const SCAN_REGION_ID = 'qr-reader';

    onMount(async () => {
        await requestCameraPermission();
    });

    onDestroy(() => {
        stopScanning();
    });

    async function requestCameraPermission() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
            stream.getTracks().forEach((track) => track.stop());
            hasPermission = true;
            await startScanning();
        } catch (err) {
            console.error('Camera permission denied:', err);
            hasPermission = false;
            errorMessage = 'Camera access is required to scan QR codes. Please enable camera permissions.';
        }
    }

    async function startScanning() {
        if (!scannerElement || isScanning) return;

        try {
            scanner = new Html5Qrcode(SCAN_REGION_ID);

            const devices = await Html5Qrcode.getCameras();
            // Prefer back camera
            const backCamera = devices.find(
                (d) => d.label.toLowerCase().includes('back') || d.label.toLowerCase().includes('rear')
            );
            cameraId = backCamera?.id || devices[0]?.id || null;

            if (!cameraId) {
                errorMessage = 'No camera found on this device.';
                return;
            }

            await scanner.start(
                cameraId,
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 },
                    aspectRatio: 1,
                },
                onScanSuccess,
                onScanFailure
            );

            isScanning = true;
            errorMessage = null;
        } catch (err) {
            console.error('Failed to start scanner:', err);
            errorMessage = 'Failed to start camera. Please refresh and try again.';
        }
    }

    async function stopScanning() {
        if (scanner && isScanning) {
            try {
                await scanner.stop();
            } catch (err) {
                console.error('Error stopping scanner:', err);
            }
            isScanning = false;
        }
    }

    function onScanSuccess(decodedText: string) {
        console.log('QR Code scanned:', decodedText);

        // Try to extract shop UUID from the scanned URL or text
        let shopUuid: string | null = null;

        // Check if it's a URL with /print/{uuid} pattern
        const urlMatch = decodedText.match(/\/print\/([a-f0-9-]{36})/i);
        if (urlMatch) {
            shopUuid = urlMatch[1];
        }
        // Check if it's just a UUID
        else if (/^[a-f0-9-]{36}$/i.test(decodedText)) {
            shopUuid = decodedText;
        }
        // Check if it's a URL with shop_uuid query param
        else {
            try {
                const url = new URL(decodedText);
                shopUuid = url.searchParams.get('shop') || url.searchParams.get('shop_uuid');
            } catch {
                // Not a valid URL, use the whole text as potential identifier
                shopUuid = decodedText;
            }
        }

        if (shopUuid) {
            scannedShopUuid = shopUuid;
            isRedirecting = true;
            stopScanning();

            // Redirect to print page with shop UUID
            setTimeout(() => {
                router.visit(`/print/${shopUuid}`);
            }, 1000);
        }
    }

    function onScanFailure(error: string) {
        // Ignore scan failures (no QR code in view)
    }

    async function toggleTorch() {
        if (scanner && isScanning) {
            try {
                // @ts-ignore - torch may not be in types
                await scanner.applyVideoConstraints({
                    advanced: [{ torch: !torchEnabled }],
                });
                torchEnabled = !torchEnabled;
            } catch (err) {
                console.log('Torch not supported on this device');
            }
        }
    }

    function goToManualUpload() {
        router.visit('/print');
    }
</script>

<AppHead title="Scan QR Code">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
</AppHead>

<div data-theme="printsecure" class="flex min-h-screen flex-col bg-black">
    <!-- Header -->
    <header class="sticky top-0 z-50 bg-black/80 px-4 py-3 backdrop-blur-xl">
        <div class="mx-auto flex max-w-lg items-center gap-3">
            <a href="/" class="btn btn-circle btn-ghost btn-sm text-white">
                <ArrowLeft class="h-5 w-5" />
            </a>
            <div class="flex items-center gap-2">
                <div
                    class="flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-violet-500 to-indigo-600 shadow-md"
                >
                    <Printer class="h-4 w-4 text-white" />
                </div>
                <span class="bg-gradient-to-r from-violet-400 to-indigo-400 bg-clip-text text-lg font-bold text-transparent">
                    PrintSecure
                </span>
            </div>
        </div>
    </header>

    <main class="flex flex-1 flex-col items-center justify-center px-4">
        <!-- Success State (Redirecting) -->
        {#if isRedirecting}
            <div class="space-y-6 text-center">
                <div
                    class="mx-auto flex h-24 w-24 animate-pulse items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-teal-500 shadow-xl"
                >
                    <QrCode class="h-12 w-12 text-white" />
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-white">QR Code Scanned!</h2>
                    <p class="mt-2 text-slate-400">Redirecting to upload page...</p>
                </div>
                <Loader2 class="mx-auto h-8 w-8 animate-spin text-violet-500" />
            </div>
        {:else if hasPermission === false}
            <!-- No Permission State -->
            <div class="space-y-6 text-center">
                <div
                    class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-red-500/20 shadow-xl"
                >
                    <CameraOff class="h-12 w-12 text-red-400" />
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-white">Camera Access Required</h2>
                    <p class="mt-2 max-w-xs text-slate-400">
                        Please enable camera permissions in your browser settings to scan QR codes.
                    </p>
                </div>
                <div class="space-y-3">
                    <button onclick={requestCameraPermission} class="btn btn-primary btn-lg w-full max-w-xs">
                        <Camera class="h-5 w-5" />
                        Try Again
                    </button>
                    <button onclick={goToManualUpload} class="btn btn-ghost btn-lg w-full max-w-xs text-white">
                        <Upload class="h-5 w-5" />
                        Upload Without Scanning
                    </button>
                </div>
            </div>
        {:else if hasPermission === null}
            <!-- Loading State -->
            <div class="space-y-6 text-center">
                <div
                    class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-violet-500/20 shadow-xl"
                >
                    <Camera class="h-12 w-12 animate-pulse text-violet-400" />
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">Requesting Camera Access</h2>
                    <p class="mt-2 text-slate-400">Please allow camera access to scan QR codes</p>
                </div>
                <Loader2 class="mx-auto h-8 w-8 animate-spin text-violet-500" />
            </div>
        {:else}
            <!-- Scanner View -->
            <div class="w-full max-w-md space-y-6">
                <!-- Instructions -->
                <div class="text-center">
                    <h2 class="text-xl font-bold text-white">Scan Shop QR Code</h2>
                    <p class="mt-1 text-sm text-slate-400">Point your camera at the QR code at the print shop</p>
                </div>

                <!-- Scanner Container -->
                <div class="relative mx-auto aspect-square w-full max-w-[300px] overflow-hidden rounded-3xl">
                    <!-- Scanner Element -->
                    <div id={SCAN_REGION_ID} bind:this={scannerElement} class="h-full w-full"></div>

                    <!-- Overlay Frame -->
                    <div class="pointer-events-none absolute inset-0">
                        <!-- Corner brackets -->
                        <div class="absolute left-4 top-4 h-12 w-12 border-l-4 border-t-4 border-violet-500 rounded-tl-lg"></div>
                        <div class="absolute right-4 top-4 h-12 w-12 border-r-4 border-t-4 border-violet-500 rounded-tr-lg"></div>
                        <div class="absolute bottom-4 left-4 h-12 w-12 border-b-4 border-l-4 border-violet-500 rounded-bl-lg"></div>
                        <div class="absolute bottom-4 right-4 h-12 w-12 border-b-4 border-r-4 border-violet-500 rounded-br-lg"></div>

                        <!-- Scanning line animation -->
                        <div class="absolute left-4 right-4 top-1/2 h-0.5 -translate-y-1/2 bg-gradient-to-r from-transparent via-violet-500 to-transparent animate-pulse"></div>
                    </div>
                </div>

                <!-- Error Message -->
                {#if errorMessage}
                    <div class="mx-auto max-w-xs rounded-xl bg-red-500/20 p-4 text-center">
                        <AlertCircle class="mx-auto h-6 w-6 text-red-400" />
                        <p class="mt-2 text-sm text-red-300">{errorMessage}</p>
                    </div>
                {/if}

                <!-- Controls -->
                <div class="flex justify-center gap-4">
                    <button
                        onclick={toggleTorch}
                        class="btn btn-circle btn-lg border-2 border-white/20 bg-white/10 text-white hover:bg-white/20"
                        title={torchEnabled ? 'Turn off flashlight' : 'Turn on flashlight'}
                    >
                        {#if torchEnabled}
                            <Flashlight class="h-6 w-6" />
                        {:else}
                            <FlashlightOff class="h-6 w-6" />
                        {/if}
                    </button>
                </div>

                <!-- Alternative Action -->
                <div class="pt-4 text-center">
                    <p class="mb-3 text-sm text-slate-500">Don't have a QR code?</p>
                    <button onclick={goToManualUpload} class="btn btn-outline btn-lg w-full max-w-xs border-white/30 text-white hover:bg-white/10">
                        <Upload class="h-5 w-5" />
                        Upload Without Scanning
                    </button>
                </div>
            </div>
        {/if}
    </main>

    <!-- Footer hint -->
    <footer class="px-4 pb-8 pt-4 text-center">
        <p class="text-xs text-slate-600">
            Scan the QR code displayed at any PrintSecure partner shop
        </p>
    </footer>
</div>

<style>
    /* Override html5-qrcode styles for better look */
    :global(#qr-reader) {
        border: none !important;
        background: transparent !important;
    }

    :global(#qr-reader video) {
        border-radius: 1.5rem !important;
        object-fit: cover !important;
    }

    :global(#qr-reader__scan_region) {
        background: transparent !important;
    }

    :global(#qr-reader__dashboard) {
        display: none !important;
    }
</style>
