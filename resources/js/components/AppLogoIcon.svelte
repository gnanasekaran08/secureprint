<script lang="ts">
    import { page } from '@inertiajs/svelte';
    let {
        class: className = 'w-90',
        ...rest
    }: {
        class?: string;
        [key: string]: unknown;
    } = $props();
</script>

<img
    src={$page.props.app_logo}
    alt="App Logo"
    class={className}
    {...rest}

/>
