<script setup lang="ts">
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { ArrowLeft, Eye, EyeOff, ImagePlus, Star, Trash2, Upload, X, ChevronDown, Search, Check } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import FlashMessage from '@/components/FlashMessage.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { Car } from '@/types';

const props = defineProps<{
    car: (Car & { services?: any[] }) | null;
    isEditing: boolean;
    categories?: { id: number; name: string }[];
    services?: { id: number; name: string; duration_days: number; is_active: boolean }[];
}>();

const page = usePage();

const brandOptions = computed(() => {
    return (page.props.global_settings as any)?.home_brand_logos || [];
});

const isBrandDropdownOpen = ref(false);
const brandSearch = ref('');
const filteredBrands = computed(() => {
    const q = brandSearch.value.toLowerCase();
    return brandOptions.value.filter((b: any) => 
        b.label.toLowerCase().includes(q) || b.slug.includes(q)
    );
});

function selectBrand(brandLabel: string) {
    form.brand = brandLabel;
    isBrandDropdownOpen.value = false;
    brandSearch.value = '';
}

const form = useForm({
    name: props.car?.name ?? '',
    brand: props.car?.brand ?? '',
    slug: props.car?.slug ?? '',
    short_description: props.car?.short_description ?? '',
    description: props.car?.description ?? '',
    price_per_day: props.car?.price_per_day ?? '',
    transmission: props.car?.transmission ?? 'Automatic',
    year: props.car?.year ?? new Date().getFullYear(),
    seats: props.car?.seats ?? 4,
    fuel_type: props.car?.fuel_type ?? 'Bensin',
    color: props.car?.color ?? '',
    cars_category_id: props.car?.cars_category_id ?? null,
    horsepower: props.car?.horsepower ?? '',
    engine_capacity: props.car?.engine_capacity ?? '',
    acceleration_0_100: props.car?.acceleration_0_100 ?? '',
    services: (props.services ?? []).map(service => {
        const existing = props.car?.services?.find((s: any) => s.id === service.id);
        return {
            id: service.id,
            name: service.name,
            duration_days: service.duration_days,
            enabled: !!existing,
            discount_percentage: existing ? existing.pivot.discount_percentage : 5,
        };
    }),
    is_available: props.car?.is_available ?? true,
    is_featured: props.car?.is_featured ?? false,
    sort_order: props.car?.sort_order ?? 0,
    images: [] as File[],
});

// Preview for new uploads
const previews = ref<string[]>([]);

function handleFileSelect(e: Event) {
    const input = e.target as HTMLInputElement;
    if (!input.files) return;

    const files = Array.from(input.files);
    for (const file of files) {
        form.images.push(file);
        const reader = new FileReader();
        reader.onload = (ev) => {
            previews.value.push(ev.target?.result as string);
        };
        reader.readAsDataURL(file);
    }

    // Reset input so same file can be re-selected
    input.value = '';
}

function removePreview(index: number) {
    form.images.splice(index, 1);
    previews.value.splice(index, 1);
}

// Handle drag & drop
const isDragging = ref(false);

function handleDrop(e: DragEvent) {
    isDragging.value = false;
    const files = e.dataTransfer?.files;
    if (!files) return;

    for (const file of Array.from(files)) {
        if (file.type.startsWith('image/')) {
            form.images.push(file);
            const reader = new FileReader();
            reader.onload = (ev) => {
                previews.value.push(ev.target?.result as string);
            };
            reader.readAsDataURL(file);
        }
    }
}

function submit() {
    const data = form.data();
    // Filter out unchecked services
    const filteredServices = data.services
        .filter((s: any) => s.enabled)
        .map((s: any) => ({
            id: s.id,
            discount_percentage: s.discount_percentage,
        }));

    if (props.isEditing && props.car) {
        // For PUT with files, use POST with _method override
        router.post(`/vbpanel/cars/${props.car.id}`, {
            _method: 'put',
            ...data,
            services: filteredServices,
        }, {
            forceFormData: true,
            preserveScroll: true,
            onError: (err) => form.setError(err as any),
        });
    } else {
        const transformedForm = form.transform((data) => ({
            ...data,
            services: filteredServices,
        }));
        
        transformedForm.post('/vbpanel/cars', {
            forceFormData: true,
            preserveScroll: true,
        });
    }
}

// Delete existing image
function deleteImage(imageId: number) {
    router.delete(`/vbpanel/car-images/${imageId}`, {
        preserveScroll: true,
    });
}

// Set primary image
function setPrimary(imageId: number) {
    router.patch(`/vbpanel/car-images/${imageId}/set-primary`, {}, {
        preserveScroll: true,
    });
}

const pageTitle = computed(() => props.isEditing ? `Edit: ${props.car?.name}` : 'Tambah Mobil Baru');
</script>

<template>
    <Head :title="pageTitle" />
    <FlashMessage />

    <div class="flex flex-col gap-6 p-4">
        <!-- Header -->
        <div>
            <Link href="/vbpanel/cars" class="mb-3 inline-flex items-center gap-1.5 text-sm text-muted-foreground transition-colors hover:text-foreground">
                <ArrowLeft class="size-4" />
                Kembali
            </Link>
            <h1 class="text-2xl font-bold tracking-tight text-foreground">{{ pageTitle }}</h1>
        </div>

        <form @submit.prevent="submit" class="grid gap-6 lg:grid-cols-3">
            <!-- Left Column: Main Info -->
            <div class="space-y-6 lg:col-span-2">
                <!-- Basic Info -->
                <Card>
                    <CardHeader>
                        <CardTitle class="text-base">Informasi Dasar</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <!-- Name -->
                            <div>
                                <label class="mb-1.5 block text-sm font-medium">Nama Mobil *</label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    placeholder="e.g. BMW Z4"
                                    class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                                    :class="{ 'border-destructive': form.errors.name }"
                                />
                                <p v-if="form.errors.name" class="mt-1 text-xs text-destructive">{{ form.errors.name }}</p>
                            </div>

                            <!-- Brand -->
                            <div class="relative">
                                <label class="mb-1.5 block text-sm font-medium">Merk *</label>
                                <div 
                                    class="flex h-9 w-full items-center justify-between rounded-md border border-input bg-background px-3 text-sm cursor-pointer transition focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20"
                                    :class="{ 'border-destructive': form.errors.brand }"
                                    @click="isBrandDropdownOpen = !isBrandDropdownOpen"
                                >
                                    <span v-if="form.brand">{{ form.brand }}</span>
                                    <span v-else class="text-muted-foreground">Pilih Merk...</span>
                                    <ChevronDown class="size-4 opacity-50" />
                                </div>
                                <p v-if="form.errors.brand" class="mt-1 text-xs text-destructive">{{ form.errors.brand }}</p>

                                <!-- Dropdown Overlay -->
                                <div v-if="isBrandDropdownOpen" class="fixed inset-0 z-40" @click="isBrandDropdownOpen = false"></div>
                                
                                <!-- Dropdown Content -->
                                <div v-if="isBrandDropdownOpen" class="absolute left-0 top-[60px] z-50 w-full rounded-md border bg-popover text-popover-foreground shadow-md outline-none">
                                    <div class="flex items-center border-b px-3">
                                        <Search class="mr-2 size-4 shrink-0 opacity-50" />
                                        <input 
                                            v-model="brandSearch" 
                                            class="flex h-10 w-full rounded-md bg-transparent py-3 text-sm outline-none placeholder:text-muted-foreground" 
                                            placeholder="Cari merk..." 
                                            autofocus
                                        />
                                    </div>
                                    <div class="max-h-[200px] overflow-y-auto p-1">
                                        <div 
                                            v-for="brand in filteredBrands" 
                                            :key="brand.slug"
                                            class="relative flex cursor-pointer select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none hover:bg-accent hover:text-accent-foreground"
                                            @click="selectBrand(brand.label)"
                                        >
                                            <img :src="`https://cdn.jsdelivr.net/gh/filippofilip95/car-logos-dataset@master/logos/optimized/${brand.slug}.png`" class="mr-2 h-4 w-auto object-contain" />
                                            {{ brand.label }}
                                            <Check v-if="form.brand === brand.label" class="ml-auto size-4" />
                                        </div>
                                        <div v-if="filteredBrands.length === 0" class="py-6 text-center text-sm text-muted-foreground">
                                            Merk tidak ditemukan di pengaturan.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Slug -->
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">
                                Slug
                                <span class="text-xs text-muted-foreground">(otomatis dari nama, bisa diedit)</span>
                            </label>
                            <input
                                v-model="form.slug"
                                type="text"
                                placeholder="auto-generated-from-name"
                                class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm font-mono outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                                :class="{ 'border-destructive': form.errors.slug }"
                            />
                            <p v-if="form.errors.slug" class="mt-1 text-xs text-destructive">{{ form.errors.slug }}</p>
                        </div>

                        <!-- Short Description -->
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">
                                Deskripsi Singkat
                                <span class="text-xs text-muted-foreground">(untuk card)</span>
                            </label>
                            <textarea
                                v-model="form.short_description"
                                rows="2"
                                placeholder="Deskripsi singkat yang muncul di card katalog..."
                                class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                            ></textarea>
                        </div>

                        <!-- Full Description -->
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Deskripsi Lengkap</label>
                            <textarea
                                v-model="form.description"
                                rows="4"
                                placeholder="Deskripsi detail untuk halaman detail mobil..."
                                class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                            ></textarea>
                        </div>
                    </CardContent>
                </Card>

                <!-- Specifications -->
                <Card>
                    <CardHeader>
                        <CardTitle class="text-base">Spesifikasi</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium">Harga Sewa/Hari (IDR) *</label>
                                <input
                                    v-model="form.price_per_day"
                                    type="number"
                                    placeholder="3500000"
                                    class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                                    :class="{ 'border-destructive': form.errors.price_per_day }"
                                />
                                <p v-if="form.errors.price_per_day" class="mt-1 text-xs text-destructive">{{ form.errors.price_per_day }}</p>
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium">Transmisi *</label>
                                <select
                                    v-model="form.transmission"
                                    class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"
                                >
                                    <option value="Automatic">Automatic</option>
                                    <option value="Manual">Manual</option>
                                </select>
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium">Tahun *</label>
                                <input
                                    v-model.number="form.year"
                                    type="number"
                                    min="1900"
                                    :max="new Date().getFullYear() + 2"
                                    class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                                    :class="{ 'border-destructive': form.errors.year }"
                                />
                                <p v-if="form.errors.year" class="mt-1 text-xs text-destructive">{{ form.errors.year }}</p>
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium">Jumlah Kursi *</label>
                                <input
                                    v-model.number="form.seats"
                                    type="number"
                                    min="1"
                                    max="20"
                                    class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                                    :class="{ 'border-destructive': form.errors.seats }"
                                />
                                <p v-if="form.errors.seats" class="mt-1 text-xs text-destructive">{{ form.errors.seats }}</p>
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium">Bahan Bakar *</label>
                                <select
                                    v-model="form.fuel_type"
                                    class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"
                                >
                                    <option value="Bensin">Bensin</option>
                                    <option value="Diesel">Diesel</option>
                                    <option value="Hybrid">Hybrid</option>
                                    <option value="Electric">Electric</option>
                                </select>
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium">Warna *</label>
                                <input
                                    v-model="form.color"
                                    type="text"
                                    placeholder="e.g. Silver"
                                    class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                                    :class="{ 'border-destructive': form.errors.color }"
                                />
                                <p v-if="form.errors.color" class="mt-1 text-xs text-destructive">{{ form.errors.color }}</p>
                            </div>

                            <!-- NEW TRINITY FIELDS -->
                            <div>
                                <label class="mb-1.5 block text-sm font-medium">Kategori Kendaran</label>
                                <select
                                    v-model="form.cars_category_id"
                                    class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"
                                >
                                    <option :value="null">Tanpa Kategori</option>
                                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                        {{ cat.name }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium">Horsepower (HP)</label>
                                <input
                                    v-model.number="form.horsepower"
                                    type="number"
                                    placeholder="e.g. 771"
                                    class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                                    :class="{ 'border-destructive': form.errors.horsepower }"
                                />
                                <p v-if="form.errors.horsepower" class="mt-1 text-xs text-destructive">{{ form.errors.horsepower }}</p>
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium">Kapasitas Mesin</label>
                                <input
                                    v-model="form.engine_capacity"
                                    type="text"
                                    placeholder="e.g. 4.0L V8"
                                    class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                                    :class="{ 'border-destructive': form.errors.engine_capacity }"
                                />
                                <p v-if="form.errors.engine_capacity" class="mt-1 text-xs text-destructive">{{ form.errors.engine_capacity }}</p>
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium">Akselerasi 0-100 km/h (detik)</label>
                                <input
                                    v-model.number="form.acceleration_0_100"
                                    type="number"
                                    step="0.1"
                                    placeholder="e.g. 3.4"
                                    class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                                    :class="{ 'border-destructive': form.errors.acceleration_0_100 }"
                                />
                                <p v-if="form.errors.acceleration_0_100" class="mt-1 text-xs text-destructive">{{ form.errors.acceleration_0_100 }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Diskon & Pricing Strategi -->
                <Card>
                    <CardHeader>
                        <CardTitle class="text-base">Pengaturan Diskon Paket Durasi</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-xs text-muted-foreground mb-4">
                            Aktifkan checkbox pada layanan master di bawah untuk menerapkan diskon pada mobil ini.
                        </p>
                        <div class="grid gap-3">
                            <div v-for="(svc, index) in form.services" :key="svc.id" class="flex items-center justify-between rounded-lg border border-border p-3 transition-colors" :class="svc.enabled ? 'bg-muted/10 border-primary/30' : 'bg-transparent'">
                                <div class="flex items-center gap-3">
                                    <input
                                        type="checkbox"
                                        v-model="form.services[index].enabled"
                                        class="size-4 rounded border-input accent-primary cursor-pointer"
                                    />
                                    <div>
                                        <div class="text-sm font-medium">{{ svc.name }}</div>
                                        <div class="text-[10px] text-muted-foreground uppercase tracking-widest mt-0.5">Min. {{ svc.duration_days }} Hari</div>
                                    </div>
                                </div>
                                
                                <div class="flex flex-col items-end gap-1">
                                    <div class="relative w-28">
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                            <span class="text-muted-foreground sm:text-sm">%</span>
                                        </div>
                                        <input
                                            v-model.number="form.services[index].discount_percentage"
                                            type="number"
                                            min="0"
                                            max="100"
                                            :disabled="!svc.enabled"
                                            class="h-9 flex w-full rounded-md border bg-background pr-8 pl-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 disabled:cursor-not-allowed disabled:opacity-50"
                                            :class="form.errors[`services.${index}.discount_percentage` as keyof typeof form.errors] ? 'border-destructive focus-visible:ring-destructive' : 'border-input focus-visible:ring-primary/50'"
                                        />
                                    </div>
                                    <p v-if="form.errors[`services.${index}.discount_percentage` as keyof typeof form.errors]" class="text-[10px] text-destructive">{{ form.errors[`services.${index}.discount_percentage` as keyof typeof form.errors] }}</p>
                                </div>
                            </div>
                            
                            <div v-if="form.services.length === 0" class="text-xs text-muted-foreground italic border border-dashed rounded-lg p-3 text-center">
                                Data master paket durasi kosong. Silakan tambahkan di menu Layanan terlebih dahulu.
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Images -->
                <Card>
                    <CardHeader>
                        <CardTitle class="text-base">Foto Mobil</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <!-- Existing images (edit mode) -->
                        <div v-if="isEditing && car?.images?.length" class="space-y-2">
                            <p class="text-sm font-medium text-muted-foreground">Foto saat ini</p>
                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                                <div
                                    v-for="img in car!.images"
                                    :key="img.id"
                                    class="group relative overflow-hidden rounded-lg border-2"
                                    :class="[img.is_primary ? 'border-primary' : 'border-border']"
                                >
                                    <img
                                        :src="`/storage/${img.image_path}`"
                                        :alt="`${car!.name} image`"
                                        class="aspect-video w-full object-cover"
                                    />
                                    <div class="absolute inset-0 flex items-center justify-center gap-1 bg-black/40 opacity-0 transition group-hover:opacity-100">
                                        <Button
                                            v-if="!img.is_primary"
                                            variant="secondary"
                                            size="icon-sm"
                                            title="Set sebagai foto utama"
                                            @click.prevent="setPrimary(img.id)"
                                        >
                                            <Star class="size-3.5" />
                                        </Button>
                                        <Button
                                            variant="destructive"
                                            size="icon-sm"
                                            title="Hapus foto"
                                            @click.prevent="deleteImage(img.id)"
                                        >
                                            <Trash2 class="size-3.5" />
                                        </Button>
                                    </div>
                                    <span
                                        v-if="img.is_primary"
                                        class="absolute top-1.5 left-1.5 flex items-center gap-1 rounded bg-primary px-1.5 py-0.5 text-[10px] font-medium text-primary-foreground"
                                    >
                                        <Star class="size-3" />
                                        Utama
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Upload new images -->
                        <div>
                            <p class="mb-2 text-sm font-medium text-muted-foreground">Upload foto baru</p>
                            <div
                                class="rounded-lg border-2 border-dashed p-6 text-center transition-colors"
                                :class="[isDragging ? 'border-primary bg-primary/5' : 'border-border']"
                                @dragover.prevent="isDragging = true"
                                @dragleave.prevent="isDragging = false"
                                @drop.prevent="handleDrop"
                            >
                                <ImagePlus class="mx-auto size-8 text-muted-foreground/40" />
                                <p class="mt-2 text-sm text-muted-foreground">
                                    Drag & drop foto di sini, atau
                                </p>
                                <label class="mt-2 inline-block cursor-pointer">
                                    <Button variant="outline" size="sm" as-child class="gap-2">
                                        <span>
                                            <Upload class="size-4" />
                                            Pilih File
                                        </span>
                                    </Button>
                                    <input
                                        type="file"
                                        accept="image/jpeg,image/png,image/webp"
                                        multiple
                                        class="hidden"
                                        @change="handleFileSelect"
                                    />
                                </label>
                                <p class="mt-1.5 text-xs text-muted-foreground">JPG, PNG, WebP. Gambar akan otomatis dikompresi oleh sistem.</p>
                            </div>
                            <p v-if="form.errors.images" class="mt-1 text-xs text-destructive">{{ form.errors.images }}</p>
                        </div>

                        <!-- New upload previews -->
                        <div v-if="previews.length" class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                            <div
                                v-for="(preview, index) in previews"
                                :key="index"
                                class="group relative overflow-hidden rounded-lg border border-border"
                            >
                                <img :src="preview" class="aspect-video w-full object-cover" />
                                <button
                                    type="button"
                                    class="absolute top-1.5 right-1.5 rounded-full bg-destructive p-1 text-white opacity-0 transition group-hover:opacity-100"
                                    @click="removePreview(index)"
                                >
                                    <X class="size-3" />
                                </button>
                                <span class="absolute bottom-1.5 left-1.5 rounded bg-black/60 px-1.5 py-0.5 text-[10px] text-white">
                                    Baru
                                </span>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Right Column: Settings & Submit -->
            <div class="space-y-6">
                <!-- Settings -->
                <Card>
                    <CardHeader>
                        <CardTitle class="text-base">Pengaturan</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <!-- Availability -->
                        <label class="flex items-center justify-between gap-3 rounded-lg border border-border p-3 cursor-pointer transition hover:bg-muted/30">
                            <div class="flex items-center gap-2.5">
                                <Eye class="size-4 text-muted-foreground" />
                                <div>
                                    <p class="text-sm font-medium">Tersedia</p>
                                    <p class="text-xs text-muted-foreground">Tampil di website pelanggan</p>
                                </div>
                            </div>
                            <input
                                v-model="form.is_available"
                                type="checkbox"
                                class="size-4 rounded border-input accent-primary"
                            />
                        </label>

                        <!-- Featured -->
                        <label class="flex items-center justify-between gap-3 rounded-lg border border-border p-3 cursor-pointer transition hover:bg-muted/30">
                            <div class="flex items-center gap-2.5">
                                <Star class="size-4 text-muted-foreground" />
                                <div>
                                    <p class="text-sm font-medium">Unggulan</p>
                                    <p class="text-xs text-muted-foreground">Tampil di homepage</p>
                                </div>
                            </div>
                            <input
                                v-model="form.is_featured"
                                type="checkbox"
                                class="size-4 rounded border-input accent-primary"
                            />
                        </label>

                    </CardContent>
                </Card>

                <!-- Submit -->
                <Card>
                    <CardContent class="pt-6 space-y-3">
                        <Button
                            type="submit"
                            class="w-full"
                            :disabled="form.processing"
                        >
                            {{ form.processing ? 'Menyimpan...' : isEditing ? 'Simpan Perubahan' : 'Tambah Mobil' }}
                        </Button>
                        <Link href="/vbpanel/cars" class="block">
                            <Button variant="outline" class="w-full" type="button">
                                Batal
                            </Button>
                        </Link>
                    </CardContent>
                </Card>
            </div>
        </form>
    </div>
</template>
