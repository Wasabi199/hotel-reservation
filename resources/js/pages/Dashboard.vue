<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { BedDouble, Building2, CalendarCheck, DoorOpen, Plus } from '@lucide/vue';
import { ref } from 'vue';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import user from '@/routes/user';
import reservations from '@/routes/user/reservations';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: user.dashboard(),
            },
        ],
    },
});

defineProps<{
    hotelsCount: number;
    roomsCount: number;
    reservationsCount: number;
    hotels: {
        id: number;
        name: string;
        address: string;
        roomsCount: number;
        image: string | null;
    }[];
    rooms: {
        id: number;
        roomNumber: string;
        type: { id: number; text: string; color: string | null };
        capacity: number;
        price: number;
        isActive: boolean;
        image: string | null;
        hotelName: string;
    }[];
}>();

const activeTab = ref<'hotels' | 'rooms'>('hotels');
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
        <Heading title="Dashboard" description="Welcome back! Manage your hotel reservations." />

        <div class="grid gap-4 md:grid-cols-3">
            <Card>
                <CardHeader class="flex flex-row items-center gap-3 pb-2">
                    <Building2 class="size-8 text-primary" />
                    <div>
                        <CardTitle class="text-2xl">{{ hotelsCount }}</CardTitle>
                        <CardDescription>Total Hotels</CardDescription>
                    </div>
                </CardHeader>
            </Card>
            <Card>
                <CardHeader class="flex flex-row items-center gap-3 pb-2">
                    <BedDouble class="size-8 text-primary" />
                    <div>
                        <CardTitle class="text-2xl">{{ roomsCount }}</CardTitle>
                        <CardDescription>Total Rooms</CardDescription>
                    </div>
                </CardHeader>
            </Card>
            <Card>
                <CardHeader class="flex flex-row items-center gap-3 pb-2">
                    <CalendarCheck class="size-8 text-primary" />
                    <div>
                        <CardTitle class="text-2xl">{{ reservationsCount }}</CardTitle>
                        <CardDescription>Total Reservations</CardDescription>
                    </div>
                </CardHeader>
            </Card>
        </div>

        <Card>
            <CardHeader>
                <div class="flex items-center justify-between">
                    <CardTitle>Overview</CardTitle>
                    <div class="flex gap-1 rounded-lg border p-0.5">
                        <button
                            :class="[
                                'rounded-md px-3 py-1 text-sm font-medium transition-colors',
                                activeTab === 'hotels'
                                    ? 'bg-primary text-primary-foreground'
                                    : 'text-muted-foreground hover:text-foreground',
                            ]"
                            @click="activeTab = 'hotels'"
                        >
                            Hotels
                        </button>
                        <button
                            :class="[
                                'rounded-md px-3 py-1 text-sm font-medium transition-colors',
                                activeTab === 'rooms'
                                    ? 'bg-primary text-primary-foreground'
                                    : 'text-muted-foreground hover:text-foreground',
                            ]"
                            @click="activeTab = 'rooms'"
                        >
                            Rooms
                        </button>
                    </div>
                </div>
                <CardDescription>Browse all hotels and rooms</CardDescription>
            </CardHeader>
            <CardContent>
                <div v-if="activeTab === 'hotels'" class="space-y-3">
                    <div v-for="hotel in hotels" :key="hotel.id" class="flex items-center gap-4 rounded-lg border p-3">
                        <div class="size-16 shrink-0 overflow-hidden rounded-md bg-muted">
                            <img
                                v-if="hotel.image"
                                :src="hotel.image"
                                :alt="hotel.name"
                                class="h-full w-full object-cover"
                            />
                            <div v-else class="flex h-full items-center justify-center text-muted-foreground text-xs">
                                No Image
                            </div>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-medium">{{ hotel.name }}</p>
                            <p class="truncate text-xs text-muted-foreground">{{ hotel.address }}</p>
                            <div class="mt-1 flex items-center gap-2">
                                <DoorOpen class="size-3 text-muted-foreground" />
                                <span class="text-xs text-muted-foreground">{{ hotel.roomsCount }} room{{ hotel.roomsCount !== 1 ? 's' : '' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="space-y-3">
                    <div v-for="room in rooms" :key="room.id" class="flex items-center gap-4 rounded-lg border p-3">
                        <div class="size-16 shrink-0 overflow-hidden rounded-md bg-muted">
                            <img
                                v-if="room.image"
                                :src="room.image"
                                :alt="'Room ' + room.roomNumber"
                                class="h-full w-full object-cover"
                            />
                            <div v-else class="flex h-full items-center justify-center text-muted-foreground text-xs">
                                No Image
                            </div>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <p class="truncate font-medium">Room #{{ room.roomNumber }}</p>
                                <Badge :class="'badge-' + (room.type?.color ?? 'outline')" class="shrink-0">
                                    {{ room.type?.text }}
                                </Badge>
                            </div>
                            <p class="truncate text-xs text-muted-foreground">{{ room.hotelName }}</p>
                            <div class="mt-1 flex items-center gap-3">
                                <span class="text-xs text-muted-foreground">Up to {{ room.capacity }} guest{{ room.capacity !== 1 ? 's' : '' }}</span>
                                <span class="text-xs font-medium">₱{{ room.price }}/night</span>
                            </div>
                        </div>
                    </div>
                </div>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>Quick Actions</CardTitle>
                <CardDescription>Common tasks</CardDescription>
            </CardHeader>
            <CardContent class="space-y-3">
                <Link :href="reservations.create.url()">
                    <Button class="w-full justify-start">
                        <Plus class="mr-2 size-4" />
                        New Reservation
                    </Button>
                </Link>
                <Link :href="reservations.index.url()">
                    <Button variant="outline" class="w-full justify-start">
                        <CalendarCheck class="mr-2 size-4" />
                        My Reservations
                    </Button>
                </Link>
            </CardContent>
        </Card>
    </div>
</template>
