<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Search, X, Phone, Calendar, User, Car as CarIcon, ArrowRight, ClipboardList } from 'lucide-vue-next';
import { ref } from 'vue';
import FlatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import FlashMessage from '@/components/FlashMessage.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

interface Booking {
    id: number;
    car_id: number;
    customer_name: string;
    customer_phone: string;
    rental_date: string | null;
    status: 'pending' | 'follow_up' | 'approved' | 'completed' | 'cancelled';
    notes: string | null;
    created_at: string;
    updated_at: string;
    car: {
        id: number;
        name: string;
        brand: string;
        images?: { image_path: string; is_primary: boolean }[];
    };
}

interface PaginatedBookings {
    data: Booking[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: Array<{ url: string | null; label: string; active: boolean }>;
}

const props = defineProps<{
    bookings: PaginatedBookings;
    filters: {
        search?: string;
        status?: string;
    };
    statusCounts: {
        all: number;
        pending: number;
        follow_up: number;
        approved: number;
        completed: number;
        cancelled: number;
    };
}>();

const search = ref(props.filters.search ?? '');
const statusFilter = ref(props.filters.status ?? '');

let searchTimeout: ReturnType<typeof setTimeout>;

function applyFilters() {
    const params: Record<string, string> = {};
    if (search.value) params.search = search.value;
    if (statusFilter.value) params.status = statusFilter.value;

    router.get('/vbpanel/bookings', params, {
        preserveState: true,
        preserveScroll: true,
    });
}

function clearSearch() {
    search.value = '';
    applyFilters();
}

function onSearchInput() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 400);
}

function updateStatus(booking: Booking, newStatus: string, rentalDate: string | null = null, notes: string | null = null) {
    const payload: any = { status: newStatus };
    if (rentalDate) payload.rental_date = rentalDate;
    if (notes !== null) payload.notes = notes;
    
    router.patch(`/vbpanel/bookings/${booking.id}/status`, payload, {
        preserveScroll: true,
    });
}

function getStatusBadge(status: string) {
    switch (status) {
        case 'pending':
            return { label: 'Baru Masuk', classes: 'bg-blue-500/15 text-blue-600 border-blue-500/30 dark:text-blue-400' };
        case 'follow_up':
            return { label: 'Follow Up', classes: 'bg-amber-500/15 text-amber-600 border-amber-500/30 dark:text-amber-400' };
        case 'approved':
            return { label: 'Sewa Berjalan', classes: 'bg-emerald-500/15 text-emerald-600 border-emerald-500/30 dark:text-emerald-400' };
        case 'completed':
            return { label: 'Selesai', classes: 'bg-indigo-500/15 text-indigo-600 border-indigo-500/30 dark:text-indigo-400' };
        case 'cancelled':
            return { label: 'Batal', classes: 'bg-red-500/15 text-red-600 border-red-500/30 dark:text-red-400' };
        default:
            return { label: status, classes: 'bg-gray-500/15 text-gray-600 border-gray-500/30' };
    }
}

function formatDate(dateStr: string) {
    return new Date(dateStr).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

function getPrimaryImage(car: Booking['car']) {
    const primary = car.images?.find((img) => img.is_primary);
    const first = car.images?.[0];
    const img = primary ?? first;
    return img ? `/storage/${img.image_path}` : null;
}

// Confirm dialog state
const showConfirmDialog = ref(false);
const confirmAction = ref<{ booking: Booking; status: string; label: string; rental_date: string | null; notes: string | null } | null>(null);

const flatpickrConfig = {
    mode: 'range',
    minDate: 'today',
    dateFormat: 'Y-m-d',
    inline: true,
};

function openConfirm(booking: Booking, status: string, label: string) {
    confirmAction.value = { booking, status, label, rental_date: booking.rental_date, notes: booking.notes };
    showConfirmDialog.value = true;
}

function executeConfirm() {
    if (confirmAction.value) {
        updateStatus(confirmAction.value.booking, confirmAction.value.status, confirmAction.value.rental_date, confirmAction.value.notes);
        showConfirmDialog.value = false;
        confirmAction.value = null;
    }
}

const statusTabs = [
    { key: '', label: 'Semua', countKey: 'all' },
    { key: 'pending', label: 'Baru Masuk', countKey: 'pending' },
    { key: 'follow_up', label: 'Follow Up', countKey: 'follow_up' },
    { key: 'approved', label: 'Berjalan', countKey: 'approved' },
    { key: 'completed', label: 'Selesai', countKey: 'completed' },
    { key: 'cancelled', label: 'Batal', countKey: 'cancelled' },
] as const;
</script>

<template>
    <Head title="Manajemen Pesanan" />
    <FlashMessage />

    <div class="flex flex-col gap-6 p-4">
        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-foreground">Manajemen Pesanan</h1>
            <p class="text-sm text-muted-foreground">{{ bookings.total }} pesanan total</p>
        </div>

        <!-- Status Tabs -->
        <div class="flex gap-2 overflow-x-auto pb-px border-b border-border hide-scrollbar">
            <button
                v-for="tab in statusTabs"
                :key="tab.key"
                @click="statusFilter = tab.key; applyFilters()"
                class="px-4 py-2 text-sm font-medium transition-colors border-b-2 whitespace-nowrap flex items-center gap-2"
                :class="statusFilter === tab.key ? 'border-primary text-primary' : 'border-transparent text-muted-foreground hover:text-foreground hover:border-muted'"
            >
                {{ tab.label }}
                <span
                    class="inline-flex h-5 min-w-5 items-center justify-center rounded-full text-[10px] font-bold px-1.5"
                    :class="statusFilter === tab.key ? 'bg-primary text-primary-foreground' : 'bg-muted text-muted-foreground'"
                >
                    {{ statusCounts[tab.countKey] }}
                </span>
            </button>
        </div>

        <!-- Search -->
        <Card>
            <CardContent class="pt-6">
                <div class="flex flex-col gap-3 sm:flex-row">
                    <div class="relative flex-1">
                        <Search class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Cari nama atau nomor telepon pelanggan..."
                            class="h-9 w-full rounded-md border border-input bg-background pl-10 pr-8 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                            @input="onSearchInput"
                        />
                        <button
                            v-if="search"
                            class="absolute top-1/2 right-3 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                            @click="clearSearch"
                        >
                            <X class="size-4" />
                        </button>
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- Table -->
        <Card>
            <CardContent class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-border bg-muted/30">
                                <th class="px-4 py-3 text-left font-medium text-muted-foreground">Pelanggan</th>
                                <th class="px-4 py-3 text-left font-medium text-muted-foreground">Mobil</th>
                                <th class="px-4 py-3 text-left font-medium text-muted-foreground">Tgl. Sewa</th>
                                <th class="px-4 py-3 text-left font-medium text-muted-foreground">Status</th>
                                <th class="px-4 py-3 text-left font-medium text-muted-foreground">Masuk</th>
                                <th class="px-4 py-3 text-right font-medium text-muted-foreground">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="booking in bookings.data"
                                :key="booking.id"
                                class="border-b border-border/50 transition-colors last:border-0 hover:bg-muted/20"
                            >
                                <!-- Customer Info -->
                                <td class="px-4 py-3">
                                    <div class="flex flex-col gap-0.5">
                                        <div class="flex items-center gap-1.5">
                                            <User class="size-3.5 text-muted-foreground shrink-0" />
                                            <p class="font-medium text-foreground">{{ booking.customer_name }}</p>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <Phone class="size-3 text-muted-foreground shrink-0" />
                                            <a :href="`tel:${booking.customer_phone}`" class="text-xs text-muted-foreground hover:text-foreground transition-colors">
                                                {{ booking.customer_phone }}
                                            </a>
                                        </div>
                                    </div>
                                </td>

                                <!-- Car -->
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2.5">
                                        <div class="size-9 shrink-0 overflow-hidden rounded-lg border border-border bg-muted">
                                            <img
                                                v-if="getPrimaryImage(booking.car)"
                                                :src="getPrimaryImage(booking.car)!"
                                                :alt="booking.car.name"
                                                class="h-full w-full object-cover"
                                            />
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-foreground">{{ booking.car.name }}</p>
                                            <p class="text-[11px] text-muted-foreground">{{ booking.car.brand }}</p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Rental Date -->
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-1.5 text-sm text-muted-foreground">
                                        <Calendar class="size-3.5 shrink-0" />
                                        {{ booking.rental_date || '-' }}
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="px-4 py-3">
                                    <Badge
                                        variant="outline"
                                        class="text-[10px] font-semibold"
                                        :class="getStatusBadge(booking.status).classes"
                                    >
                                        {{ getStatusBadge(booking.status).label }}
                                    </Badge>
                                </td>

                                <!-- Created At -->
                                <td class="px-4 py-3 text-xs text-muted-foreground whitespace-nowrap">
                                    {{ formatDate(booking.created_at) }}
                                </td>

                                <!-- Actions -->
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                        <!-- Pending: Follow Up or Cancel -->
                                        <template v-if="booking.status === 'pending'">
                                            <Button
                                                size="sm"
                                                class="gap-1.5 h-7 text-xs bg-amber-600 text-white hover:bg-amber-700"
                                                @click="openConfirm(booking, 'follow_up', 'Follow Up')"
                                            >
                                                <ArrowRight class="size-3" />
                                                Follow Up
                                            </Button>
                                            <Button
                                                variant="destructive"
                                                size="sm"
                                                class="gap-1.5 h-7 text-xs"
                                                @click="openConfirm(booking, 'cancelled', 'Batal Sewa')"
                                            >
                                                <X class="size-3" />
                                                Batal
                                            </Button>
                                        </template>

                                        <!-- Follow Up: Approve or Cancel -->
                                        <template v-if="booking.status === 'follow_up'">
                                            <Button
                                                size="sm"
                                                class="gap-1.5 h-7 text-xs bg-emerald-600 text-white hover:bg-emerald-700"
                                                @click="openConfirm(booking, 'approved', 'Setuju Sewa')"
                                            >
                                                <ArrowRight class="size-3" />
                                                Setuju Sewa
                                            </Button>
                                            <Button
                                                variant="destructive"
                                                size="sm"
                                                class="gap-1.5 h-7 text-xs"
                                                @click="openConfirm(booking, 'cancelled', 'Batal Sewa')"
                                            >
                                                <X class="size-3" />
                                                Batal
                                            </Button>
                                        </template>

                                        <!-- Approved (Sewa Berjalan): Complete or Cancel -->
                                        <template v-if="booking.status === 'approved'">
                                            <Button
                                                size="sm"
                                                class="gap-1.5 h-7 text-xs bg-indigo-600 text-white hover:bg-indigo-700"
                                                @click="openConfirm(booking, 'completed', 'Tandai Selesai')"
                                            >
                                                <ArrowRight class="size-3" />
                                                Selesai
                                            </Button>
                                            <Button
                                                variant="destructive"
                                                size="sm"
                                                class="gap-1.5 h-7 text-xs"
                                                @click="openConfirm(booking, 'cancelled', 'Batal Sewa')"
                                            >
                                                <X class="size-3" />
                                                Batal
                                            </Button>
                                        </template>

                                        <!-- Completed / Cancelled: no action, just info -->
                                        <template v-if="booking.status === 'completed' || booking.status === 'cancelled'">
                                            <span class="text-[11px] text-muted-foreground italic">Selesai</span>
                                        </template>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Empty state -->
                <div v-if="!bookings.data.length" class="py-12 text-center">
                    <ClipboardList class="mx-auto size-10 text-muted-foreground/30" />
                    <p class="mt-3 text-sm text-muted-foreground">Belum ada pesanan{{ statusFilter ? ' dengan filter ini' : '' }}.</p>
                </div>
            </CardContent>
        </Card>

        <!-- Pagination -->
        <div v-if="bookings.last_page > 1" class="flex items-center justify-center gap-1">
            <template v-for="link in bookings.links" :key="link.label">
                <button
                    v-if="link.url"
                    class="inline-flex h-9 min-w-9 items-center justify-center rounded-md border px-3 text-sm transition-colors"
                    :class="[
                        link.active
                            ? 'border-primary bg-primary text-primary-foreground'
                            : 'border-border bg-background text-foreground hover:bg-accent',
                    ]"
                    @click="router.get(link.url!, {}, { preserveState: true, preserveScroll: true })"
                    v-html="link.label"
                />
                <span
                    v-else
                    class="inline-flex h-9 min-w-9 items-center justify-center px-3 text-sm text-muted-foreground"
                    v-html="link.label"
                />
            </template>
        </div>
    </div>

    <!-- Confirm Dialog -->
    <Dialog v-model:open="showConfirmDialog">
        <DialogContent
            @interact-outside="(e) => {
                const target = e.target as HTMLElement;
                if (target?.closest('.flatpickr-calendar')) {
                    e.preventDefault();
                }
            }"
        >
            <DialogHeader>
                <DialogTitle>Konfirmasi Aksi</DialogTitle>
                <DialogDescription>
                    Apakah Anda yakin ingin mengubah status pesanan
                    <strong>{{ confirmAction?.booking.customer_name }}</strong>
                    menjadi <strong>{{ confirmAction?.label }}</strong>?
                </DialogDescription>
            </DialogHeader>
            <div class="py-4" v-if="confirmAction?.status === 'approved'">
                <label class="mb-1.5 block text-sm font-medium">Penyesuaian Tanggal Sewa (Reschedule)</label>
                <FlatPickr
                    v-model="confirmAction.rental_date"
                    :config="flatpickrConfig"
                    placeholder="Biarkan jika tidak ada perubahan"
                    class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                />
                <p class="mt-1 text-xs text-muted-foreground">Ubah tanggal jika disepakati ada reschedule. Jika tidak, tanggal awal akan digunakan.</p>
            </div>
            <div class="py-4" v-if="confirmAction?.status === 'cancelled'">
                <label class="mb-1.5 block text-sm font-medium">Catatan Pembatalan & Penyesuaian DP</label>
                <textarea
                    v-model="confirmAction.notes"
                    rows="3"
                    placeholder="Contoh: Pelanggan membatalkan, DP hangus / refund 50%..."
                    class="w-full rounded-md border border-input bg-background p-3 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                ></textarea>
                <p class="mt-1 text-xs text-muted-foreground">Opsional. Berikan catatan tentang alasan pembatalan dan status pengembalian DP (jika ada).</p>
            </div>
            <DialogFooter>
                <DialogClose as-child>
                    <Button variant="outline">Batal</Button>
                </DialogClose>
                <Button
                    @click="executeConfirm"
                    :class="confirmAction?.status === 'cancelled' ? 'bg-red-600 hover:bg-red-700' : 'bg-primary hover:bg-primary/90'"
                    class="text-white"
                >
                    {{ confirmAction?.label }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
