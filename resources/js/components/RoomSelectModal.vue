<script setup lang="ts">
import { computed } from 'vue';
import { X } from '@lucide/vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Skeleton } from '@/components/ui/skeleton';

export type RoomOption = {
    id: number;
    room_number: string;
    type: { id: number; text: string; color: string | null };
    capacity: number;
    price: number;
    is_active: boolean;
    image: string | null;
    hotel: { id: number; name: string };
};

const props = defineProps<{
    open: boolean;
    rooms: RoomOption[];
    loading?: boolean;
}>();

const emit = defineEmits<{
    (e: 'update:open', val: boolean): void;
    (e: 'select', room: RoomOption): void;
}>();

function formatPrice(price: number) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'PHP',
    }).format(price);
}

const loadingSkeletons = computed(() => Array.from({ length: 6 }));
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent class="max-h-[95vh] sm:max-w-[95vw] p-0" :show-close-button="false">
            <DialogHeader class="sticky top-0 z-20 bg-background border-b px-6 py-4">
                <DialogTitle>Select a Room</DialogTitle>
                <DialogDescription>
                    Choose an available room for your reservation.
                </DialogDescription>
                <DialogClose as-child>
                    <Button variant="ghost" size="icon" class="absolute right-4 top-4 z-30">
                        <X class="size-4" />
                    </Button>
                </DialogClose>
            </DialogHeader>

            <div class="overflow-y-auto p-6 pt-4" style="max-height: calc(95vh - 80px);">
            <div v-if="loading" class="grid gap-4 sm:grid-cols-2">
                <Card v-for="i in loadingSkeletons" :key="i" class="overflow-hidden">
                    <Skeleton class="h-40 w-full rounded-none" />
                    <CardHeader class="p-4 pb-2">
                        <Skeleton class="h-5 w-3/4" />
                        <Skeleton class="mt-1 h-4 w-1/2" />
                    </CardHeader>
                    <CardContent class="p-4 pt-0">
                        <Skeleton class="h-4 w-full" />
                    </CardContent>
                </Card>
            </div>

            <div v-else-if="rooms.length === 0" class="py-12 text-center">
                <p class="text-muted-foreground text-sm">No available rooms found for the selected dates.</p>
            </div>

            <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <Card
                    v-for="room in rooms"
                    :key="room.id"
                    class="cursor-pointer overflow-hidden transition-colors hover:border-primary"
                    @click="emit('select', room)"
                >
                    <div class="aspect-video w-full overflow-hidden bg-muted">
                        <img
                            v-if="room.image"
                            :src="room.image"
                            :alt="'Room ' + room.room_number"
                            class="h-full w-full object-cover"
                        />
                        <div v-else class="flex h-full items-center justify-center text-muted-foreground text-sm">
                            No Image
                        </div>
                    </div>
                    <CardHeader class="p-4 pb-2">
                        <div class="flex items-start justify-between gap-2">
                            <CardTitle class="text-base">
                                {{ room.hotel.name }} - #{{ room.room_number }}
                            </CardTitle>
                            <Badge :class="'badge-' + (room.type?.color ?? 'outline')" class="shrink-0">
                                {{ room.type?.text }}
                            </Badge>
                        </div>
                        <CardDescription class="text-xs">
                            {{ room.hotel.name }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="flex items-center justify-between p-4 pt-0">
                        <span class="text-xs text-muted-foreground">
                            Up to {{ room.capacity }} guest{{ room.capacity !== 1 ? 's' : '' }}
                        </span>
                        <span class="text-sm font-semibold">{{ formatPrice(room.price) }}/night</span>
                    </CardContent>
                </Card>
            </div>
            </div>
        </DialogContent>
    </Dialog>
</template>
