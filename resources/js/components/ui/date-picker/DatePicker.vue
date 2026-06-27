<script setup lang="ts">
import { VueDatePicker } from '@vuepic/vue-datepicker';

const props = withDefaults(defineProps<{
    modelValue?: string;
    placeholder?: string;
    minDate?: Date;
    maxDate?: Date;
}>(), {
    placeholder: 'Pick a date',
});

const emit = defineEmits<{
    (e: 'update:modelValue', val: string | null): void;
}>();

function onDateChange(date: Date | null) {
    if (!date) {
        emit('update:modelValue', '');
        return;
    }
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const d = String(date.getDate()).padStart(2, '0');
    emit('update:modelValue', `${y}-${m}-${d}`);
}

function parseValue(): Date | null {
    if (!props.modelValue) return null;
    const parts = props.modelValue.split('-');
    if (parts.length !== 3) return null;
    const d = new Date(+parts[0], +parts[1] - 1, +parts[2]);
    return d;
}
</script>

<template>
    <VueDatePicker
        :model-value="parseValue()"
        :placeholder="placeholder"
        :min-date="minDate"
        :max-date="maxDate"
        :format="'yyyy-MM-dd'"
        :enable-time-picker="false"
        auto-apply
        class="w-full"
        @update:model-value="onDateChange"
    />
</template>

<style>
.dp__theme_light {
    --dp-background: var(--background);
    --dp-text-color: var(--foreground);
    --dp-border-color: var(--border);
    --dp-border-color-focus: var(--ring);
    --dp-primary-color: var(--primary);
    --dp-primary-text-color: var(--primary-foreground);
    --dp-secondary-color: var(--secondary);
    --dp-secondary-text-color: var(--secondary-foreground);
    --dp-muted-color: var(--muted);
    --dp-muted-text-color: var(--muted-foreground);
    --dp-today-color: var(--accent);
    --dp-cell-size: 36px;
    --dp-input-padding: 6px 12px;
    --dp-menu-min-width: 280px;
}
</style>
