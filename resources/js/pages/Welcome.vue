<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Building2, CalendarCheck, LogIn, Menu, UserPlus, X } from '@lucide/vue';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { login, register } from '@/routes';
import user from '@/routes/user';
import reservations from '@/routes/user/reservations';

defineProps<{
    canLogin?: boolean;
    canRegister?: boolean;
}>();
</script>

<template>
    <Head title="Welcome | Hotel Reservation" />

    <div class="flex min-h-screen flex-col bg-background">
        <!-- Navigation -->
        <header class="sticky top-0 z-50 border-b bg-background/95 backdrop-blur">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <Building2 class="size-6 text-primary" />
                    <span class="text-lg font-semibold">Hotel Reservation</span>
                </div>

                <nav v-if="!$page.props.auth.user" class="flex items-center gap-4">
                    <Link :href="login()">
                        <Button variant="ghost">Log in</Button>
                    </Link>
                    <Link :href="register()">
                        <Button>Get Started</Button>
                    </Link>
                </nav>

                <nav v-else class="flex items-center gap-4">
                    <Link :href="reservations.index.url()">
                        <Button variant="outline">
                            <CalendarCheck class="mr-2 size-4" />
                            My Reservations
                        </Button>
                    </Link>
                    <Link :href="user.dashboard()">
                        <Button>Dashboard</Button>
                    </Link>
                </nav>
            </div>
        </header>

        <!-- Hero Section -->
        <main class="flex-1">
            <section class="mx-auto max-w-7xl px-4 py-24 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h1 class="text-4xl font-bold tracking-tight sm:text-6xl">
                        Book Your Perfect
                        <span class="text-primary">Stay</span>
                    </h1>
                    <p class="mt-6 text-lg leading-8 text-muted-foreground max-w-2xl mx-auto">
                        Experience comfort and luxury at our hotels. Browse available rooms, 
                        choose your dates, and reserve your stay in just a few clicks.
                    </p>

                    <div class="mt-10 flex items-center justify-center gap-4">
                        <Link v-if="$page.props.auth.user" :href="reservations.create.url()">
                            <Button size="lg" class="text-base">
                                <CalendarCheck class="mr-2 size-5" />
                                Book a Room Now
                            </Button>
                        </Link>
                        <Link v-else :href="register()">
                            <Button size="lg" class="text-base">
                                <UserPlus class="mr-2 size-5" />
                                Create an Account
                            </Button>
                        </Link>
                        <Link v-if="$page.props.auth.user" :href="reservations.index.url()">
                            <Button variant="outline" size="lg" class="text-base">
                                View My Reservations
                            </Button>
                        </Link>
                        <Link v-else :href="login()">
                            <Button variant="outline" size="lg" class="text-base">
                                <LogIn class="mr-2 size-5" />
                                Sign In
                            </Button>
                        </Link>
                    </div>
                </div>
            </section>

            <!-- Features -->
            <section class="border-t bg-muted/50">
                <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                    <div class="grid gap-8 md:grid-cols-3">
                        <div class="rounded-lg border bg-card p-6 text-center">
                            <CalendarCheck class="mx-auto mb-4 size-10 text-primary" />
                            <h3 class="mb-2 text-lg font-semibold">Easy Booking</h3>
                            <p class="text-sm text-muted-foreground">
                                Select your dates and room in minutes. 
                                Instant confirmation for your reservation.
                            </p>
                        </div>
                        <div class="rounded-lg border bg-card p-6 text-center">
                            <Building2 class="mx-auto mb-4 size-10 text-primary" />
                            <h3 class="mb-2 text-lg font-semibold">Premium Rooms</h3>
                            <p class="text-sm text-muted-foreground">
                                Choose from Single, Double, Suite, or Deluxe 
                                rooms to match your needs.
                            </p>
                        </div>
                        <div class="rounded-lg border bg-card p-6 text-center">
                            <LogIn class="mx-auto mb-4 size-10 text-primary" />
                            <h3 class="mb-2 text-lg font-semibold">Manage Online</h3>
                            <p class="text-sm text-muted-foreground">
                                View, cancel, or modify your reservations 
                                anytime from your dashboard.
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="border-t py-6 text-center text-sm text-muted-foreground">
            &copy; {{ new Date().getFullYear() }} Hotel Reservation System. All rights reserved.
        </footer>
    </div>
</template>
