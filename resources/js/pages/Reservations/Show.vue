<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Archive, ArrowLeft, TriangleAlert } from '@lucide/vue';
import { ref } from 'vue';
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
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Separator } from '@/components/ui/separator';
import Heading from '@/components/Heading.vue';
import { useDateFormat } from '@/composables/useDateFormat';
import reservations from '@/routes/user/reservations';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Reservations',
                href: reservations.index.url(),
            },
            {
                title: 'Reservation Details',
                href: '',
            },
        ],
    },
});

const props = defineProps<{
    resource: {
        id: number;
        name: string | null;
        user: { id: number; name: string; email: string } | null;
        status: { id: number; text: string; color: string | null };
        amount: number;
        checkInAt: string;
        checkOutAt: string;
        duration: number;
        createdAt: string;
        deletedAt: string | null;
    };
}>();

const showConfirm = ref(false);

function formatAmount(amount: number) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'PHP',
    }).format(amount);
}

function confirmCancel() {
    const form = reservations.archive.form(props.resource.id);
    showConfirm.value = false;
    router.visit(form.action, {
        method: form.method as 'post',
        preserveScroll: true,
        preserveState: false,
    });
}

const { formatDate } = useDateFormat();
</script>

<template>
    <Head :title="props.resource.name ?? 'Reservation'" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Link :href="reservations.index.url()">
                    <Button variant="ghost" size="icon" aria-label="Back">
                        <ArrowLeft class="size-4" />
                    </Button>
                </Link>
                <Heading title="Reservation Details" :description="props.resource?.name ?? 'Reservation #' + props.resource?.id" />
            </div>
            <Badge :class="'badge-' + (props.resource.status?.color ?? 'outline')" class="text-sm px-3 py-1">
                {{ props.resource.status?.text }}
            </Badge>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <Card>
                <CardHeader>
                    <CardTitle>Stay Information</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div>
                        <CardDescription>Check In</CardDescription>
                        <p class="font-medium">{{ formatDate(props.resource.checkInAt) }}</p>
                    </div>
                    <div>
                        <CardDescription>Check Out</CardDescription>
                        <p class="font-medium">{{ formatDate(props.resource.checkOutAt) }}</p>
                    </div>
                    <div>
                        <CardDescription>Duration</CardDescription>
                        <p class="font-medium">
                            {{ props.resource.duration }} night{{ props.resource.duration !== 1 ? 's' : '' }}
                        </p>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Booking Summary</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div>
                        <CardDescription>Room</CardDescription>
                        <p class="font-medium">{{ props.resource.name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <CardDescription>Guest</CardDescription>
                        <p class="font-medium">{{ props.resource.user?.name ?? 'N/A' }}</p>
                        <p v-if="props.resource.user?.email" class="text-sm text-muted-foreground">{{ props.resource.user.email }}</p>
                    </div>
                    <Separator />
                    <div class="flex items-center justify-between">
                        <CardDescription>Total Amount</CardDescription>
                        <p class="text-lg font-semibold">{{ formatAmount(props.resource.amount) }}</p>
                    </div>
                </CardContent>
                <CardContent v-if="!props.resource.deletedAt" class="flex items-center gap-4 border-t pt-6">
                    <Button variant="destructive" @click="showConfirm = true">
                        <Archive class="size-4" />
                        Cancel Reservation
                    </Button>
                </CardContent>
            </Card>
        </div>
    </div>

    <Dialog :open="showConfirm" @update:open="showConfirm = $event">
        <DialogContent>
            <DialogHeader>
                <div class="flex items-center gap-3">
                    <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900">
                        <TriangleAlert class="size-5 text-red-600 dark:text-red-400" />
                    </span>
                    <DialogTitle>Cancel Reservation</DialogTitle>
                </div>
                <DialogDescription>
                    Are you sure you want to cancel this reservation? This action can be reversed later.
                </DialogDescription>
            </DialogHeader>
            <DialogFooter class="mt-4">
                <Button variant="outline" @click="showConfirm = false">Keep Reservation</Button>
                <Button variant="destructive" @click="confirmCancel">Yes, Cancel</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
