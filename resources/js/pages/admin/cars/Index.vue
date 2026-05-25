<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Edit, Eye, EyeOff, Plus, Search, Trash2, X, Tag } from 'lucide-vue-next';
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
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
    DialogTrigger,
} from '@/components/ui/dialog';
import type { Car } from '@/types';

interface PaginatedCars {
    data: Car[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: Array<{ url: string | null; label: string; active: boolean }>;
}

const props = defineProps<{
    cars: PaginatedCars;
    categories?: { id: number; name: string; cars_count: number }[];
    filters: {
        search?: string;
        is_available?: string;
    };
}>();

const activeTab = ref<'cars' | 'categories'>('cars');

// --- Cars State ---
const search = ref(props.filters.search ?? '');
const isAvailable = ref(props.filters.is_available ?? '');

let searchTimeout: ReturnType<typeof setTimeout>;

function applyFilters() {
    const params: Record<string, string> = {};
    if (search.value) params.search = search.value;
    if (isAvailable.value !== '') params.is_available = isAvailable.value;

    router.get('/vbpanel/cars', params, {
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

function toggleAvailability(car: Car) {
    router.patch(`/vbpanel/cars/${car.id}/toggle-availability`, {}, {
        preserveScroll: true,
    });
}

function deleteCar(car: Car) {
    router.delete(`/vbpanel/cars/${car.id}`, {
        preserveScroll: true,
    });
}

function formatPrice(price: string | number) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(parseFloat(String(price)));
}

function getPrimaryImage(car: Car) {
    const primary = car.images?.find((img) => img.is_primary);
    const first = car.images?.[0];
    const img = primary ?? first;
    return img ? `/storage/${img.image_path}` : null;
}

// --- Categories State ---
const showCategoryDialog = ref(false);
const editingCategory = ref<{ id: number; name: string } | null>(null);

const categoryForm = useForm({
    name: '',
});

function openCreateCategory() {
    editingCategory.value = null;
    categoryForm.reset();
    categoryForm.clearErrors();
    showCategoryDialog.value = true;
}

function openEditCategory(category: { id: number; name: string }) {
    editingCategory.value = { ...category };
    categoryForm.name = category.name;
    categoryForm.clearErrors();
    showCategoryDialog.value = true;
}

function submitCategory() {
    if (editingCategory.value) {
        categoryForm.put(`/vbpanel/car-categories/${editingCategory.value.id}`, {
            onSuccess: () => {
                showCategoryDialog.value = false;
            },
        });
    } else {
        categoryForm.post('/vbpanel/car-categories', {
            onSuccess: () => {
                showCategoryDialog.value = false;
            },
        });
    }
}

function deleteCategory(category: { id: number }) {
    router.delete(`/vbpanel/car-categories/${category.id}`, {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Manajemen Mobil" />
    <FlashMessage />

    <div class="flex flex-col gap-6 p-4">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-foreground">Manajemen Mobil</h1>
                <p class="text-sm text-muted-foreground">{{ cars.total }} unit total</p>
            </div>
            <Link href="/vbpanel/cars/create">
                <Button class="gap-2">
                    <Plus class="size-4" />
                    Tambah Mobil
                </Button>
            </Link>
        </div>

        <!-- Tabs -->
        <div class="flex gap-2 border-b border-border pb-px overflow-x-auto">
            <button
                @click="activeTab = 'cars'"
                class="px-4 py-2 text-sm font-medium transition-colors border-b-2 whitespace-nowrap"
                :class="activeTab === 'cars' ? 'border-primary text-primary' : 'border-transparent text-muted-foreground hover:text-foreground hover:border-muted'"
            >
                Daftar Mobil
            </button>
            <button
                @click="activeTab = 'categories'"
                class="px-4 py-2 text-sm font-medium transition-colors border-b-2 whitespace-nowrap"
                :class="activeTab === 'categories' ? 'border-primary text-primary' : 'border-transparent text-muted-foreground hover:text-foreground hover:border-muted'"
            >
                Kategori Mobil
            </button>
        </div>

        <div v-if="activeTab === 'cars'" class="flex flex-col gap-6">
            <!-- Search & Filter -->
        <Card>
            <CardContent class="pt-6">
                <div class="flex flex-col gap-3 sm:flex-row">
                    <div class="relative flex-1">
                        <Search class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Cari nama atau merk..."
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
                    <select
                        v-model="isAvailable"
                        class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 sm:w-44"
                        @change="applyFilters"
                    >
                        <option value="">Semua Status</option>
                        <option value="1">Tersedia</option>
                        <option value="0">Tidak Tersedia</option>
                    </select>
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
                                <th class="px-4 py-3 text-left font-medium text-muted-foreground">Mobil</th>
                                <th class="px-4 py-3 text-left font-medium text-muted-foreground">Merk</th>
                                <th class="px-4 py-3 text-left font-medium text-muted-foreground">Harga/Hari</th>
                                <th class="px-4 py-3 text-left font-medium text-muted-foreground">Status</th>
                                <th class="px-4 py-3 text-right font-medium text-muted-foreground">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="car in cars.data"
                                :key="car.id"
                                class="border-b border-border/50 transition-colors last:border-0 hover:bg-muted/20"
                            >
                                <!-- Car info -->
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="size-10 shrink-0 overflow-hidden rounded-lg border border-border bg-muted">
                                            <img
                                                v-if="getPrimaryImage(car)"
                                                :src="getPrimaryImage(car)!"
                                                :alt="car.name"
                                                class="h-full w-full object-cover"
                                            />
                                        </div>
                                        <div>
                                            <p class="font-medium text-foreground">{{ car.name }}</p>
                                            <p class="text-xs text-muted-foreground">{{ car.color }} · {{ car.year }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-muted-foreground">{{ car.brand }}</td>
                                <td class="px-4 py-3 font-medium text-foreground">{{ formatPrice(car.price_per_day) }}</td>

                                <!-- Status -->
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1.5">
                                        <Badge
                                            :variant="car.is_available ? 'default' : 'destructive'"
                                            class="text-[10px]"
                                        >
                                            {{ car.is_available ? 'Tersedia' : 'Tidak Tersedia' }}
                                        </Badge>
                                        <Badge v-if="car.is_featured" variant="secondary" class="text-[10px]">
                                            Unggulan
                                        </Badge>
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <Link :href="`/vbpanel/cars/${car.id}/edit`">
                                            <Button variant="ghost" size="icon-sm">
                                                <Edit class="size-4" />
                                            </Button>
                                        </Link>

                                        <Button
                                            variant="ghost"
                                            size="icon-sm"
                                            @click="toggleAvailability(car)"
                                            :title="car.is_available ? 'Sembunyikan' : 'Tampilkan'"
                                        >
                                            <EyeOff v-if="car.is_available" class="size-4" />
                                            <Eye v-else class="size-4" />
                                        </Button>

                                        <!-- Delete Dialog -->
                                        <Dialog>
                                            <DialogTrigger as-child>
                                                <Button variant="ghost" size="icon-sm" class="text-destructive hover:text-destructive">
                                                    <Trash2 class="size-4" />
                                                </Button>
                                            </DialogTrigger>
                                            <DialogContent>
                                                <DialogHeader>
                                                    <DialogTitle>Hapus Mobil</DialogTitle>
                                                    <DialogDescription>
                                                        Apakah Anda yakin ingin menghapus <strong>{{ car.name }}</strong>?
                                                        Semua data dan foto akan dihapus secara permanen.
                                                    </DialogDescription>
                                                </DialogHeader>
                                                <DialogFooter>
                                                    <DialogClose as-child>
                                                        <Button variant="outline">Batal</Button>
                                                    </DialogClose>
                                                    <Button variant="destructive" @click="deleteCar(car)">
                                                        Hapus
                                                    </Button>
                                                </DialogFooter>
                                            </DialogContent>
                                        </Dialog>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Empty state -->
                <div v-if="!cars.data.length" class="py-12 text-center">
                    <p class="text-sm text-muted-foreground">Belum ada data mobil.</p>
                    <Link href="/vbpanel/cars/create" class="mt-2 inline-block">
                        <Button variant="outline" size="sm" class="gap-2">
                            <Plus class="size-4" />
                            Tambah Mobil Pertama
                        </Button>
                    </Link>
                </div>
            </CardContent>
        </Card>

        <!-- Pagination -->
        <div v-if="cars.last_page > 1" class="flex items-center justify-center gap-1">
            <template v-for="link in cars.links" :key="link.label">
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

        <div v-else-if="activeTab === 'categories'" class="flex flex-col gap-6">
            <div class="flex sm:justify-end">
                <Button @click="openCreateCategory" size="sm" class="gap-2">
                    <Plus class="size-4" />
                    Tambah Kategori
                </Button>
            </div>

            <Card>
                <CardContent class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-border bg-muted/30">
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground w-16">No</th>
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">Kategori</th>
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">Jumlah Mobil</th>
                                    <th class="px-4 py-3 text-right font-medium text-muted-foreground">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(category, index) in categories"
                                    :key="category.id"
                                    class="border-b border-border/50 transition-colors last:border-0 hover:bg-muted/20"
                                >
                                    <td class="px-4 py-3 text-muted-foreground">{{ index + 1 }}</td>
                                    <td class="px-4 py-3 font-medium">{{ category.name }}</td>
                                    <td class="px-4 py-3 text-muted-foreground">
                                        <Badge variant="outline">{{ category.cars_count ?? 0 }} Unit</Badge>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-end gap-1">
                                            <Button variant="ghost" size="icon-sm" @click="openEditCategory(category)">
                                                <Edit class="size-4" />
                                            </Button>

                                            <!-- Delete Dialog -->
                                            <Dialog>
                                                <DialogTrigger as-child>
                                                    <Button variant="ghost" size="icon-sm" class="text-destructive hover:text-destructive">
                                                        <Trash2 class="size-4" />
                                                    </Button>
                                                </DialogTrigger>
                                                <DialogContent>
                                                    <DialogHeader>
                                                        <DialogTitle>Hapus Kategori</DialogTitle>
                                                        <DialogDescription>
                                                            Apakah Anda yakin ingin menghapus kategori <strong>{{ category.name }}</strong>?
                                                            Jika kategori ini memiliki mobil, hapus atau ubah kategori mobil tersebut terlebih dahulu.
                                                        </DialogDescription>
                                                    </DialogHeader>
                                                    <DialogFooter>
                                                        <DialogClose as-child>
                                                            <Button variant="outline">Batal</Button>
                                                        </DialogClose>
                                                        <Button variant="destructive" @click="deleteCategory(category)" :disabled="category.cars_count > 0">
                                                            Hapus
                                                        </Button>
                                                    </DialogFooter>
                                                </DialogContent>
                                            </Dialog>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="!categories?.length" class="py-12 text-center">
                        <p class="text-sm text-muted-foreground">Belum ada kategori.</p>
                        <Button variant="outline" size="sm" class="gap-2 mt-2" @click="openCreateCategory">
                            <Plus class="size-4" />
                            Buat Kategori
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <!-- Category Form Dialog -->
            <Dialog v-model:open="showCategoryDialog">
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>{{ editingCategory ? 'Edit Kategori' : 'Tambah Kategori' }}</DialogTitle>
                        <DialogDescription>
                            Masukkan nama kategori mobil yang ingin ditambahkan. Slug otomatis dibuat.
                        </DialogDescription>
                    </DialogHeader>
                    
                    <form @submit.prevent="submitCategory" class="space-y-4 py-4">
                        <div class="space-y-1.5">
                            <label class="text-sm font-medium">Nama Kategori</label>
                            <input
                                v-model="categoryForm.name"
                                type="text"
                                class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                                placeholder="..."
                                required
                            />
                            <p v-if="categoryForm.errors.name" class="mt-1 text-xs text-destructive">{{ categoryForm.errors.name }}</p>
                        </div>
                    </form>

                    <DialogFooter>
                        <DialogClose as-child>
                            <Button variant="outline">Batal</Button>
                        </DialogClose>
                        <Button type="button" @click="submitCategory" :disabled="categoryForm.processing">
                            Simpan
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>
    </div>
</template>
