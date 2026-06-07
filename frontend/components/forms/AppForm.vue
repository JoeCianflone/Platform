<template>
    <form class="ds-form" @submit="$emit('submit', $event)">
        <slot />
    </form>
</template>

<script setup lang="ts">
defineEmits<{ submit: [event: SubmitEvent] }>()
</script>

<style scoped>
.ds-form {
    display: contents;
}
</style>
