<template>
    <div class="smi-loader" :class="{'success': success}">
        <svg viewBox="25 25 50 50">
            <circle class="loader_background" cx="50" cy="50" r="22" stroke-width="0"/>
            <circle class="loader_rotation" cx="50" cy="50" r="20" fill="none" stroke-width="4"/>
            <path class="loader_path" d="m48,58l15,-20" stroke-dasharray="20 20" stroke-dashoffset="20"/>
            <path class="loader_path" d="m48,58l-8,-6" stroke-dasharray="8 8" stroke-dashoffset="8"/>
        </svg>
    </div>
</template>

<script>
    export default {
        props: ['fail', 'success']
    }
</script>