<script setup lang="ts">
import { ref, watch } from 'vue';
import InputError from '@/components/InputError.vue';
import { Label } from '@/components/ui/label';
import type { RoomOption } from '@/components/RoomSelectModal.vue';
import RoomSelectModal from '@/components/RoomSelectModal.vue';

const props = defineProps<{
    paymentMethod: { label: string; value: string }[];
    form: any;
}>();

const availableRoomsUrl = '/user/available-rooms';

const showRoomPicker = ref(false);
const selectedRoom = ref<RoomOption | null>(null);
const availableRooms = ref<RoomOption[]>([]);
const roomsLoading = ref(false);

const today = new Date().toISOString().split('T')[0];

function fetchAvailableRooms() {
    const { check_in_at, check_out_at, guest } = props.form;
    if (!check_in_at || !check_out_at) {
        availableRooms.value = [];
        return;
    }

    roomsLoading.value = true;

    fetch(`${availableRoomsUrl}?${new URLSearchParams({
        check_in_at,
        check_out_at,
        ...(guest ? { guest: String(guest) } : {}),
    })}`)
        .then((res) => res.json())
        .then((data) => { availableRooms.value = data.data ?? []; })
        .catch(() => { availableRooms.value = []; })
        .finally(() => { roomsLoading.value = false; });
}

watch(() => props.form.check_in_at, () => { selectedRoom.value = null; fetchAvailableRooms(); });
watch(() => props.form.check_out_at, () => { selectedRoom.value = null; fetchAvailableRooms(); });
watch(() => props.form.guest, fetchAvailableRooms);

function openRoomPicker() {
    if (availableRooms.value.length === 0 && !roomsLoading.value) fetchAvailableRooms();
    showRoomPicker.value = true;
}

function selectRoom(room: RoomOption) {
    selectedRoom.value = room;
    props.form.room_id = room.id;
    showRoomPicker.value = false;
}
</script>

<template>
    <div class="flex gap-4">
        <div class="grid flex-1 gap-2">
            <Label>Check In</Label>
            <input
                type="date"
                :min="today"
                :value="props.form.check_in_at"
                @input="props.form.check_in_at = ($event.target as HTMLInputElement).value"
                class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-base shadow-xs transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm [color-scheme:light] dark:[color-scheme:dark]"
            />
            <InputError :message="props.form.errors?.check_in_at" />
        </div>

        <div class="grid flex-1 gap-2">
            <Label>Check Out</Label>
            <input
                type="date"
                :min="props.form.check_in_at || today"
                :value="props.form.check_out_at"
                @input="props.form.check_out_at = ($event.target as HTMLInputElement).value"
                class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-base shadow-xs transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm [color-scheme:light] dark:[color-scheme:dark]"
            />
            <InputError :message="props.form.errors?.check_out_at" />
        </div>
    </div>

    <div class="grid gap-2">
        <Label for="guest">Number of Guests</Label>
        <input
            id="guest"
            type="number"
            min="1"
            :value="props.form.guest"
            @input="props.form.guest = Number(($event.target as HTMLInputElement).value) || 1"
            class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-base shadow-xs transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
        />
        <InputError :message="props.form.errors?.guest" />
    </div>

    <div class="grid gap-2">
        <Label>Room</Label>
        <div
            class="flex cursor-pointer items-center justify-between rounded-md border px-3 py-2 text-sm transition-colors hover:border-primary"
            :class="{ 'pointer-events-none opacity-50': !props.form.check_in_at || !props.form.check_out_at }"
            @click="openRoomPicker"
        >
            <span v-if="selectedRoom" class="font-medium">
                {{ selectedRoom.hotel.name }} - #{{ selectedRoom.room_number }}
            </span>
            <span v-else class="text-muted-foreground">
                {{ !props.form.check_in_at || !props.form.check_out_at ? 'Select dates first' : 'Click to select a room' }}
            </span>
            <span class="text-xs text-muted-foreground">Browse &rarr;</span>
        </div>
        <InputError :message="props.form.errors?.room_id" />
    </div>

    <div class="grid gap-2">
        <Label for="payment_method">Payment Method</Label>
        <select
            id="payment_method"
            :value="props.form.payment_method"
            @input="props.form.payment_method = ($event.target as HTMLSelectElement).value"
            class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-base shadow-xs transition-colors focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
        >
            <option value="" disabled>Select payment method</option>
            <option
                v-for="option in paymentMethod"
                :key="option.value"
                :value="option.value"
            >
                {{ option.label }}
            </option>
        </select>
        <InputError :message="props.form.errors?.payment_method" />
    </div>

    <slot name="buttons" :processing="props.form.processing" />

    <RoomSelectModal
        :open="showRoomPicker"
        :rooms="availableRooms"
        :loading="roomsLoading"
        @update:open="showRoomPicker = $event"
        @select="selectRoom"
    />
</template>
