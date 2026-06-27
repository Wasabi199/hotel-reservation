<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus } from '@lucide/vue';
import { ref } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import Heading from '@/components/Heading.vue';
import { useDateFormat } from '@/composables/useDateFormat';
import user from '@/routes/user';
import reservations from '@/routes/user/reservations';

type Reservation = {
    id: number;
    name: string | null;
    user: { id: number; name: string; email: string } | null;
    status: { id: number; text: string; color: string | null };
    amount: number;
    duration: number;
    createdAt: string;
    deletedAt: string | null;
};

const props = defineProps<{
    data: {
        data: Reservation[];
        current_page: number;
        from: number | null;
        to: number | null;
        per_page: number;
        total: number;
        last_page: number;
        path: string;
    };
    trashed?: string;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Reservations',
                href: user.dashboard(),
            },
        ],
    },
});

const tabs = [
    { label: 'All', value: '' },
    { label: 'Archived', value: 'only' },
];

const { formatDate } = useDateFormat();

function formatAmount(amount: number) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'PHP',
    }).format(amount);
}

function goToPage(page: number) {
    router.get(reservations.index.url({ query: { page, trashed: props.trashed ?? '' } }));
}

function switchTab(trashed: string) {
    router.get(reservations.index.url({ query: { trashed } }));
}
</script>

<template>
    <Head title="Reservations" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <div class="flex items-center justify-between">
            <Heading title="Reservations" description="Manage your hotel reservations" />
            <Link :href="reservations.create.url()">
                <Button>
                    <Plus class="size-4" />
                    New Reservation
                </Button>
            </Link>
        </div>

        <Card>
            <CardHeader>
                <div class="flex items-center justify-between">
                    <CardTitle>All Reservations</CardTitle>
                    <div class="flex gap-1 rounded-lg border p-0.5">
                        <button
                            v-for="tab in tabs"
                            :key="tab.value"
                            :class="[
                                'rounded-md px-3 py-1 text-sm font-medium transition-colors',
                                (trashed ?? '') === tab.value
                                    ? 'bg-primary text-primary-foreground'
                                    : 'text-muted-foreground hover:text-foreground',
                            ]"
                            @click="switchTab(tab.value)"
                        >
                            {{ tab.label }}
                        </button>
                    </div>
                </div>
            </CardHeader>
            <CardContent class="p-0">
                <div v-if="data.data.length === 0" class="flex flex-col items-center gap-2 py-12 text-center">
                    <p class="text-muted-foreground text-sm">No reservations found.</p>
                    <Link v-if="!trashed" :href="reservations.create.url()">
                        <Button variant="outline" size="sm">
                            <Plus class="size-4" />
                            Create your first reservation
                        </Button>
                    </Link>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b text-left text-muted-foreground">
                                <th class="px-6 py-3 font-medium">Room</th>
                                <th class="px-6 py-3 font-medium">Guest</th>
                                <th class="px-6 py-3 font-medium">Status</th>
                                <th class="px-6 py-3 font-medium">Amount</th>
                                <th class="px-6 py-3 font-medium">Duration</th>
                                <th class="px-6 py-3 font-medium">Date Created</th>
                                <th class="px-6 py-3 font-medium text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="reservation in data.data"
                                :key="reservation.id"
                                :class="[
                                    'border-b last:border-b-0 hover:bg-muted/50 transition-colors',
                                    reservation.deletedAt ? 'opacity-60' : '',
                                ]"
                            >
                                <td class="px-6 py-3">
                                    <Link
                                        :href="reservations.show.url(reservation.id)"
                                        class="font-medium hover:underline"
                                    >
                                        {{ reservation.name ?? 'N/A' }}
                                    </Link>
                                </td>
                                <td class="px-6 py-3 text-muted-foreground">
                                    {{ reservation.user?.name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-3">
                                    <Badge :class="'badge-' + (reservation.status?.color ?? 'outline')">
                                        {{ reservation.status?.text }}
                                    </Badge>
                                </td>
                                <td class="px-6 py-3">
                                    {{ formatAmount(reservation.amount) }}
                                </td>
                                <td class="px-6 py-3 text-muted-foreground">
                                    {{ reservation.duration }} night{{ reservation.duration !== 1 ? 's' : '' }}
                                </td>
                                <td class="px-6 py-3 text-muted-foreground">
                                    {{ formatDate(reservation.createdAt) }}
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <Link
                                            v-if="!reservation.deletedAt"
                                            :href="reservations.show.url(reservation.id)"
                                        >
                                            <Button variant="ghost" size="sm">View</Button>
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </CardContent>
        </Card>

        <div v-if="data.total > data.per_page" class="flex items-center justify-between gap-2">
            <span class="text-sm text-muted-foreground">
                Showing {{ data.from }} to {{ data.to }} of {{ data.total }}
            </span>
            <div class="flex items-center gap-2">
                <Button
                    variant="outline"
                    size="sm"
                    :disabled="data.current_page <= 1"
                    @click="goToPage(data.current_page - 1)"
                >
                    Previous
                </Button>
                <span class="text-sm text-muted-foreground">
                    Page {{ data.current_page }} of {{ data.last_page }}
                </span>
                <Button
                    variant="outline"
                    size="sm"
                    :disabled="data.current_page >= data.last_page"
                    @click="goToPage(data.current_page + 1)"
                >
                    Next
                </Button>
            </div>
        </div>
    </div>
</template>
