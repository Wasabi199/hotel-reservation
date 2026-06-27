<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import reservations, { create, index, store } from '@/routes/user/reservations';
import ReservationForm from './ReservationForm.vue';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Reservations',
                href: index.url(),
            },
            {
                title: 'Create Reservation',
                href: create.url(),
            },
        ],
    },
});

const props = defineProps<{
    paymentMethod: { label: string; value: string }[];
}>();

const form = useForm({
    room_id: null as number | null,
    check_in_at: '',
    check_out_at: '',
    guest: 1,
    payment_method: '',
});

function submit() {
    const { url, method } = store();
    form.post(url);
}
</script>

<template>
    <Head title="Create Reservation" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <Heading title="Create Reservation" description="Book a new room reservation" />

        <Card>
            <CardHeader>
                <CardTitle>Reservation Details</CardTitle>
            </CardHeader>
            <CardContent class="space-y-6">
                <ReservationForm
                    :payment-method="paymentMethod"
                    :form="form"
                >
                    <template #buttons="{ processing: btnProcessing }">
                        <div class="flex items-center gap-4">
                            <Button :disabled="btnProcessing" @click="submit">
                                Submit Reservation
                            </Button>
                        </div>
                    </template>
                </ReservationForm>
            </CardContent>
        </Card>
    </div>
</template>
