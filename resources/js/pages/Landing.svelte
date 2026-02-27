<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import {
        Printer,
        Upload,
        Shield,
        Clock,
        ArrowRight,
        Zap,
        Sparkles,
        FileText,
        Store,
        CreditCard,
        EyeOff,
        UserX,
        QrCode,
    } from 'lucide-svelte';
    import AppHead from '@/components/AppHead.svelte';
    import { toUrl } from '@/lib/utils';
    import { dashboard, print } from '@/routes';

    let { data } = $props();
    const auth = $derived($page.props.auth);

    const features = [
        {
            icon: EyeOff,
            title: 'No Account Required',
            description:
                'Print instantly without signing up. No email, no registration, no hassle.',
            gradient: 'from-violet-500 to-purple-600',
            bgColor: 'bg-violet-500/10',
            iconColor: 'text-violet-500',
        },
        {
            icon: Shield,
            title: 'Zero Personal Data',
            description:
                'We never collect your personal information. Your privacy is our priority.',
            gradient: 'from-orange-500 to-red-500',
            bgColor: 'bg-orange-500/10',
            iconColor: 'text-orange-500',
        },
        {
            icon: Upload,
            title: 'Simple Upload',
            description:
                "Just upload your files, pay, and collect. It's that simple.",
            gradient: 'from-emerald-500 to-teal-500',
            bgColor: 'bg-emerald-500/10',
            iconColor: 'text-emerald-500',
        },
        {
            icon: Clock,
            title: 'Instant Printing',
            description:
                'Your documents are ready within minutes at nearby partner shops.',
            gradient: 'from-blue-500 to-cyan-500',
            bgColor: 'bg-blue-500/10',
            iconColor: 'text-blue-500',
        },
    ];

    const steps = [
        {
            step: 1,
            title: 'Scan QR',
            description: 'Scan the shop QR code to connect instantly',
            icon: QrCode,
            color: 'from-cyan-500 to-blue-600',
        },
        {
            step: 2,
            title: 'Upload',
            description: 'Select your files and customize print settings',
            icon: FileText,
            color: 'from-violet-500 to-indigo-600',
        },
        {
            step: 3,
            title: 'Pay',
            description: 'Secure checkout with multiple payment options',
            icon: CreditCard,
            color: 'from-orange-500 to-pink-500',
        },
        {
            step: 4,
            title: 'Collect',
            description: 'Pick up your prints from your chosen shop',
            icon: Store,
            color: 'from-emerald-500 to-teal-500',
        },
    ];

    const stats = [
        {
            value: data?.shops_count,
            label: 'Partner Shops',
            icon: Store,
        },
        {
            value: data?.prints_count,
            label: 'Documents Printed',
            icon: FileText,
        },
        { value: '0', label: 'Data Collected', icon: EyeOff },
        { value: 'No', label: 'Sign-up Needed for users', icon: UserX },
    ];
</script>

<AppHead title="Print Anywhere, Securely">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin="anonymous"
    />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet"
    />
</AppHead>

<div data-theme="printsecure" class="min-h-screen bg-base-100 font-sans">
    <!-- Animated Background with Patterns -->
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <!-- Gradient Orbs -->
        <div
            class="absolute -left-40 -top-40 h-96 w-96 animate-pulse rounded-full bg-gradient-to-br from-violet-400/30 to-purple-600/30 blur-3xl"
        ></div>
        <div
            class="absolute -bottom-40 -right-40 h-[500px] w-[500px] animate-pulse rounded-full bg-gradient-to-br from-orange-400/20 to-pink-600/20 blur-3xl"
            style="animation-delay: 1s;"
        ></div>
        <div
            class="absolute left-1/3 top-1/4 h-72 w-72 animate-pulse rounded-full bg-gradient-to-br from-cyan-400/15 to-blue-600/15 blur-3xl"
            style="animation-delay: 2s;"
        ></div>
        <div
            class="absolute right-1/4 top-2/3 h-64 w-64 animate-pulse rounded-full bg-gradient-to-br from-emerald-400/10 to-teal-600/10 blur-3xl"
            style="animation-delay: 3s;"
        ></div>

        <!-- Grid Pattern - Subtle Lines -->
        <svg
            class="absolute inset-0 h-full w-full"
            xmlns="http://www.w3.org/2000/svg"
        >
            <defs>
                <pattern
                    id="grid-pattern"
                    patternUnits="userSpaceOnUse"
                    width="80"
                    height="80"
                >
                    <!-- Vertical lines -->
                    <line
                        x1="80"
                        y1="0"
                        x2="80"
                        y2="80"
                        stroke="#8b5cf6"
                        stroke-width="0.5"
                        opacity="0.08"
                    />
                    <!-- Horizontal lines -->
                    <line
                        x1="0"
                        y1="80"
                        x2="80"
                        y2="80"
                        stroke="#8b5cf6"
                        stroke-width="0.5"
                        opacity="0.08"
                    />
                    <!-- Intersection dots -->
                    <circle
                        cx="80"
                        cy="80"
                        r="1.5"
                        fill="#8b5cf6"
                        opacity="0.12"
                    />
                </pattern>
                <!-- Gradient mask for fade effect -->
                <radialGradient id="grid-fade" cx="50%" cy="50%" r="70%">
                    <stop offset="0%" stop-color="white" stop-opacity="1" />
                    <stop offset="100%" stop-color="white" stop-opacity="0" />
                </radialGradient>
                <mask id="grid-mask">
                    <rect width="100%" height="100%" fill="url(#grid-fade)" />
                </mask>
            </defs>
            <rect
                width="100%"
                height="100%"
                fill="url(#grid-pattern)"
                mask="url(#grid-mask)"
            />
        </svg>
    </div>

    <!-- Navigation -->
    <nav
        class="navbar fixed top-0 z-50 border-b border-base-200/50 bg-base-100/80 px-6 backdrop-blur-xl lg:px-12"
    >
        <div class="navbar-start">
            <a href="/" class="flex items-center gap-3 text-xl font-bold">
                <div
                    class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-indigo-600 shadow-lg shadow-violet-500/30"
                >
                    <Printer class="h-5 w-5 text-white" />
                </div>
                <span
                    class="bg-gradient-to-r from-violet-600 via-purple-600 to-indigo-600 bg-clip-text text-transparent"
                >
                    PrintSecure
                </span>
            </a>
        </div>
        <div class="navbar-center hidden lg:flex">
            <ul class="menu menu-horizontal gap-1 px-1">
                <li>
                    <a
                        href="#features"
                        class="rounded-lg font-medium text-base-content/70 transition-colors hover:bg-violet-500/10 hover:text-violet-600"
                    >
                        Features
                    </a>
                </li>
                <li>
                    <a
                        href="#how-it-works"
                        class="rounded-lg font-medium text-base-content/70 transition-colors hover:bg-violet-500/10 hover:text-violet-600"
                    >
                        How it Works
                    </a>
                </li>
            </ul>
        </div>
        <div class="navbar-end gap-3">
            {#if auth.user}
                <Link
                    href={toUrl(dashboard())}
                    class="btn btn-ghost font-medium text-base-content/70 hover:bg-violet-500/10 hover:text-violet-600"
                >
                    Dashboard
                </Link>
            {/if}
            <Link
                href={toUrl(print())}
                class="btn bg-gradient-to-r from-violet-500 to-indigo-600 text-white shadow-lg shadow-violet-500/30 hover:shadow-xl hover:shadow-violet-500/40"
            >
                Start Printing
            </Link>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative min-h-screen overflow-hidden pt-24 bg-[#faf9fb]">
        <!-- Hero Background Grid Lines -->
        <div class="absolute inset-0 -z-10">
            <!-- Base Grid Lines - High visibility -->
            <div
                class="absolute inset-0"
                style="background-image: linear-gradient(to right, rgba(180, 180, 195, 0.8) 1px, transparent 1px), linear-gradient(to bottom, rgba(180, 180, 195, 0.8) 1px, transparent 1px); background-size: 40px 40px;"
            ></div>

            <!-- Gradient overlay for colorful effect -->
            <div
                class="absolute inset-0 bg-gradient-to-br from-pink-200/60 via-transparent to-orange-200/50"
            ></div>
            <div
                class="absolute inset-0 bg-gradient-to-tl from-violet-200/50 via-transparent to-cyan-200/40"
            ></div>

            <!-- Center fade to white -->
            <div
                class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,rgba(255,255,255,0.85)_0%,transparent_60%)]"
            ></div>
        </div>

        <style>
            @keyframes float {
                0%,
                100% {
                    transform: translateY(0) rotate(var(--rotate, 0deg));
                }
                50% {
                    transform: translateY(-20px) rotate(var(--rotate, 0deg));
                }
            }
        </style>

        <div
            class="mx-auto flex max-w-7xl flex-col items-center gap-16 px-6 py-20 lg:flex-row-reverse lg:gap-20 lg:px-12"
        >
            <!-- Printer Illustration -->
            <div class="relative flex-1">
                <div class="relative mx-auto w-full max-w-md">
                    <!-- Printer Image Container -->
                    <div class="relative mb-8 flex justify-center">
                        <div class="relative">
                            <div
                                class="flex h-56 w-80 items-center justify-center rounded-3xl bg-gradient-to-br from-slate-50 via-violet-50/50 to-indigo-50"
                            >
                                <!-- Stylized Printer Illustration -->
                                <svg
                                    viewBox="0 0 200 160"
                                    class="h-48 w-60"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <!-- Shadow -->
                                    <ellipse
                                        cx="100"
                                        cy="155"
                                        rx="70"
                                        ry="5"
                                        fill="#e2e8f0"
                                    />
                                    <!-- Printer Body -->
                                    <rect
                                        x="20"
                                        y="50"
                                        width="160"
                                        height="80"
                                        rx="12"
                                        fill="url(#printerGradient)"
                                        stroke="#8b5cf6"
                                        stroke-width="2"
                                    />
                                    <!-- Paper Input -->
                                    <rect
                                        x="50"
                                        y="20"
                                        width="100"
                                        height="35"
                                        rx="6"
                                        fill="#f1f5f9"
                                        stroke="#cbd5e1"
                                        stroke-width="2"
                                    />
                                    <!-- Paper Stack Lines -->
                                    <line
                                        x1="60"
                                        y1="30"
                                        x2="140"
                                        y2="30"
                                        stroke="#e2e8f0"
                                        stroke-width="2"
                                    />
                                    <line
                                        x1="60"
                                        y1="38"
                                        x2="140"
                                        y2="38"
                                        stroke="#e2e8f0"
                                        stroke-width="2"
                                    />
                                    <!-- Paper Coming Out -->
                                    <rect
                                        x="60"
                                        y="125"
                                        width="80"
                                        height="32"
                                        rx="3"
                                        fill="white"
                                        stroke="#e2e8f0"
                                        stroke-width="2"
                                    />
                                    <line
                                        x1="72"
                                        y1="135"
                                        x2="128"
                                        y2="135"
                                        stroke="#8b5cf6"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                    />
                                    <line
                                        x1="72"
                                        y1="143"
                                        x2="118"
                                        y2="143"
                                        stroke="#a78bfa"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                    />
                                    <line
                                        x1="72"
                                        y1="151"
                                        x2="108"
                                        y2="151"
                                        stroke="#c4b5fd"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                    />
                                    <!-- Control Panel -->
                                    <rect
                                        x="128"
                                        y="68"
                                        width="40"
                                        height="28"
                                        rx="6"
                                        fill="#1e1b4b"
                                    />
                                    <circle
                                        cx="140"
                                        cy="82"
                                        r="5"
                                        fill="#22c55e"
                                    >
                                        <animate
                                            attributeName="opacity"
                                            values="1;0.5;1"
                                            dur="2s"
                                            repeatCount="indefinite"
                                        />
                                    </circle>
                                    <circle
                                        cx="158"
                                        cy="82"
                                        r="5"
                                        fill="#f97316"
                                    />
                                    <!-- Scanner Line -->
                                    <rect
                                        x="35"
                                        y="95"
                                        width="85"
                                        height="4"
                                        rx="2"
                                        fill="url(#scannerGradient)"
                                    >
                                        <animate
                                            attributeName="x"
                                            values="35;80;35"
                                            dur="3s"
                                            repeatCount="indefinite"
                                        />
                                    </rect>
                                    <!-- Glossy Highlight -->
                                    <rect
                                        x="25"
                                        y="55"
                                        width="60"
                                        height="15"
                                        rx="4"
                                        fill="white"
                                        opacity="0.2"
                                    />
                                    <!-- Gradients -->
                                    <defs>
                                        <linearGradient
                                            id="printerGradient"
                                            x1="20"
                                            y1="50"
                                            x2="20"
                                            y2="130"
                                        >
                                            <stop
                                                offset="0%"
                                                stop-color="#f8fafc"
                                            />
                                            <stop
                                                offset="100%"
                                                stop-color="#e2e8f0"
                                            />
                                        </linearGradient>
                                        <linearGradient
                                            id="scannerGradient"
                                            x1="0%"
                                            y1="0%"
                                            x2="100%"
                                            y2="0%"
                                        >
                                            <stop
                                                offset="0%"
                                                stop-color="#8b5cf6"
                                            />
                                            <stop
                                                offset="50%"
                                                stop-color="#06b6d4"
                                            />
                                            <stop
                                                offset="100%"
                                                stop-color="#8b5cf6"
                                            />
                                        </linearGradient>
                                    </defs>
                                </svg>
                            </div>
                            <!-- Status Badge -->
                            <div
                                class="absolute -right-2 -top-2 flex items-center gap-1.5 rounded-full bg-gradient-to-r from-emerald-500 to-teal-500 px-3 py-1.5 text-xs font-semibold text-white"
                            >
                                <span
                                    class="h-2 w-2 animate-pulse rounded-full bg-white"
                                ></span>
                                Ready
                            </div>
                        </div>
                    </div>

                    <!-- Quick Features -->
                    <div class="flex justify-center gap-6">
                        <div class="flex items-center gap-2">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-100"
                            >
                                <Shield class="h-5 w-5 text-violet-600" />
                            </div>
                            <span class="text-sm font-medium text-slate-700"
                                >Secure</span
                            >
                        </div>
                        <div class="flex items-center gap-2">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-100"
                            >
                                <Zap class="h-5 w-5 text-orange-500" />
                            </div>
                            <span class="text-sm font-medium text-slate-700"
                                >Instant</span
                            >
                        </div>
                        <div class="flex items-center gap-2">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100"
                            >
                                <EyeOff class="h-5 w-5 text-emerald-500" />
                            </div>
                            <span class="text-sm font-medium text-slate-700"
                                >Private</span
                            >
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hero Text -->
            <div class="flex-1 text-center lg:text-left">
                <!-- Decorative element -->
                <div
                    class="mb-6 flex items-center justify-center gap-4 lg:justify-start"
                >
                    <div
                        class="h-px w-12 bg-gradient-to-r from-transparent to-violet-400"
                    ></div>
                    <div
                        class="inline-flex items-center gap-2 rounded-full border border-violet-200/50 bg-gradient-to-r from-violet-50 to-indigo-50 px-5 py-2.5 text-sm font-semibold text-violet-700 shadow-sm"
                    >
                        <Shield class="h-4 w-4" />
                        100% Secure & Private
                    </div>
                    <div
                        class="h-px w-12 bg-gradient-to-l from-transparent to-violet-400"
                    ></div>
                </div>

                <h1
                    class="mb-6 text-5xl font-extrabold leading-[1.1] tracking-tight lg:text-7xl"
                >
                    <span class="block text-slate-800"
                        >Print <span
                            class="relative inline-block bg-gradient-to-r from-violet-600 via-purple-500 to-pink-500 bg-clip-text text-transparent"
                        >
                            Securely
                            <svg
                                class="absolute -bottom-2 left-0 w-full"
                                viewBox="0 0 300 12"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M2 8C50 2 100 2 150 6C200 10 250 4 298 8"
                                    stroke="url(#underline-gradient)"
                                    stroke-width="4"
                                    stroke-linecap="round"
                                />
                                <defs>
                                    <linearGradient
                                        id="underline-gradient"
                                        x1="0"
                                        y1="0"
                                        x2="300"
                                        y2="0"
                                    >
                                        <stop
                                            offset="0%"
                                            stop-color="#8b5cf6"
                                        />
                                        <stop
                                            offset="50%"
                                            stop-color="#a855f7"
                                        />
                                        <stop
                                            offset="100%"
                                            stop-color="#ec4899"
                                        />
                                    </linearGradient>
                                </defs>
                            </svg>
                        </span></span
                    >
                </h1>

                <p
                    class="mb-10 text-lg leading-relaxed text-slate-600 lg:text-xl lg:leading-relaxed"
                >
                    Upload your documents securely, and pay then collect your
                    prints from shops. Doesn't need to expose the your personal
                    details.
                    <span class="font-medium text-violet-600"
                        >No sign-up required</span
                    >, no personal data collected. Your privacy matters.
                </p>

                <div
                    class="flex flex-col gap-4 sm:flex-row sm:justify-center lg:justify-start"
                >
                    <Link
                        href="/scan"
                        class="group btn btn-lg gap-2 bg-gradient-to-r from-violet-500 via-purple-500 to-indigo-600 text-white shadow-xl shadow-violet-500/30 transition-all duration-300 hover:scale-[1.02] hover:shadow-2xl hover:shadow-violet-500/40"
                    >
                        <QrCode class="h-5 w-5" />
                        Scan QR Code
                    </Link>
                    <Link
                        href={toUrl(print())}
                        class="btn btn-lg gap-2 border-2 border-violet-200 bg-white/80 text-violet-600 backdrop-blur-sm transition-all duration-300 hover:border-violet-300 hover:bg-violet-50 hover:shadow-lg"
                    >
                        Upload Files
                        <ArrowRight
                            class="h-5 w-5 transition-transform group-hover:translate-x-1"
                        />
                    </Link>
                </div>

                <!-- Trust indicators -->
                <div
                    class="mt-10 flex flex-wrap items-center justify-center gap-6 lg:justify-start"
                >
                    <div class="flex items-center gap-2 text-sm text-slate-500">
                        <div
                            class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100"
                        >
                            <Shield class="h-4 w-4 text-emerald-600" />
                        </div>
                        <span>256-bit Encryption</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-slate-500">
                        <div
                            class="flex h-8 w-8 items-center justify-center rounded-full bg-violet-100"
                        >
                            <EyeOff class="h-4 w-4 text-violet-600" />
                        </div>
                        <span>Zero Data Storage</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-slate-500">
                        <div
                            class="flex h-8 w-8 items-center justify-center rounded-full bg-orange-100"
                        >
                            <Clock class="h-4 w-4 text-orange-600" />
                        </div>
                        <span>Auto-Delete After Print</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section - Vibrant -->
    <section
        class="relative overflow-hidden bg-gradient-to-r from-violet-600 via-purple-600 to-indigo-600 py-16"
    >
        <div
            class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=''30'' height=''30'' viewBox=''0 0 30 30'' fill=''none'' xmlns=''http://www.w3.org/2000/svg''%3E%3Ccircle cx=''2'' cy=''2'' r=''1'' fill=''white'' fill-opacity=''0.1''/%3E%3C/svg%3E')]"
        ></div>
        <div class="relative mx-auto max-w-6xl px-6 lg:px-12">
            <div class="grid grid-cols-2 gap-8 md:grid-cols-4">
                {#each stats as stat}
                    <div class="group text-center">
                        <div
                            class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-xl bg-white/10 backdrop-blur-sm transition-transform group-hover:scale-110"
                        >
                            <stat.icon class="h-6 w-6 text-white" />
                        </div>
                        <p class="text-3xl font-bold text-white lg:text-4xl">
                            {stat.value}
                        </p>
                        <p class="text-sm font-medium text-white/70">
                            {stat.label}
                        </p>
                    </div>
                {/each}
            </div>
        </div>
    </section>

    <!-- Features Section - Colorful Cards -->
    <section id="features" class="py-28">
        <div class="mx-auto max-w-6xl px-6 lg:px-12">
            <div class="mb-20 text-center">
                <div
                    class="mb-4 inline-flex items-center gap-2 rounded-full bg-violet-100 px-4 py-2 text-sm font-semibold text-violet-700"
                >
                    <Zap class="h-4 w-4" />
                    Features
                </div>
                <h2 class="mb-4 text-4xl font-bold lg:text-5xl">
                    Why Choose
                    <span
                        class="bg-gradient-to-r from-violet-600 to-indigo-600 bg-clip-text text-transparent"
                    >
                        PrintSecure
                    </span>
                    ?
                </h2>
                <p class="mx-auto max-w-2xl text-lg text-slate-600">
                    Experience the future of printing with our secure,
                    convenient, and affordable service.
                </p>
            </div>
            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-4">
                {#each features as feature}
                    <div
                        class="group relative overflow-hidden rounded-3xl border border-slate-100 bg-white p-8 shadow-xl shadow-slate-200/50 transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl"
                    >
                        <!-- Hover Gradient Overlay -->
                        <div
                            class="absolute inset-0 bg-gradient-to-br {feature.gradient} opacity-0 transition-opacity duration-500 group-hover:opacity-5"
                        ></div>

                        <div class="relative">
                            <div
                                class="mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br {feature.gradient} shadow-lg transition-transform duration-300 group-hover:scale-110"
                            >
                                <feature.icon class="h-8 w-8 text-white" />
                            </div>
                            <h3 class="mb-3 text-xl font-bold text-slate-800">
                                {feature.title}
                            </h3>
                            <p class="text-slate-600">{feature.description}</p>
                        </div>
                    </div>
                {/each}
            </div>
        </div>
    </section>

    <!-- How it Works Section - Modern Timeline -->
    <section
        id="how-it-works"
        class="bg-gradient-to-b from-slate-50 to-white py-28"
    >
        <div class="mx-auto max-w-6xl px-6 lg:px-12">
            <div class="mb-20 text-center">
                <div
                    class="mb-4 inline-flex items-center gap-2 rounded-full bg-orange-100 px-4 py-2 text-sm font-semibold text-orange-700"
                >
                    <Sparkles class="h-4 w-4" />
                    Simple Process
                </div>
                <h2 class="mb-4 text-4xl font-bold lg:text-5xl">
                    How It
                    <span
                        class="bg-gradient-to-r from-orange-500 to-pink-500 bg-clip-text text-transparent"
                        >Works</span
                    >
                </h2>
                <p class="mx-auto max-w-2xl text-lg text-slate-600">
                    Get your documents printed in four simple steps.
                </p>
            </div>
            <div
                class="relative grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-4"
            >
                <!-- Connection Line -->
                <div
                    class="absolute left-0 right-0 top-24 hidden h-1 bg-gradient-to-r from-cyan-500 via-violet-500 via-orange-500 to-emerald-500 md:block"
                    style="margin: 0 10%;"
                ></div>

                {#each steps as step}
                    <div class="group relative h-full">
                        <div
                            class="relative flex h-full flex-col rounded-3xl border border-slate-100 bg-white p-6 shadow-xl shadow-slate-200/50 transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl"
                        >
                            <div
                                class="mx-auto mb-5 flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-full bg-gradient-to-br {step.color} shadow-xl transition-transform duration-300 group-hover:scale-110"
                            >
                                <step.icon class="h-8 w-8 text-white" />
                            </div>
                            <div
                                class="absolute -top-3 left-1/2 flex h-7 w-7 -translate-x-1/2 items-center justify-center rounded-full bg-gradient-to-br {step.color} text-xs font-bold text-white shadow-lg"
                            >
                                {step.step}
                            </div>
                            <h3
                                class="mb-2 text-center text-xl font-bold text-slate-800"
                            >
                                {step.title}
                            </h3>
                            <p
                                class="flex-grow text-center text-sm text-slate-600"
                            >
                                {step.description}
                            </p>
                        </div>
                    </div>
                {/each}
            </div>
        </div>
    </section>

    <!-- CTA Section - Gradient with Pattern -->
    <section class="py-24">
        <div class="mx-auto max-w-5xl px-6 lg:px-12">
            <div
                class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-violet-600 via-purple-600 to-indigo-600 p-12 shadow-2xl shadow-violet-500/30 lg:p-16"
            >
                <!-- Pattern Overlay -->
                <div class="absolute inset-0 opacity-10">
                    <svg
                        class="h-full w-full"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <defs>
                            <pattern
                                id="grid"
                                width="40"
                                height="40"
                                patternUnits="userSpaceOnUse"
                            >
                                <circle cx="20" cy="20" r="1.5" fill="white" />
                            </pattern>
                        </defs>
                        <rect width="100%" height="100%" fill="url(#grid)" />
                    </svg>
                </div>

                <div class="relative text-center">
                    <h2 class="mb-6 text-4xl font-bold text-white lg:text-5xl">
                        Ready to Print Anonymously?
                    </h2>
                    <p class="mx-auto mb-10 max-w-xl text-lg text-white/80">
                        No sign-up. No personal data. Just upload, pay, and
                        collect your prints.
                    </p>
                    <div class="flex justify-center">
                        <Link
                            href={toUrl(print())}
                            class="btn btn-lg bg-white text-violet-700 shadow-xl hover:bg-slate-100"
                        >
                            Start Printing Now
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer - Clean & Modern -->
    <footer class="border-t border-slate-200 bg-slate-50 py-16">
        <div class="mx-auto max-w-6xl px-6 lg:px-12">
            <div class="grid gap-12 md:grid-cols-4">
                <div class="md:col-span-2">
                    <a
                        href="/"
                        class="mb-6 flex items-center gap-3 text-xl font-bold"
                    >
                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-indigo-600 shadow-lg shadow-violet-500/30"
                        >
                            <Printer class="h-5 w-5 text-white" />
                        </div>
                        <span
                            class="bg-gradient-to-r from-violet-600 to-indigo-600 bg-clip-text text-transparent"
                            >PrintSecure</span
                        >
                    </a>
                    <p class="max-w-sm text-slate-600">
                        Printing made simple. Upload, Pay, and Collect - no
                        personal data stored.
                    </p>
                </div>
                <div>
                    <h4 class="mb-4 font-bold text-slate-800">Quick Links</h4>
                    <ul class="space-y-3 text-slate-600">
                        <li>
                            <a
                                href="#features"
                                class="transition-colors hover:text-violet-600"
                                >Features</a
                            >
                        </li>
                        <li>
                            <a
                                href="#how-it-works"
                                class="transition-colors hover:text-violet-600"
                                >How it Works</a
                            >
                        </li>
                    </ul>
                </div>
                <div>
                    <h4 class="mb-4 font-bold text-slate-800">Support</h4>
                    <ul class="space-y-3 text-slate-600">
                        <li>
                            <a
                                href="javascript:void(0);"
                                class="transition-colors hover:text-violet-600"
                                >Help Center</a
                            >
                        </li>
                        <li>
                            <a
                                href="javascript:void(0);"
                                class="transition-colors hover:text-violet-600"
                                >Contact Us</a
                            >
                        </li>
                        <li>
                            <a
                                href="javascript:void(0);"
                                class="transition-colors hover:text-violet-600"
                                >Privacy Policy</a
                            >
                        </li>
                    </ul>
                </div>
            </div>
            <div
                class="mt-12 border-t border-slate-200 pt-8 text-center text-sm text-slate-500"
            >
                <p>
                    &copy; {new Date().getFullYear()} PrintSecure. All rights reserved.
                </p>
            </div>
        </div>
    </footer>
</div>
