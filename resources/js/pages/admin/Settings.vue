<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Facebook, Instagram, Mail, MapPin, Phone, Settings as SettingsIcon, Star, Plus, Trash2, GripVertical, CheckCircle2 } from 'lucide-vue-next';
import { ref } from 'vue';
import FlashMessage from '@/components/FlashMessage.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import IconPicker from '@/components/IconPicker.vue';
import BrandLogoPicker from '@/components/BrandLogoPicker.vue';

const props = defineProps<{
    settings: Record<string, any>;
}>();



let initialWa = props.settings.whatsapp_number ?? '';
if (initialWa.startsWith('62')) initialWa = initialWa.substring(2);
else if (initialWa.startsWith('+62')) initialWa = initialWa.substring(3);
else if (initialWa.startsWith('0')) initialWa = initialWa.substring(1);

const form = useForm({
    whatsapp_number: initialWa,
    company_email: props.settings.company_email ?? '',
    instagram_url: props.settings.instagram_url ?? '',
    facebook_url: props.settings.facebook_url ?? '',
    company_address: props.settings.company_address ?? '',
    terms_and_conditions: Array.isArray(props.settings.terms_and_conditions) ? props.settings.terms_and_conditions : [],
    home_usps: Array.isArray(props.settings.home_usps) ? props.settings.home_usps : [],
    home_services: Array.isArray(props.settings.home_services) ? props.settings.home_services : [],
    rental_requirements: props.settings.rental_requirements ?? { tourist: [], resident: [] },
    home_hero_title: props.settings.home_hero_title ?? 'Luxury Car Rental in',
    home_hero_highlight: props.settings.home_hero_highlight ?? 'Bali',
    home_hero_subtitle: props.settings.home_hero_subtitle ?? 'Pilih dari 80+ koleksi mobil premium, sports, dan eksklusif.',
    rental_requirements_footer: props.settings.rental_requirements_footer ?? 'Umur minimal 21 tahun untuk mobil standar...',
    home_brand_logos: Array.isArray(props.settings.home_brand_logos) ? props.settings.home_brand_logos : [],
    seo_settings: {
        site_name: props.settings.seo_settings?.site_name ?? 'VINS BALI',
        author: props.settings.seo_settings?.author ?? 'VINS BALI',
        robots: props.settings.seo_settings?.robots ?? 'index, follow',
        twitter_handle: props.settings.seo_settings?.twitter_handle ?? '@vinsbali',
        og_locale: props.settings.seo_settings?.og_locale ?? 'id_ID',
        default_title: props.settings.seo_settings?.default_title ?? 'VINS BALI - Premium Car Rental', 
        default_description: props.settings.seo_settings?.default_description ?? 'Premium car rental service in Bali. Experience the island with luxury and style.', 
        default_keywords: props.settings.seo_settings?.default_keywords ?? 'rental mobil bali, sewa mobil premium bali, luxury car rental bali',
        home_title: props.settings.seo_settings?.home_title ?? 'Premium Car Rental in Bali', 
        home_description: props.settings.seo_settings?.home_description ?? 'Pilih dari 80+ koleksi mobil premium, sports, dan eksklusif.', 
        home_keywords: props.settings.seo_settings?.home_keywords ?? 'rental mobil mewah bali, sewa alphard bali, rental porsche bali',
        catalog_title: props.settings.seo_settings?.catalog_title ?? 'Katalog Mobil', 
        catalog_description: props.settings.seo_settings?.catalog_description ?? 'Temukan mobil impian Anda dari koleksi premium kami di Vins Bali.',
        catalog_keywords: props.settings.seo_settings?.catalog_keywords ?? 'katalog mobil bali, harga sewa mobil bali',
        google_verification_code: props.settings.seo_settings?.google_verification_code ?? '',
        google_analytics_script: props.settings.seo_settings?.google_analytics_script ?? '',
        favicon_path: props.settings.seo_settings?.favicon_path ?? '',
        default_og_image_path: props.settings.seo_settings?.default_og_image_path ?? '',
    },
    favicon: null as File | null,
    default_og_image: null as File | null,
    home_faqs: Array.isArray(props.settings.home_faqs) ? props.settings.home_faqs : [],
    home_testimonials: Array.isArray(props.settings.home_testimonials) ? props.settings.home_testimonials : [],
    founder_name: props.settings.founder_name ?? '',
    founder_title: props.settings.founder_title ?? '',
    founder_text: props.settings.founder_text ?? '',
    founder_photo_path: props.settings.founder_photo_path ?? '',
    founder_photo: null as File | null,
});

const faviconPreview = ref(props.settings.seo_settings?.favicon_path ? `/storage/${props.settings.seo_settings.favicon_path}` : '');
const ogImagePreview = ref(props.settings.seo_settings?.default_og_image_path ? `/storage/${props.settings.seo_settings.default_og_image_path}` : '');
const founderPhotoPreview = ref(props.settings.founder_photo_path ? `/storage/${props.settings.founder_photo_path}` : '');

function handleFaviconChange(e: Event) {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) {
        form.favicon = file;
        faviconPreview.value = URL.createObjectURL(file);
    }
}

function handleOgImageChange(e: Event) {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) {
        form.default_og_image = file;
        ogImagePreview.value = URL.createObjectURL(file);
    }
}

function handleFounderPhotoChange(e: Event) {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) {
        form.founder_photo = file;
        founderPhotoPreview.value = URL.createObjectURL(file);
    }
}

const activeTab = ref<'umum' | 'homepage' | 'seo'>('umum');

function addTerm() {
    form.terms_and_conditions.push('');
}
function removeTerm(index: number) {
    form.terms_and_conditions.splice(index, 1);
}

function addUsp() {
    form.home_usps.push('');
}
function removeUsp(index: number) {
    form.home_usps.splice(index, 1);
}

function addService() {
    form.home_services.push({ icon: 'Star', title: '', description: '' });
}
function removeService(index: number) {
    form.home_services.splice(index, 1);
}

function addRequirement(type: 'tourist' | 'resident') {
    form.rental_requirements[type].push('');
}
function removeRequirement(type: 'tourist' | 'resident', index: number) {
    form.rental_requirements[type].splice(index, 1);
}

function addFaq() {
    form.home_faqs.unshift({ question: '', answer: '' });
}
function removeFaq(index: number) {
    form.home_faqs.splice(index, 1);
}

function addTestimonial() {
    form.home_testimonials.unshift({ name: '', rating: 5, review: '' });
}
function removeTestimonial(index: number) {
    form.home_testimonials.splice(index, 1);
}

function submit() {
    form.transform((data) => {
        let wa = data.whatsapp_number.replace(/\D/g, '');
        if (wa.startsWith('0')) wa = wa.substring(1);
        if (!wa.startsWith('62')) wa = '62' + wa;
        
        return {
            ...data,
            whatsapp_number: wa,
        };
    }).post('/vbpanel/settings', {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Pengaturan Website" />
    <FlashMessage />

    <div class="flex flex-col gap-6 p-4 max-w-5xl">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-foreground">Pengaturan Website</h1>
            <p class="text-sm text-muted-foreground">Kelola informasi kontak, media sosial, dan teks halaman depan website.</p>
        </div>

        <div class="flex gap-2 border-b border-border pb-px">
            <button
                @click="activeTab = 'umum'"
                class="px-4 py-2 text-sm font-medium transition-colors border-b-2"
                :class="activeTab === 'umum' ? 'border-primary text-primary' : 'border-transparent text-muted-foreground hover:text-foreground hover:border-muted'"
            >
                Pengaturan Umum
            </button>
            <button
                @click="activeTab = 'homepage'"
                class="px-4 py-2 text-sm font-medium transition-colors border-b-2"
                :class="activeTab === 'homepage' ? 'border-primary text-primary' : 'border-transparent text-muted-foreground hover:text-foreground hover:border-muted'"
            >
                Teks Homepage (Beranda)
            </button>
            <button
                @click="activeTab = 'seo'"
                class="px-4 py-2 text-sm font-medium transition-colors border-b-2"
                :class="activeTab === 'seo' ? 'border-primary text-primary' : 'border-transparent text-muted-foreground hover:text-foreground hover:border-muted'"
            >
                SEO & Meta
            </button>
        </div>

        <form @submit.prevent="submit" class="grid gap-6">
            <div v-show="activeTab === 'umum'" class="grid gap-6">
                <!-- Kontak Utama -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2 text-lg">
                            <Phone class="size-5 text-primary" />
                            Kontak Utama
                        </CardTitle>
                        <CardDescription>Nomor ini akan digunakan untuk fitur "Detail via WhatsApp" pada publik katalog.</CardDescription>
                    </CardHeader>
                    <CardContent class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Nomor WhatsApp *</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-muted-foreground">+62</span>
                                <input
                                    v-model="form.whatsapp_number"
                                    type="text"
                                    placeholder="81234567890"
                                    class="h-9 w-full rounded-md border border-input bg-background pl-10 pr-3 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                                    :class="{ 'border-destructive': form.errors.whatsapp_number }"
                                />
                            </div>
                            <p class="mt-1 text-[11px] text-muted-foreground">Gunakan format angka berawalan 8 langsung (contoh: 8123456), sistem otomatis menambahkan 62.</p>
                            <p v-if="form.errors.whatsapp_number" class="mt-1 text-xs text-destructive">{{ form.errors.whatsapp_number }}</p>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Alamat Email *</label>
                            <div class="relative">
                                <Mail class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
                                <input
                                    v-model="form.company_email"
                                    type="email"
                                    placeholder="info@vinsbali.com"
                                    class="h-9 w-full rounded-md border border-input bg-background pl-10 pr-3 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                                />
                            </div>
                            <p v-if="form.errors.company_email" class="mt-1 text-xs text-destructive">{{ form.errors.company_email }}</p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Alamat Perusahaan -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2 text-lg">
                            <MapPin class="size-5 text-primary" />
                            Alamat Perusahaan
                        </CardTitle>
                        <CardDescription>Alamat akan ditampilkan pada area Footer.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div>
                            <textarea
                                v-model="form.company_address"
                                rows="3"
                                placeholder="Alamat lengkap VINS Bali..."
                                class="w-full rounded-md border border-input bg-background p-3 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                            ></textarea>
                            <p v-if="form.errors.company_address" class="mt-1 text-xs text-destructive">{{ form.errors.company_address }}</p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Media Sosial -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2 text-lg">
                            <Instagram class="size-5 text-primary" />
                            Media Sosial
                        </CardTitle>
                        <CardDescription>Tautan media sosial perusahaan.</CardDescription>
                    </CardHeader>
                    <CardContent class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Tautan Instagram</label>
                            <div class="relative">
                                <Instagram class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
                                <input
                                    v-model="form.instagram_url"
                                    type="url"
                                    placeholder="https://instagram.com/..."
                                    class="h-9 w-full rounded-md border border-input bg-background pl-10 pr-3 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                                />
                            </div>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Tautan Facebook</label>
                            <div class="relative">
                                <Facebook class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
                                <input
                                    v-model="form.facebook_url"
                                    type="url"
                                    placeholder="https://facebook.com/..."
                                    class="h-9 w-full rounded-md border border-input bg-background pl-10 pr-3 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                                />
                            </div>
                        </div>
                    </CardContent>
                </Card>
                
                <!-- Terms -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2 text-lg">
                            <SettingsIcon class="size-5 text-primary" />
                            Syarat dan Ketentuan Utama
                        </CardTitle>
                        <CardDescription>Atur syarat dan ketentuan dasar saat menyewa mobil.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="flex items-center justify-between mb-3 border-b border-border pb-2">
                            <h4 class="font-bold text-sm">Daftar Poin Syarat & Ketentuan</h4>
                            <Button type="button" variant="outline" size="sm" @click="addTerm" class="gap-1.5 h-7 text-xs">
                                <Plus class="size-3" /> Tambah
                            </Button>
                        </div>
                        <div class="grid gap-2">
                            <div v-for="(term, index) in form.terms_and_conditions" :key="`term_${index}`" class="flex gap-2 items-center">
                                <div class="size-1.5 rounded-full bg-primary shrink-0"></div>
                                <input
                                    v-model="form.terms_and_conditions[index]"
                                    type="text"
                                    placeholder="Contoh: Penyewa harus berusia minimal 21 tahun..."
                                    class="h-9 flex-1 rounded-md border border-input bg-background px-3 text-xs sm:text-sm outline-none transition focus:border-primary"
                                />
                                <Button type="button" variant="ghost" size="icon" class="text-destructive h-9 w-9 shrink-0" @click="removeTerm(index)">
                                    <Trash2 class="size-3.5" />
                                </Button>
                            </div>
                        </div>
                        <p v-if="form.terms_and_conditions.length === 0" class="text-sm text-muted-foreground italic mt-2">Belum ada data. Silakan tambah poin.</p>
                        <p v-if="form.errors.terms_and_conditions" class="mt-2 text-xs text-destructive">{{ form.errors.terms_and_conditions }}</p>
                    </CardContent>
                </Card>
            </div>

            <div v-show="activeTab === 'homepage'" class="grid gap-6">
                <!-- Hero Section Texts -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2 text-lg">
                            <Star class="size-5 text-primary" />
                            Teks Utama (Hero Section)
                        </CardTitle>
                        <CardDescription>Ubah judul dan paragraf utama web anda.</CardDescription>
                    </CardHeader>
                    <CardContent class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Judul Utama</label>
                            <input
                                v-model="form.home_hero_title"
                                type="text"
                                placeholder="..."
                                class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                            />
                            <p v-if="form.errors.home_hero_title" class="mt-1 text-xs text-destructive">{{ form.errors.home_hero_title }}</p>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Kata Sorotan (Gradient)</label>
                            <input
                                v-model="form.home_hero_highlight"
                                type="text"
                                placeholder="..."
                                class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                            />
                            <p v-if="form.errors.home_hero_highlight" class="mt-1 text-xs text-destructive">{{ form.errors.home_hero_highlight }}</p>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-sm font-medium">Sub-judul / Deskripsi Pendek</label>
                            <textarea
                                v-model="form.home_hero_subtitle"
                                rows="2"
                                placeholder="..."
                                class="w-full rounded-md border border-input bg-background p-3 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                            ></textarea>
                            <p v-if="form.errors.home_hero_subtitle" class="mt-1 text-xs text-destructive">{{ form.errors.home_hero_subtitle }}</p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Brand Logos -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2 text-lg">
                            <Star class="size-5 text-primary" />
                            Logo Brand Mobil
                        </CardTitle>
                        <CardDescription>Pilih logo brand mobil yang akan Informasi Dasar sebagai marquee strip di halaman depan.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <BrandLogoPicker v-model="form.home_brand_logos" />
                    </CardContent>
                </Card>

                <!-- Homepage USPs -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center justify-between text-lg">
                            <div class="flex items-center gap-2">
                                <Star class="size-5 text-primary" />
                                Unique Selling Propositions (USP)
                            </div>
                            <Button type="button" variant="outline" size="sm" @click="addUsp" class="gap-1.5 h-8">
                                <Plus class="size-3.5" /> Tambah
                            </Button>
                        </CardTitle>
                        <CardDescription>Nilai jual utama yang muncul pada bagian atas di halaman depan website.</CardDescription>
                    </CardHeader>
                    <CardContent class="grid gap-3">
                        <div v-for="(usp, index) in form.home_usps" :key="`usp_${index}`" class="flex gap-2 items-center">
                            <GripVertical class="size-4 text-muted-foreground shrink-0 cursor-move" />
                            <input
                                v-model="form.home_usps[index]"
                                type="text"
                                placeholder="Highlight fitur/layanan..."
                                class="h-9 flex-1 rounded-md border border-input bg-background px-3 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                            />
                            <Button type="button" variant="ghost" size="icon" class="text-destructive h-9 w-9" @click="removeUsp(index)">
                                <Trash2 class="size-4" />
                            </Button>
                        </div>
                        <p v-if="form.home_usps.length === 0" class="text-sm text-muted-foreground italic">Belum ada data. Silakan tambah.</p>
                    </CardContent>
                </Card>

                <!-- Homepage Services -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center justify-between text-lg">
                            <div class="flex items-center gap-2">
                                <MapPin class="size-5 text-primary" />
                                Layanan Ekstra / Servis
                            </div>
                            <Button type="button" variant="outline" size="sm" @click="addService" class="gap-1.5 h-8">
                                <Plus class="size-3.5" /> Tambah
                            </Button>
                        </CardTitle>
                        <CardDescription>Layanan unggulan (seperti Chauffeur, Pengantaran) di halaman beranda.</CardDescription>
                    </CardHeader>
                    <CardContent class="grid gap-4">
                        <div v-for="(service, index) in form.home_services" :key="`svc_${index}`" class="rounded-md border border-border p-4 relative group bg-muted/10 pr-12 mt-2">
                            <Button type="button" title="Hapus Servis" variant="ghost" size="icon" class="text-destructive absolute right-2 top-2 h-8 w-8" @click="removeService(index)">
                                <Trash2 class="size-4" />
                            </Button>
                            
                            <div class="grid sm:grid-cols-[200px_1fr] gap-4">
                                <div>
                                    <label class="mb-1.5 block text-xs font-semibold text-muted-foreground">Ikon Lucide</label>
                                    <IconPicker v-model="service.icon" />
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-xs font-semibold text-muted-foreground">Judul Layanan</label>
                                    <input
                                        v-model="service.title"
                                        type="text"
                                        placeholder="Contoh: Layanan Chauffeur"
                                        class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                                    />
                                </div>
                            </div>
                            <div class="mt-3">
                                <label class="mb-1.5 block text-xs font-semibold text-muted-foreground">Deskripsi Singkat</label>
                                <textarea
                                    v-model="service.description"
                                    rows="2"
                                    placeholder="Pesan kendaraan mewah beserta supir..."
                                    class="w-full rounded-md border border-input bg-background p-3 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                                ></textarea>
                            </div>
                        </div>
                        <p v-if="form.home_services.length === 0" class="text-sm text-muted-foreground italic">Belum ada data. Silakan tambah.</p>
                    </CardContent>
                </Card>
                
                <!-- Rental Requirements -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center justify-between text-lg">
                            <div class="flex items-center gap-2">
                                <CheckCircle2 class="size-5 text-primary" />
                                Persyaratan Sewa
                            </div>
                        </CardTitle>
                        <CardDescription>Dokumen persyaratan menyewa mobil untuk turis dan warga lokal.</CardDescription>
                    </CardHeader>
                    <CardContent class="grid md:grid-cols-2 gap-8">
                        <!-- Tourist -->
                        <div>
                            <div class="flex items-center justify-between mb-3 border-b border-border pb-2">
                                <h4 class="font-bold text-sm">Dokumen Turis</h4>
                                <Button type="button" variant="outline" size="sm" @click="addRequirement('tourist')" class="gap-1.5 h-7 text-xs">
                                    <Plus class="size-3" /> Tambah
                                </Button>
                            </div>
                            <div class="grid gap-2">
                                <div v-for="(req, index) in form.rental_requirements.tourist" :key="`t_${index}`" class="flex gap-2 items-center">
                                    <div class="size-1.5 rounded-full bg-primary shrink-0"></div>
                                    <input
                                        v-model="form.rental_requirements.tourist[index]"
                                        type="text"
                                        placeholder="Contoh: Paspor Valid"
                                        class="h-8 flex-1 rounded-md border border-input bg-background px-2 text-xs outline-none transition focus:border-primary"
                                    />
                                    <Button type="button" variant="ghost" size="icon" class="text-destructive h-8 w-8 shrink-0" @click="removeRequirement('tourist', index)">
                                        <Trash2 class="size-3.5" />
                                    </Button>
                                </div>
                            </div>
                        </div>

                        <!-- Resident -->
                        <div>
                            <div class="flex items-center justify-between mb-3 border-b border-border pb-2">
                                <h4 class="font-bold text-sm">Dokumen Warga Lokal</h4>
                                <Button type="button" variant="outline" size="sm" @click="addRequirement('resident')" class="gap-1.5 h-7 text-xs">
                                    <Plus class="size-3" /> Tambah
                                </Button>
                            </div>
                            <div class="grid gap-2">
                                <div v-for="(req, index) in form.rental_requirements.resident" :key="`r_${index}`" class="flex gap-2 items-center">
                                    <div class="size-1.5 rounded-full bg-primary shrink-0"></div>
                                    <input
                                        v-model="form.rental_requirements.resident[index]"
                                        type="text"
                                        placeholder="Contoh: KTP"
                                        class="h-8 flex-1 rounded-md border border-input bg-background px-2 text-xs outline-none transition focus:border-primary"
                                    />
                                    <Button type="button" variant="ghost" size="icon" class="text-destructive h-8 w-8 shrink-0" @click="removeRequirement('resident', index)">
                                        <Trash2 class="size-3.5" />
                                    </Button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="md:col-span-2 mt-2 pt-4 border-t border-border">
                            <label class="mb-1.5 block text-sm font-medium">Teks Catatan Tambahan (Footer Persyaratan)</label>
                            <textarea
                                v-model="form.rental_requirements_footer"
                                rows="2"
                                placeholder="Umur minimal 21 tahun..."
                                class="w-full rounded-md border border-input bg-background p-3 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                            ></textarea>
                            <p v-if="form.errors.rental_requirements_footer" class="mt-1 text-xs text-destructive">{{ form.errors.rental_requirements_footer }}</p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Founder's Note -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2 text-lg">
                            <Star class="size-5 text-primary" />
                            Catatan Pendiri (Founder's Note)
                        </CardTitle>
                        <CardDescription>Pesan pribadi dan visi dari pendiri/owner untuk membangun kepercayaan pelanggan.</CardDescription>
                    </CardHeader>
                    <CardContent class="grid gap-6 sm:grid-cols-[250px_1fr]">
                        <!-- Left column: Photo upload and basic info -->
                        <div class="space-y-4 border-r border-border/50 pr-0 sm:pr-6">
                            <div>
                                <label class="mb-1.5 block text-xs font-semibold text-muted-foreground">Foto Pendiri</label>
                                <div class="flex flex-col items-center gap-3">
                                    <div class="size-28 rounded-2xl border-2 border-primary/20 bg-muted flex items-center justify-center overflow-hidden shrink-0 shadow-inner">
                                        <img v-if="founderPhotoPreview" :src="founderPhotoPreview" class="h-full w-full object-cover" />
                                        <span v-else class="text-xs text-muted-foreground italic text-center p-2">Belum ada foto</span>
                                    </div>
                                    <input 
                                        type="file" 
                                        accept="image/png,image/jpeg,image/webp" 
                                        @change="handleFounderPhotoChange" 
                                        class="text-xs text-muted-foreground file:mr-2 file:py-1 file:px-2 file:rounded-md file:border file:border-input file:bg-background file:text-foreground file:text-xs file:cursor-pointer hover:file:bg-muted w-full"
                                    />
                                    <p class="text-[10px] text-muted-foreground text-center">Format: PNG, JPG, WEBP. Max: 2MB.</p>
                                </div>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-semibold text-muted-foreground">Nama Pendiri</label>
                                <input
                                    v-model="form.founder_name"
                                    type="text"
                                    placeholder="Contoh: I Putu Vinso"
                                    class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                                />
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-semibold text-muted-foreground">Jabatan</label>
                                <input
                                    v-model="form.founder_title"
                                    type="text"
                                    placeholder="Contoh: Founder & CEO"
                                    class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                                />
                            </div>
                        </div>
                        <!-- Right column: The text -->
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-muted-foreground">Pesan Pribadi Pendiri</label>
                            <textarea
                                v-model="form.founder_text"
                                rows="10"
                                placeholder="Tulis pesan sambutan atau komitmen pelayanan premium Anda kepada pelanggan..."
                                class="w-full rounded-md border border-input bg-background p-3 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                            ></textarea>
                            <p class="mt-1 text-[11px] text-muted-foreground">Pesan ini akan ditampilkan dengan tipografi premium di halaman depan website Anda.</p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Testimonials -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center justify-between text-lg">
                            <div class="flex items-center gap-2">
                                <Star class="size-5 text-primary" />
                                Ulasan Pelanggan (Testimonial)
                            </div>
                            <Button type="button" variant="outline" size="sm" @click="addTestimonial" class="gap-1.5 h-8">
                                <Plus class="size-3.5" /> Tambah Ulasan
                            </Button>
                        </CardTitle>
                        <CardDescription>Kelola ulasan nyata dari pelanggan untuk meningkatkan kredibilitas di halaman depan.</CardDescription>
                    </CardHeader>
                    <CardContent class="grid gap-4">
                        <div v-for="(item, index) in form.home_testimonials" :key="`testi_${index}`" class="rounded-xl border border-border p-4 relative group bg-muted/10 pr-12">
                            <Button type="button" title="Hapus Ulasan" variant="ghost" size="icon" class="text-destructive absolute right-2 top-2 h-8 w-8" @click="removeTestimonial(index)">
                                <Trash2 class="size-4" />
                            </Button>
                            
                            <div class="grid sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="mb-1.5 block text-xs font-semibold text-muted-foreground">Nama Pelanggan</label>
                                    <input
                                        v-model="item.name"
                                        type="text"
                                        placeholder="Contoh: John Doe"
                                        class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                                    />
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-xs font-semibold text-muted-foreground">Rating Bintang (1 - 5)</label>
                                    <select
                                        v-model.number="item.rating"
                                        class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                                    >
                                        <option :value="5">⭐⭐⭐⭐⭐ (5 Bintang)</option>
                                        <option :value="4">⭐⭐⭐⭐ (4 Bintang)</option>
                                        <option :value="3">⭐⭐⭐ (3 Bintang)</option>
                                        <option :value="2">⭐⭐ (2 Bintang)</option>
                                        <option :value="1">⭐ (1 Bintang)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-3">
                                <label class="mb-1.5 block text-xs font-semibold text-muted-foreground">Teks Ulasan</label>
                                <textarea
                                    v-model="item.review"
                                    rows="2"
                                    placeholder="Tulis ulasan pelanggan di sini..."
                                    class="w-full rounded-md border border-input bg-background p-3 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                                ></textarea>
                            </div>
                        </div>
                        <p v-if="form.home_testimonials.length === 0" class="text-sm text-muted-foreground italic text-center py-4">Belum ada ulasan pelanggan. Silakan tambah.</p>
                    </CardContent>
                </Card>

                <!-- FAQ -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center justify-between text-lg">
                            <div class="flex items-center gap-2">
                                <Star class="size-5 text-primary" />
                                Tanya Jawab Dinamis (FAQ)
                            </div>
                            <Button type="button" variant="outline" size="sm" @click="addFaq" class="gap-1.5 h-8">
                                <Plus class="size-3.5" /> Tambah FAQ
                            </Button>
                        </CardTitle>
                        <CardDescription>Kelola daftar pertanyaan yang sering diajukan beserta jawabannya.</CardDescription>
                    </CardHeader>
                    <CardContent class="grid gap-4">
                        <div v-for="(item, index) in form.home_faqs" :key="`faq_${index}`" class="rounded-xl border border-border p-4 relative group bg-muted/10 pr-12">
                            <Button type="button" title="Hapus FAQ" variant="ghost" size="icon" class="text-destructive absolute right-2 top-2 h-8 w-8" @click="removeFaq(index)">
                                <Trash2 class="size-4" />
                            </Button>
                            
                            <div>
                                <label class="mb-1.5 block text-xs font-semibold text-muted-foreground">Pertanyaan (Question)</label>
                                <input
                                    v-model="item.question"
                                    type="text"
                                    placeholder="Contoh: Apakah bisa sewa mobil lepas kunci?"
                                    class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                                />
                            </div>
                            <div class="mt-3">
                                <label class="mb-1.5 block text-xs font-semibold text-muted-foreground">Jawaban (Answer)</label>
                                <textarea
                                    v-model="item.answer"
                                    rows="3"
                                    placeholder="Tulis jawaban lengkap di sini..."
                                    class="w-full rounded-md border border-input bg-background p-3 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                                ></textarea>
                            </div>
                        </div>
                        <p v-if="form.home_faqs.length === 0" class="text-sm text-muted-foreground italic text-center py-4">Belum ada daftar FAQ. Silakan tambah.</p>
                    </CardContent>
                </Card>
            </div>

            <div v-show="activeTab === 'seo'" class="grid gap-6">
                <!-- Default SEO -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2 text-lg">
                            <Star class="size-5 text-primary" />
                            SEO Default (Global)
                        </CardTitle>
                        <CardDescription>Meta tags standar jika halaman tidak memiliki SEO spesifik.</CardDescription>
                    </CardHeader>
                    <CardContent class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Site Name</label>
                            <input v-model="form.seo_settings.site_name" type="text" class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm outline-none transition focus:border-primary" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Author</label>
                            <input v-model="form.seo_settings.author" type="text" class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm outline-none transition focus:border-primary" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Robots</label>
                            <input v-model="form.seo_settings.robots" type="text" placeholder="index, follow" class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm outline-none transition focus:border-primary" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Twitter Handle</label>
                            <input v-model="form.seo_settings.twitter_handle" type="text" placeholder="@vinsbali" class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm outline-none transition focus:border-primary" />
                        </div>
                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-sm font-medium">Default Keywords</label>
                            <input v-model="form.seo_settings.default_keywords" type="text" placeholder="keyword1, keyword2, keyword3" class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm outline-none transition focus:border-primary" />
                        </div>
                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-sm font-medium">Default Title (OG)</label>
                            <input v-model="form.seo_settings.default_title" type="text" class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm outline-none transition focus:border-primary" />
                        </div>
                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-sm font-medium">Default Description</label>
                            <textarea v-model="form.seo_settings.default_description" rows="2" class="w-full rounded-md border border-input bg-background p-3 text-sm outline-none transition focus:border-primary"></textarea>
                        </div>
                    </CardContent>
                </Card>

                <!-- Home SEO -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2 text-lg">
                            <Star class="size-5 text-primary" />
                            SEO Beranda (Home)
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-sm font-medium">Home Keywords</label>
                            <input v-model="form.seo_settings.home_keywords" type="text" class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm outline-none transition focus:border-primary" />
                        </div>
                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-sm font-medium">Home Title</label>
                            <input v-model="form.seo_settings.home_title" type="text" class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm outline-none transition focus:border-primary" />
                        </div>
                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-sm font-medium">Home Description</label>
                            <textarea v-model="form.seo_settings.home_description" rows="2" class="w-full rounded-md border border-input bg-background p-3 text-sm outline-none transition focus:border-primary"></textarea>
                        </div>
                    </CardContent>
                </Card>
                
                <!-- Catalog SEO -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2 text-lg">
                            <Star class="size-5 text-primary" />
                            SEO Katalog (Index)
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-sm font-medium">Catalog Keywords</label>
                            <input v-model="form.seo_settings.catalog_keywords" type="text" class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm outline-none transition focus:border-primary" />
                        </div>
                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-sm font-medium">Catalog Title</label>
                            <input v-model="form.seo_settings.catalog_title" type="text" class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm outline-none transition focus:border-primary" />
                        </div>
                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-sm font-medium">Catalog Description</label>
                            <textarea v-model="form.seo_settings.catalog_description" rows="2" class="w-full rounded-md border border-input bg-background p-3 text-sm outline-none transition focus:border-primary"></textarea>
                        </div>
                    </CardContent>
                </Card>

                <!-- Google Search Console & Analytics -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2 text-lg">
                            <span class="text-primary font-bold">G</span>
                            Google Search Console & Analytics
                        </CardTitle>
                        <CardDescription>Integrasikan kode verifikasi Google Search Console dan script pelacakan pengunjung.</CardDescription>
                    </CardHeader>
                    <CardContent class="grid gap-4">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Google Verification Code</label>
                            <input 
                                v-model="form.seo_settings.google_verification_code" 
                                type="text" 
                                placeholder="Contoh: google-site-verification=xyz123abc..." 
                                class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm outline-none transition focus:border-primary" 
                            />
                            <p class="mt-1 text-[11px] text-muted-foreground">Isi dengan kode verifikasi GSC Anda. Kode ini akan dirender sebagai meta tag google-site-verification.</p>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Google Analytics / GTM Script</label>
                            <textarea 
                                v-model="form.seo_settings.google_analytics_script" 
                                rows="5" 
                                placeholder="<!-- Google tag (gtag.js) -->&#10;<script>...</script>" 
                                class="w-full rounded-md border border-input bg-background p-3 text-xs font-mono outline-none transition focus:border-primary"
                            ></textarea>
                            <p class="mt-1 text-[11px] text-muted-foreground">Tempel script pelacakan utuh (termasuk tag &lt;script&gt;). Hanya dimuat pada halaman publik pengunjung.</p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Favicon & Default OG Image -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2 text-lg">
                            <span class="text-primary font-bold">Aset</span>
                            Favicon & Default OG Image
                        </CardTitle>
                        <CardDescription>Upload favicon kustom dan default share image untuk WhatsApp/Sosial Media.</CardDescription>
                    </CardHeader>
                    <CardContent class="grid gap-6 sm:grid-cols-2">
                        <!-- Favicon Upload -->
                        <div class="space-y-3">
                            <label class="block text-sm font-medium">Favicon</label>
                            <div class="flex items-center gap-4">
                                <div class="size-16 rounded border bg-muted/20 flex items-center justify-center overflow-hidden shrink-0">
                                    <img v-if="faviconPreview" :src="faviconPreview" class="h-10 w-10 object-contain" />
                                    <span v-else class="text-xs text-muted-foreground italic">Default</span>
                                </div>
                                <div class="flex-1">
                                    <input 
                                        type="file" 
                                        accept="image/png,image/x-icon,image/svg+xml,image/jpeg" 
                                        @change="handleFaviconChange" 
                                        class="text-xs text-muted-foreground file:mr-2 file:py-1 file:px-2 file:rounded file:border file:border-input file:bg-background file:text-foreground file:text-xs file:cursor-pointer hover:file:bg-muted"
                                    />
                                    <p class="mt-1 text-[10px] text-muted-foreground">Format: PNG, ICO, SVG. Max: 2MB.</p>
                                </div>
                            </div>
                        </div>

                        <!-- OG Image Upload -->
                        <div class="space-y-3">
                            <label class="block text-sm font-medium">Default Share Image (OG:Image)</label>
                            <div class="flex items-center gap-4">
                                <div class="h-16 w-28 rounded border bg-muted/20 flex items-center justify-center overflow-hidden shrink-0">
                                    <img v-if="ogImagePreview" :src="ogImagePreview" class="h-full w-full object-cover" />
                                    <span v-else class="text-xs text-muted-foreground italic">Default</span>
                                </div>
                                <div class="flex-1">
                                    <input 
                                        type="file" 
                                        accept="image/png,image/jpeg,image/webp" 
                                        @change="handleOgImageChange" 
                                        class="text-xs text-muted-foreground file:mr-2 file:py-1 file:px-2 file:rounded file:border file:border-input file:bg-background file:text-foreground file:text-xs file:cursor-pointer hover:file:bg-muted"
                                    />
                                    <p class="mt-1 text-[10px] text-muted-foreground">Format: PNG, JPG, WEBP (1200x630px). Max: 5MB.</p>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div class="flex justify-start sm:justify-end gap-3 sticky bottom-4 z-10 pt-4 bg-background/80 backdrop-blur-md">
                <Button type="submit" class="w-full sm:w-auto" :disabled="form.processing">
                    <SettingsIcon class="mr-2 size-4" />
                    {{ form.processing ? 'Menyimpan...' : 'Simpan Pengaturan' }}
                </Button>
            </div>
        </form>
    </div>
</template>
