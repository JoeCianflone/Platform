<script lang="ts" setup>
// Props & Emits ......................................................
defineEmits<{ submit: [event: SubmitEvent] }>()
</script>

<template>
    <form class="ds-form" @submit="$emit('submit', $event)">
        <slot />
    </form>
</template>

<style scoped>
.ds-form {
    display: contents;
}
</style>
