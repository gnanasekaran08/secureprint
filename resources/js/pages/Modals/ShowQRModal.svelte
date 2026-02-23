<script lang="ts">
    import { Download, X } from 'lucide-svelte';
    import { createQrPngDataUrl } from '@svelte-put/qr';
    import { onMount } from 'svelte';
    import QR from '@svelte-put/qr/svg/QR.svelte';

    let { shop, onClose } = $props();

    let qrDataUrl = $state('');
    let isGenerating = $state(false);

    onMount(async () => {
        const qrConfig = {
            data: shop.qr_code_url,
            width: 400,
            height: 400,
            backgroundFill: '#fff',
        };
        qrDataUrl = await createQrPngDataUrl(qrConfig);
    });

    function roundedRect(ctx: CanvasRenderingContext2D, x: number, y: number, w: number, h: number, r: number) {
        ctx.beginPath();
        ctx.moveTo(x + r, y);
        ctx.lineTo(x + w - r, y);
        ctx.quadraticCurveTo(x + w, y, x + w, y + r);
        ctx.lineTo(x + w, y + h - r);
        ctx.quadraticCurveTo(x + w, y + h, x + w - r, y + h);
        ctx.lineTo(x + r, y + h);
        ctx.quadraticCurveTo(x, y + h, x, y + h - r);
        ctx.lineTo(x, y + r);
        ctx.quadraticCurveTo(x, y, x + r, y);
        ctx.closePath();
    }

    async function downloadStyledQR() {
        isGenerating = true;

        try {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            if (!ctx) return;

            const cardWidth = 420;
            const cardHeight = 600;
            const padding = 10;
            const radius = 20;
            canvas.width = cardWidth;
            canvas.height = cardHeight;

            // Outer background with subtle gradient
            const bgGradient = ctx.createLinearGradient(0, 0, 0, cardHeight);
            bgGradient.addColorStop(0, '#f3f4f6');
            bgGradient.addColorStop(1, '#e5e7eb');
            ctx.fillStyle = bgGradient;
            ctx.fillRect(0, 0, cardWidth, cardHeight);

            // Main card with rounded corners
            ctx.save();
            roundedRect(ctx, padding, padding, cardWidth - padding * 2, cardHeight - padding * 2, radius);
            ctx.clip();

            // White card background
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(padding, padding, cardWidth - padding * 2, cardHeight - padding * 2);

            // Header gradient section
            const headerHeight = 100;
            const headerGradient = ctx.createLinearGradient(0, 0, cardWidth, 0);
            headerGradient.addColorStop(0, '#8b5cf6');
            headerGradient.addColorStop(0.5, '#a855f7');
            headerGradient.addColorStop(1, '#9333ea');
            ctx.fillStyle = headerGradient;
            roundedRect(ctx, padding, padding, cardWidth - padding * 2, headerHeight, radius);
            ctx.fill();

            // Fix bottom corners of header (make them square)
            ctx.fillRect(padding, padding + headerHeight - radius, cardWidth - padding * 2, radius);

            // Header text - "SecurePrint"
            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold 28px system-ui, -apple-system, sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText('SecurePrint', cardWidth / 2, padding + 45);

            // Tagline
            ctx.font = '14px system-ui, -apple-system, sans-serif';
            ctx.fillStyle = 'rgba(255, 255, 255, 0.9)';
            ctx.fillText('Smart Document Printing', cardWidth / 2, padding + 72);

            // Shop name section
            const shopY = padding + headerHeight + 30;
            ctx.fillStyle = '#111827';
            ctx.font = 'bold 18px system-ui, -apple-system, sans-serif';
            const shopName = shop.name || 'Print Shop';
            ctx.fillText(shopName.length > 28 ? shopName.substring(0, 28) + '...' : shopName, cardWidth / 2, shopY);

            // Decorative line under shop name
            const lineY = shopY + 15;
            const lineGradient = ctx.createLinearGradient(cardWidth / 2 - 60, 0, cardWidth / 2 + 60, 0);
            lineGradient.addColorStop(0, 'transparent');
            lineGradient.addColorStop(0.2, '#8b5cf6');
            lineGradient.addColorStop(0.8, '#8b5cf6');
            lineGradient.addColorStop(1, 'transparent');
            ctx.strokeStyle = lineGradient;
            ctx.lineWidth = 2;
            ctx.beginPath();
            ctx.moveTo(cardWidth / 2 - 60, lineY);
            ctx.lineTo(cardWidth / 2 + 60, lineY);
            ctx.stroke();

            // Instructions
            ctx.fillStyle = '#6b7280';
            ctx.font = '13px system-ui, -apple-system, sans-serif';
            ctx.fillText('Scan to upload & print documents', cardWidth / 2, lineY + 35);

            // QR Code container with shadow effect
            const qrSize = 220;
            const qrX = (cardWidth - qrSize) / 2;
            const qrY = lineY + 55;

            // QR shadow
            ctx.shadowColor = 'rgba(139, 92, 246, 0.2)';
            ctx.shadowBlur = 20;
            ctx.shadowOffsetX = 0;
            ctx.shadowOffsetY = 4;

            // QR background
            ctx.fillStyle = '#ffffff';
            roundedRect(ctx, qrX - 15, qrY - 15, qrSize + 30, qrSize + 30, 12);
            ctx.fill();

            // Reset shadow
            ctx.shadowColor = 'transparent';
            ctx.shadowBlur = 0;
            ctx.shadowOffsetY = 0;

            // QR border
            ctx.strokeStyle = '#8b5cf6';
            ctx.lineWidth = 3;
            roundedRect(ctx, qrX - 15, qrY - 15, qrSize + 30, qrSize + 30, 12);
            ctx.stroke();

            // Load and draw QR code
            const qrImage = new Image();
            qrImage.src = qrDataUrl;

            await new Promise((resolve, reject) => {
                qrImage.onload = resolve;
                qrImage.onerror = reject;
            });

            ctx.drawImage(qrImage, qrX, qrY, qrSize, qrSize);

            // Corner accents on QR
            const cornerSize = 20;
            ctx.strokeStyle = '#8b5cf6';
            ctx.lineWidth = 4;
            ctx.lineCap = 'round';

            // Top-left corner
            ctx.beginPath();
            ctx.moveTo(qrX - 5, qrY + cornerSize);
            ctx.lineTo(qrX - 5, qrY - 5);
            ctx.lineTo(qrX + cornerSize, qrY - 5);
            ctx.stroke();

            // Top-right corner
            ctx.beginPath();
            ctx.moveTo(qrX + qrSize - cornerSize, qrY - 5);
            ctx.lineTo(qrX + qrSize + 5, qrY - 5);
            ctx.lineTo(qrX + qrSize + 5, qrY + cornerSize);
            ctx.stroke();

            // Bottom-left corner
            ctx.beginPath();
            ctx.moveTo(qrX - 5, qrY + qrSize - cornerSize);
            ctx.lineTo(qrX - 5, qrY + qrSize + 5);
            ctx.lineTo(qrX + cornerSize, qrY + qrSize + 5);
            ctx.stroke();

            // Bottom-right corner
            ctx.beginPath();
            ctx.moveTo(qrX + qrSize - cornerSize, qrY + qrSize + 5);
            ctx.lineTo(qrX + qrSize + 5, qrY + qrSize + 5);
            ctx.lineTo(qrX + qrSize + 5, qrY + qrSize - cornerSize);
            ctx.stroke();

            // Footer section
            const footerY = qrY + qrSize + 50;

            // Footer divider
            ctx.strokeStyle = '#e5e7eb';
            ctx.lineWidth = 1;
            ctx.beginPath();
            ctx.moveTo(padding + 30, footerY);
            ctx.lineTo(cardWidth - padding - 30, footerY);
            ctx.stroke();

            // Footer text
            ctx.fillStyle = '#9ca3af';
            ctx.font = '12px system-ui, -apple-system, sans-serif';
            ctx.fillText("India's Smart Printing Service", cardWidth / 2, footerY + 25);

            // Website
            ctx.fillStyle = '#8b5cf6';
            ctx.font = 'bold 13px system-ui, -apple-system, sans-serif';
            ctx.fillText('secureprint.in', cardWidth / 2, footerY + 45);

            ctx.restore();

            // Download
            const link = document.createElement('a');
            link.href = canvas.toDataURL('image/png');
            link.download = `${shop.name?.replace(/\s+/g, '-').toLowerCase() || 'shop'}-qr.png`;
            link.click();
        } catch (error) {
            console.error('Error generating QR card:', error);
        } finally {
            isGenerating = false;
        }
    }
</script>

<dialog class="modal modal-open">
    <div class="modal-box max-w-sm">
        <button
            class="btn btn-sm btn-circle btn-ghost absolute right-3 top-3"
            onclick={onClose}
        >
            <X class="h-4 w-4" />
        </button>

        <div class="pt-2 text-center">
            <h3 class="text-lg font-bold">{shop.name || 'Print Shop'}</h3>
            <p class="text-sm text-base-content/60 mt-1">
                Scan to upload documents
            </p>

            <div class="mt-4 flex justify-center">
                <QR
                    data={shop.qr_code_url}
                    anchorOuterFill="#8b5cf6"
                    anchorInnerFill="#9333ea"
                    moduleFill="#1f2937"
                />
            </div>

            <button
                onclick={downloadStyledQR}
                disabled={isGenerating || !qrDataUrl}
                class="btn btn-primary mt-6 gap-2"
            >
                {#if isGenerating}
                    <span class="loading loading-spinner loading-sm"></span>
                    Generating...
                {:else}
                    <Download class="h-4 w-4" />
                    Download QR
                {/if}
            </button>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button onclick={onClose}>close</button>
    </form>
</dialog>
