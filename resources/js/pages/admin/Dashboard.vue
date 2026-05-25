<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Car as CarIcon, CheckCircle, Eye, EyeOff, Star, ClipboardList, Clock, UserCheck, Bell, X } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import FlashMessage from '@/components/FlashMessage.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import type { Car } from '@/types';

interface Booking {
    id: number;
    customer_name: string;
    customer_phone: string;
    rental_date: string | null;
    status: string;
    created_at: string;
    car: {
        id: number;
        name: string;
        brand: string;
        images?: { image_path: string; is_primary: boolean }[];
    };
}

const props = defineProps<{
    stats: {
        totalCars: number;
        availableCars: number;
        featuredCars: number;
        unavailableCars: number;
        totalBookings: number;
        pendingBookings: number;
        followUpBookings: number;
        approvedBookings: number;
    };
    recentCars: Car[];
    recentBookings: Booking[];
}>();

const page = usePage();

const carStatCards = [
    { key: 'totalCars', label: 'Total Mobil', icon: CarIcon, color: 'text-blue-600 dark:text-blue-400', bg: 'bg-blue-500/10' },
    { key: 'availableCars', label: 'Tersedia', icon: CheckCircle, color: 'text-emerald-600 dark:text-emerald-400', bg: 'bg-emerald-500/10' },
    { key: 'featuredCars', label: 'Unggulan', icon: Star, color: 'text-amber-600 dark:text-amber-400', bg: 'bg-amber-500/10' },
    { key: 'unavailableCars', label: 'Tidak Tersedia', icon: EyeOff, color: 'text-red-600 dark:text-red-400', bg: 'bg-red-500/10' },
] as const;

const bookingStatCards = [
    { key: 'totalBookings', label: 'Total Pesanan', icon: ClipboardList, color: 'text-indigo-600 dark:text-indigo-400', bg: 'bg-indigo-500/10' },
    { key: 'pendingBookings', label: 'Baru Masuk', icon: Bell, color: 'text-blue-600 dark:text-blue-400', bg: 'bg-blue-500/10' },
    { key: 'followUpBookings', label: 'Follow Up', icon: Clock, color: 'text-amber-600 dark:text-amber-400', bg: 'bg-amber-500/10' },
    { key: 'approvedBookings', label: 'Disetujui', icon: UserCheck, color: 'text-emerald-600 dark:text-emerald-400', bg: 'bg-emerald-500/10' },
] as const;

function formatPrice(price: string | number) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(parseFloat(String(price)));
}

function getStatusBadge(status: string) {
    switch (status) {
        case 'pending':
            return { label: 'Baru Masuk', classes: 'bg-blue-500/15 text-blue-600 border-blue-500/30 dark:text-blue-400' };
        case 'follow_up':
            return { label: 'Follow Up', classes: 'bg-amber-500/15 text-amber-600 border-amber-500/30 dark:text-amber-400' };
        case 'approved':
            return { label: 'Setuju Sewa', classes: 'bg-emerald-500/15 text-emerald-600 border-emerald-500/30 dark:text-emerald-400' };
        case 'cancelled':
            return { label: 'Batal', classes: 'bg-red-500/15 text-red-600 border-red-500/30 dark:text-red-400' };
        default:
            return { label: status, classes: '' };
    }
}

function formatDate(dateStr: string) {
    return new Date(dateStr).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
}

// Pop-up notification
const unreadNotifications = computed(() => {
    return (page.props.unread_notifications as any[]) || [];
});

const showNotificationPopup = ref(false);

watch(unreadNotifications, (newVal) => {
    if (newVal && newVal.length > 0) {
        showNotificationPopup.value = true;
    }
}, { immediate: true });

function dismissNotifications() {
    showNotificationPopup.value = false;
    router.post('/vbpanel/bookings/mark-read', {}, { preserveScroll: true });
}
</script>

<template>
    <Head title="Admin Dashboard" />
    <FlashMessage />

    <div class="flex flex-col gap-6 p-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-foreground">Dashboard</h1>
            <p class="text-sm text-muted-foreground">Overview armada & pesanan VINS BALI.</p>
        </div>

        <!-- Notification Pop-up -->
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0 translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 translate-y-2"
        >
            <div
                v-if="showNotificationPopup && unreadNotifications.length > 0"
                class="rounded-xl border border-blue-500/30 bg-blue-50/80 dark:bg-blue-950/50 p-4 shadow-lg"
            >
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div class="flex items-center gap-2">
                        <div class="flex size-8 items-center justify-center rounded-lg bg-blue-500/15">
                            <Bell class="size-4 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-blue-900 dark:text-blue-200">
                                {{ unreadNotifications.length }} Pesanan Baru!
                            </p>
                            <p class="text-xs text-blue-700/70 dark:text-blue-400/70">Pesanan yang belum ditindaklanjuti</p>
                        </div>
                    </div>
                    <button @click="dismissNotifications" class="text-blue-400 hover:text-blue-600 transition-colors">
                        <X class="size-4" />
                    </button>
                </div>
                <div class="space-y-2 max-h-48 overflow-y-auto">
                    <div
                        v-for="notif in unreadNotifications"
                        :key="notif.id"
                        class="flex items-center justify-between gap-3 rounded-lg bg-white/60 dark:bg-white/5 p-2.5 border border-blue-200/50 dark:border-blue-800/30"
                    >
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-foreground truncate">{{ notif.data.message }}</p>
                            <p class="text-[10px] text-muted-foreground">{{ notif.created_at }}</p>
                        </div>
                    </div>
                </div>
                <Link href="/vbpanel/bookings">
                    <Button size="sm" class="w-full mt-3 gap-1.5 bg-blue-600 text-white hover:bg-blue-700 h-8 text-xs">
                        Lihat Semua Pesanan
                    </Button>
                </Link>
            </div>
        </Transition>

        <!-- Car Stats Grid -->
        <div>
            <h2 class="text-sm font-semibold text-muted-foreground uppercase tracking-wider mb-3">Armada</h2>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <Card v-for="stat in carStatCards" :key="stat.key">
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-sm font-medium text-muted-foreground">{{ stat.label }}</CardTitle>
                        <div :class="[stat.bg, stat.color, 'flex size-9 items-center justify-center rounded-lg']">
                            <component :is="stat.icon" class="size-4" />
                        </div>
                    </CardHeader>
                    <CardContent>
                        <p class="text-3xl font-bold text-foreground">{{ stats[stat.key] }}</p>
                    </CardContent>
                </Card>
            </div>
        </div>

        <!-- Booking Stats Grid -->
        <div>
            <h2 class="text-sm font-semibold text-muted-foreground uppercase tracking-wider mb-3">Pesanan</h2>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <Card v-for="stat in bookingStatCards" :key="stat.key">
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-sm font-medium text-muted-foreground">{{ stat.label }}</CardTitle>
                        <div :class="[stat.bg, stat.color, 'flex size-9 items-center justify-center rounded-lg']">
                            <component :is="stat.icon" class="size-4" />
                        </div>
                    </CardHeader>
                    <CardContent>
                        <p class="text-3xl font-bold text-foreground">{{ stats[stat.key] }}</p>
                    </CardContent>
                </Card>
            </div>
        </div>

        <!-- Recent Bookings -->
        <Card>
            <CardHeader class="flex flex-row items-center justify-between">
                <CardTitle class="text-base">Pesanan Terbaru</CardTitle>
                <Link href="/vbpanel/bookings">
                    <Button variant="outline" size="sm" class="gap-1.5 text-xs h-7">
                        Lihat Semua
                    </Button>
                </Link>
            </CardHeader>
            <CardContent>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-border text-left">
                                <th class="pb-3 font-medium text-muted-foreground">Pelanggan</th>
                                <th class="pb-3 font-medium text-muted-foreground">Mobil</th>
                                <th class="pb-3 font-medium text-muted-foreground">Tgl. Sewa</th>
                                <th class="pb-3 font-medium text-muted-foreground">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="booking in recentBookings"
                                :key="booking.id"
                                class="border-b border-border/50 last:border-0"
                            >
                                <td class="py-3">
                                    <p class="font-medium text-foreground">{{ booking.customer_name }}</p>
                                    <p class="text-xs text-muted-foreground">{{ booking.customer_phone }}</p>
                                </td>
                                <td class="py-3 text-muted-foreground">{{ booking.car?.name ?? '-' }}</td>
                                <td class="py-3 text-muted-foreground text-xs">{{ booking.rental_date || '-' }}</td>
                                <td class="py-3">
                                    <Badge
                                        variant="outline"
                                        class="text-[10px] font-semibold"
                                        :class="getStatusBadge(booking.status).classes"
                                    >
                                        {{ getStatusBadge(booking.status).label }}
                                    </Badge>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="!recentBookings.length" class="py-8 text-center text-sm text-muted-foreground">
                    Belum ada pesanan.
                </div>
            </CardContent>
        </Card>

        <!-- Recent Cars -->
        <Card>
            <CardHeader>
                <CardTitle class="text-base">Mobil Terbaru</CardTitle>
            </CardHeader>
            <CardContent>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-border text-left">
                                <th class="pb-3 font-medium text-muted-foreground">Nama</th>
                                <th class="pb-3 font-medium text-muted-foreground">Merk</th>
                                <th class="pb-3 font-medium text-muted-foreground">Harga/Hari</th>
                                <th class="pb-3 font-medium text-muted-foreground">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="car in recentCars"
                                :key="car.id"
                                class="border-b border-border/50 last:border-0"
                            >
                                <td class="py-3 font-medium text-foreground">{{ car.name }}</td>
                                <td class="py-3 text-muted-foreground">{{ car.brand }}</td>
                                <td class="py-3 text-muted-foreground">{{ formatPrice(car.price_per_day) }}</td>
                                <td class="py-3">
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium"
                                        :class="[
                                            car.is_available
                                                ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400'
                                                : 'bg-red-500/10 text-red-700 dark:text-red-400',
                                        ]"
                                    >
                                        <Eye v-if="car.is_available" class="size-3" />
                                        <EyeOff v-else class="size-3" />
                                        {{ car.is_available ? 'Tersedia' : 'Tidak Tersedia' }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="!recentCars.length" class="py-8 text-center text-sm text-muted-foreground">
                    Belum ada data mobil.
                </div>
            </CardContent>
        </Card>
    </div>
</template>
