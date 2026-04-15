<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ArrowRight, CheckCircle2, ChevronRight, Clock, MapPin, MessageCircle, ShieldCheck, Sparkles, Star } from 'lucide-vue-next';
import CarCard from '@/components/CarCard.vue';
import FlashMessage from '@/components/FlashMessage.vue';
import { Button } from '@/components/ui/button';
import { type Car } from '@/types';
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps<{
    featuredCars: Car[];
    heroImages: string[];
    categories: string[];
}>();

const page = usePage();
const settings = computed(() => page.props.global_settings as Record<string, any>);

const currentImageIndex = ref(0);
let slideInterval: number;

onMounted(() => {
    if (props.heroImages && props.heroImages.length > 0) {
        slideInterval = window.setInterval(() => {
            currentImageIndex.value = (currentImageIndex.value + 1) % props.heroImages.length;
        }, 5000);
    }
});

onUnmounted(() => {
    if (slideInterval) clearInterval(slideInterval);
});

// Categories from database
const popularCategories = computed(() => ['Semua', ...props.categories]);
const activeCategory = ref('Semua');

// Filter featured cars by active category if it's set
// Filter featured cars by active category if it's set
const filteredCars = computed(() => {
    if (activeCategory.value === 'Semua') return props.featuredCars;
    return props.featuredCars.filter(c => c.category?.name === activeCategory.value);
});

import * as LucideIcons from 'lucide-vue-next';
function getIconComponent(name: string) {
    // @ts-ignore
    return LucideIcons[name] || LucideIcons['Star'];
}

const usps = computed(() => {
    return Array.isArray(settings.value.home_usps) && settings.value.home_usps.length > 0
        ? settings.value.home_usps
        : ['Tanpa Deposit', 'PPN Sudah Termasuk', 'Menerima Semua Bentuk Pembayaran', 'Gratis Bensin Penuh', 'Tol Sudah Termasuk'];
});

const homeServices = computed(() => {
    return Array.isArray(settings.value.home_services) ? settings.value.home_services : [];
});

const rentalRequirements = computed(() => {
    return settings.value.rental_requirements || { tourist: [], resident: [] };
});

const heroTitle = computed(() => settings.value.home_hero_title || 'Luxury Car Rental in');
const heroHighlight = computed(() => settings.value.home_hero_highlight || 'Bali');
const heroSubtitle = computed(() => settings.value.home_hero_subtitle || 'Pilih dari 80+ koleksi mobil premium, sports, dan eksklusif. Pengalaman berkendara VIP yang tak tertandingi di Pulau Dewata.');
const requirementsFooter = computed(() => settings.value.rental_requirements_footer || 'Umur minimal 21 tahun untuk mobil standar, dan 25 tahun untuk mobil sports. Asuransi dasar, bantuan pinggir jalan, dan PPN sudah kami tanggung. Jarak tempuh standar 250km/hari.');

const brandLogos = computed(() => {
    return Array.isArray(settings.value.home_brand_logos) ? settings.value.home_brand_logos : [];
});

function brandLogoUrl(slug: string) {
    return `https://cdn.jsdelivr.net/gh/filippofilip95/car-logos-dataset@master/logos/optimized/${slug}.png`;
}
</script>

<template>
    <Head title="Premium Car Rental in Bali" />
    <FlashMessage />

    <!-- STAGE 1: Hero Section & USPs -->
    <section class="relative flex min-h-[90vh] flex-col overflow-hidden bg-gradient-to-br from-gray-950 via-gray-900 to-gray-800 pt-24">
        
        <!-- Image Slider Background -->
        <!-- Static hero image -->
        <div class="absolute inset-0 bg-cover bg-center opacity-40" style="background-image: url('/images/hero-img.png')"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-gray-950 via-gray-900/70 to-transparent"></div>

        <!-- Dynamic hero image carousel (commented out for now)
        <template v-if="heroImages && heroImages.length > 0">
            <div 
                v-for="(img, idx) in heroImages" 
                :key="img"
                class="absolute inset-0 transition-opacity duration-1000 ease-in-out bg-cover bg-center"
                :style="`background-image: url('${img}')`"
                :class="idx === currentImageIndex ? 'opacity-40' : 'opacity-0'"
            ></div>
            <div class="absolute inset-0 bg-gradient-to-t from-gray-950 via-gray-900/70 to-transparent"></div>
        </template>
        <template v-else>
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-indigo-900/20 via-transparent to-transparent"></div>
        </template>
        -->

        <div class="relative z-10 flex flex-1 flex-col items-center justify-center px-4 text-center sm:px-6 lg:px-8">
            <h1 class="animate-slide-up text-5xl font-bold leading-tight tracking-tight text-white sm:text-6xl lg:text-7xl">
                {{ heroTitle }}
                <span class="bg-gradient-to-r from-red-400 to-amber-400 bg-clip-text text-transparent">{{ heroHighlight }}</span>
            </h1>

            <p class="mx-auto mt-6 max-w-2xl animate-slide-up text-lg leading-relaxed text-gray-300 animation-delay-200">
                {{ heroSubtitle }}
            </p>

            <div class="mt-8 flex animate-slide-up flex-wrap items-center justify-center gap-4 animation-delay-400">
                <Link href="#fleet">
                    <Button size="lg" class="bg-red-700 text-white hover:bg-red-800 gap-2 px-8 py-6 text-lg">
                        Eksplorasi Armanda
                        <ArrowRight class="size-5" />
                    </Button>
                </Link>
            </div>
            
            <!-- Unique Selling Propositions (Trinity Style) -->
            <div class="mt-16 grid animate-slide-up grid-cols-2 gap-4 animation-delay-500 sm:grid-cols-3 lg:flex lg:flex-wrap lg:justify-center">
                <div v-for="(usp, index) in usps" :key="index" class="flex items-center gap-2 rounded-lg bg-black/40 px-4 py-2.5 text-sm font-medium text-gray-200 backdrop-blur-md border border-white/5">
                    <CheckCircle2 class="size-4 text-amber-500 shrink-0" />
                    {{ usp }}
                </div>
            </div>
        </div>
        
        <div class="absolute right-0 bottom-0 left-0 h-32 bg-gradient-to-t from-background to-transparent"></div>
    </section>

    <!-- Brand Logos Marquee Strip -->
    <section v-if="brandLogos.length" class="border-y border-border/30 bg-muted/10 py-10 overflow-hidden">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mb-6 text-center">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-muted-foreground">Trusted Car Brands We Partner With</p>
        </div>
        <div class="relative">
            <!-- Fade edges -->
            <div class="absolute left-0 top-0 bottom-0 w-24 bg-gradient-to-r from-background to-transparent z-10"></div>
            <div class="absolute right-0 top-0 bottom-0 w-24 bg-gradient-to-l from-background to-transparent z-10"></div>
            <!-- Scrolling track -->
            <div class="flex animate-marquee gap-16 w-max">
                <div v-for="brand in brandLogos" :key="brand.slug + '_1'" class="flex flex-col items-center gap-2 shrink-0 opacity-50 hover:opacity-100 transition-opacity duration-300">
                    <img :src="brandLogoUrl(brand.slug)" :alt="brand.label" class="h-10 w-auto" loading="lazy" />
                    <span class="text-[10px] font-medium text-muted-foreground">{{ brand.label }}</span>
                </div>
                <!-- Duplicate for seamless loop -->
                <div v-for="brand in brandLogos" :key="brand.slug + '_2'" class="flex flex-col items-center gap-2 shrink-0 opacity-50 hover:opacity-100 transition-opacity duration-300">
                    <img :src="brandLogoUrl(brand.slug)" :alt="brand.label" class="h-10 w-auto" loading="lazy" />
                    <span class="text-[10px] font-medium text-muted-foreground">{{ brand.label }}</span>
                </div>
            </div>
        </div>
    </section>

    <!-- STAGE 2: Search & Vehicle Exploration -->
    <section id="fleet" class="py-20 bg-background">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center sm:text-left">
                <h2 class="text-3xl font-bold tracking-tight text-foreground sm:text-4xl">
                    Temukan Mobil Ideal Anda
                </h2>
                <p class="mt-3 text-base text-muted-foreground">
                    Filter berdasarkan gaya berkendara dan tujuan perjalanan Anda.
                </p>
            </div>

            <!-- Categories Filter -->
            <div class="mt-8 flex flex-wrap gap-2 justify-center sm:justify-start">
                <button
                    v-for="cat in popularCategories"
                    :key="cat"
                    @click="activeCategory = cat"
                    class="rounded-full px-5 py-2 text-sm font-semibold transition-all"
                    :class="activeCategory === cat ? 'bg-primary text-primary-foreground shadow-md' : 'bg-muted text-muted-foreground hover:bg-muted/80 hover:text-foreground'"
                >
                    {{ cat }}
                </button>
            </div>

            <!-- Featured Cars Grid -->
            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <CarCard
                    v-for="car in filteredCars"
                    :key="car.id"
                    :car="car"
                />
            </div>
            
            <div v-if="filteredCars.length === 0" class="mt-12 rounded-xl border border-dashed border-border p-12 text-center text-muted-foreground">
                Tidak ada mobil dalam kategori ini.
            </div>

            <div class="mt-12 text-center">
                <Link href="/catalog">
                    <Button variant="outline" size="lg" class="gap-2">
                        Lihat Seluruh Katalog
                        <ChevronRight class="size-4" />
                    </Button>
                </Link>
            </div>
        </div>
    </section>

    <!-- STAGE 3: Specific Services & Purpose -->
    <section class="border-t border-border/50 bg-muted/20 py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-bold tracking-tight text-foreground">Layanan Premium Eksklusif</h2>
                <p class="mx-auto mt-4 max-w-2xl text-muted-foreground">Lebih dari sekadar sewa mobil harian, kami menyediakan solusi mobilitas untuk setiap kebutuhan VIP Anda.</p>
            </div>

            <div class="mt-16 grid gap-8 md:grid-cols-3">
                <div v-for="service in homeServices" :key="service.title" class="rounded-2xl border border-border bg-card p-8 shadow-sm transition hover:shadow-md">
                    <div class="inline-flex size-12 items-center justify-center rounded-xl bg-primary/10 text-primary">
                        <component :is="getIconComponent(service.icon)" class="size-6" />
                    </div>
                    <h3 class="mt-5 text-xl font-bold">{{ service.title }}</h3>
                    <p class="mt-3 leading-relaxed text-muted-foreground text-sm">
                        {{ service.description }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- STAGE 4: FAQ & Fast Rules -->
    <section class="py-24 bg-background">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 text-center border p-12 rounded-3xl border-border bg-card shadow-sm">
            <h2 class="text-3xl font-bold tracking-tight text-foreground">Kriteria Penyewaan Cepat</h2>
            <div class="mt-8 grid gap-8 sm:grid-cols-2 text-left">
                <div>
                    <h4 class="font-bold flex items-center gap-2"><CheckCircle2 class="size-4 text-green-500" /> Turis / Wisatawan</h4>
                    <ul class="mt-3 space-y-2 text-sm text-muted-foreground">
                        <li v-for="req in rentalRequirements.tourist" :key="req">• {{ req }}</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold flex items-center gap-2"><CheckCircle2 class="size-4 text-green-500" /> Warga Lokal / Resident</h4>
                    <ul class="mt-3 space-y-2 text-sm text-muted-foreground">
                        <li v-for="req in rentalRequirements.resident" :key="req">• {{ req }}</li>
                    </ul>
                </div>
            </div>
            <div class="mt-8 border-t border-border pt-6 text-sm text-muted-foreground">
                {{ requirementsFooter }}
            </div>
        </div>
    </section>

    <!-- STAGE 5: CTA Booking & Callback -->
    <section class="py-24 bg-gradient-to-b from-background to-muted/30">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-16 lg:grid-cols-2 items-center">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">Hanya 3 Langkah Mudah</h2>
                    <div class="mt-8 space-y-6">
                        <div class="flex gap-4">
                            <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-primary text-primary-foreground font-bold">1</div>
                            <div>
                                <h4 class="text-lg font-bold">Pilih Mobil Anda</h4>
                                <p class="text-muted-foreground text-sm mt-1">Eksplorasi armada, bandingkan fitur, dan pilih tanggal penyewaan.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-primary text-primary-foreground font-bold">2</div>
                            <div>
                                <h4 class="text-lg font-bold">Booking Online / WhatsApp</h4>
                                <p class="text-muted-foreground text-sm mt-1">Konfirmasi persyaratan dokumen. Tidak perlu registrasi panjang.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-primary text-primary-foreground font-bold">3</div>
                            <div>
                                <h4 class="text-lg font-bold">Pengiriman Gratis</h4>
                                <p class="text-muted-foreground text-sm mt-1">Kami antarkan mobil dalam kondisi bersih sempurna langsung ke lokasi Anda di Bali.</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-12 flex items-center gap-3 rounded-xl border border-border bg-card p-4">
                        <div class="flex gap-0.5 text-amber-500">
                            <Star class="size-4 fill-current" />
                            <Star class="size-4 fill-current" />
                            <Star class="size-4 fill-current" />
                            <Star class="size-4 fill-current" />
                            <Star class="size-4 fill-current" />
                        </div>
                        <p class="text-sm font-semibold">Berdasarkan 500+ Ulasan Pelanggan Bintang 5</p>
                    </div>
                </div>

                <div class="rounded-2xl border border-border bg-card p-8 shadow-xl">
                    <h3 class="text-2xl font-bold">Kami Hubungi Anda Segera</h3>
                    <p class="mt-2 text-sm text-muted-foreground">Tinggalkan nomor Anda, manajer VIP kami akan menghubungi dalam 1 menit.</p>
                    
                    <form class="mt-8 space-y-4" @submit.prevent>
                        <div>
                            <label class="text-sm font-medium">Nama Anda</label>
                            <input type="text" class="mt-1.5 h-10 w-full rounded-md border border-input bg-background px-3 text-sm focus:border-primary focus:ring-1 focus:ring-primary" placeholder="John Doe" />
                        </div>
                        <div>
                            <label class="text-sm font-medium">Nomor WhatsApp</label>
                            <input type="text" class="mt-1.5 h-10 w-full rounded-md border border-input bg-background px-3 text-sm focus:border-primary focus:ring-1 focus:ring-primary" placeholder="+62 8xx xxxx xxxx" />
                        </div>
                        <Button class="w-full mt-2" size="lg">Request Callback</Button>
                        <p class="text-xs text-center text-muted-foreground mt-4">Atau hubungi langsung tim kami <br/> <a :href="`https://wa.me/${$page.props.global_settings?.whatsapp_number || ''}`" target="_blank" class="font-bold text-primary hover:underline">via WhatsApp</a></p>
                    </form>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
@keyframes marquee {
    0% {
        transform: translateX(0);
    }
    100% {
        transform: translateX(-50%);
    }
}
.animate-marquee {
    animation: marquee 30s linear infinite;
}
.animate-marquee:hover {
    animation-play-state: paused;
}
</style>
