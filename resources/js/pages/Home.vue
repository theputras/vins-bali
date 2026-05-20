<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ArrowRight, CheckCircle2, ChevronRight, Star, Quote, MessageSquare, HelpCircle, ChevronDown } from 'lucide-vue-next';
import { computed, defineAsyncComponent, onMounted, onUnmounted, ref, shallowRef } from 'vue';
import CarCard from '@/components/CarCard.vue';
import FlashMessage from '@/components/FlashMessage.vue';
import { Button } from '@/components/ui/button';
import { useScrollReveal } from '@/composables/useScrollReveal';
import type { Car } from '@/types';

useScrollReveal();

const props = defineProps<{
    featuredCars: Car[];
    heroImages: string[];
    categories: string[];
}>();

const page = usePage();
const settings = computed(() => page.props.global_settings as Record<string, any>);

const currentImageIndex = ref(0);
let slideInterval: number;

const isMobile = ref(false);
function checkMobile() {
    isMobile.value = typeof window !== 'undefined' && window.innerWidth < 768;
}

onMounted(() => {
    if (props.heroImages && props.heroImages.length > 0) {
        slideInterval = window.setInterval(() => {
            currentImageIndex.value = (currentImageIndex.value + 1) % props.heroImages.length;
        }, 5000);
    }
    checkMobile();
    window.addEventListener('resize', checkMobile);
    startAutoplay();
});

onUnmounted(() => {
    if (slideInterval) clearInterval(slideInterval);
    window.removeEventListener('resize', checkMobile);
    stopAutoplay();
});

// Categories from database
const popularCategories = computed(() => ['Semua', ...props.categories]);
const activeCategory = ref('Semua');

// Filter featured cars by active category if it's set
const filteredCars = computed(() => {
    if (activeCategory.value === 'Semua') return props.featuredCars;
    return props.featuredCars.filter(c => c.category?.name === activeCategory.value);
});

// Dynamically resolve icon component by name — avoids barrel-importing all ~1500 icons
const iconCache = new Map<string, ReturnType<typeof defineAsyncComponent>>();
function getIconComponent(name: string) {
    if (!name) return Star;
    if (iconCache.has(name)) return iconCache.get(name)!;

    const asyncIcon = defineAsyncComponent({
        loader: () =>
            import('lucide-vue-next').then((mod) => {
                // @ts-ignore — dynamic lookup by icon name
                return mod[name] || mod['Star'];
            }),
    });
    iconCache.set(name, asyncIcon);
    return asyncIcon;
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

// Custom FAQ, Testimonial & Founder's Note
const homeFaqs = computed(() => {
    return Array.isArray(settings.value.home_faqs) ? settings.value.home_faqs : [];
});

const homeTestimonials = computed(() => {
    return Array.isArray(settings.value.home_testimonials) ? settings.value.home_testimonials : [];
});

const founderName = computed(() => settings.value.founder_name || '');
const founderTitle = computed(() => settings.value.founder_title || '');
const founderText = computed(() => settings.value.founder_text || '');
const founderPhotoPath = computed(() => settings.value.founder_photo_path || '');
const displayFounderPhoto = computed(() => founderPhotoPath.value ? `/storage/${founderPhotoPath.value}` : '/images/founder.png');

const activeFaqIndex = ref<number | null>(null);
function toggleFaq(index: number) {
    activeFaqIndex.value = activeFaqIndex.value === index ? null : index;
}

// Testimonials Autoplay Slider
const activeTestiIndex = ref(0);
const testiContainer = ref<HTMLElement | null>(null);
let autoplayTimer: number | null = null;

const totalSlides = computed(() => {
    if (isMobile.value) {
        return homeTestimonials.value.length;
    } else {
        return Math.max(1, homeTestimonials.value.length - 2);
    }
});

function startAutoplay() {
    stopAutoplay();
    autoplayTimer = window.setInterval(() => {
        if (!homeTestimonials.value.length) return;
        activeTestiIndex.value = (activeTestiIndex.value + 1) % totalSlides.value;
        scrollToActiveIndex();
    }, 4000);
}

function stopAutoplay() {
    if (autoplayTimer) {
        clearInterval(autoplayTimer);
        autoplayTimer = null;
    }
}

function scrollToActiveIndex() {
    if (!testiContainer.value) return;
    const container = testiContainer.value;
    const card = container.children[activeTestiIndex.value] as HTMLElement;
    if (card) {
        container.scrollTo({
            left: card.offsetLeft - container.offsetLeft,
            behavior: 'smooth'
        });
    }
}

function onScroll(e: Event) {
    if (!testiContainer.value) return;
    const container = testiContainer.value;
    const scrollLeft = container.scrollLeft;
    const children = Array.from(container.children) as HTMLElement[];
    if (children.length === 0) return;
    
    let closestIndex = 0;
    let minDistance = Infinity;
    
    children.forEach((child, index) => {
        const distance = Math.abs(child.offsetLeft - container.offsetLeft - scrollLeft);
        if (distance < minDistance) {
            minDistance = distance;
            closestIndex = index;
        }
    });
    
    activeTestiIndex.value = Math.min(closestIndex, totalSlides.value - 1);
}

function handleMouseEnter() {
    stopAutoplay();
}

function handleMouseLeave() {
    startAutoplay();
}
</script>

<template>
    <Head>
        <title>{{ $page.props.global_settings?.seo_settings?.home_title || 'Premium Car Rental in Bali' }}</title>
        <meta head-key="description" name="description" :content="$page.props.global_settings?.seo_settings?.home_description || heroSubtitle" />
        <meta head-key="keywords" name="keywords" :content="$page.props.global_settings?.seo_settings?.home_keywords || $page.props.global_settings?.seo_settings?.default_keywords || 'rental mobil mewah bali, sewa alphard bali, rental porsche bali'" />
        
        <meta head-key="og:title" property="og:title" :content="($page.props.global_settings?.seo_settings?.home_title || 'Premium Car Rental in Bali') + ' - VINS BALI'" />
        <meta head-key="og:description" property="og:description" :content="$page.props.global_settings?.seo_settings?.home_description || heroSubtitle" />
        <meta head-key="og:image" property="og:image" :content="$page.props.global_settings?.seo_settings?.default_og_image_path ? '/storage/' + $page.props.global_settings.seo_settings.default_og_image_path : '/images/logo.png'" />
        <meta head-key="og:type" property="og:type" content="website" />
        <meta head-key="og:site_name" property="og:site_name" :content="$page.props.global_settings?.seo_settings?.site_name || 'VINS BALI'" />
        <meta head-key="og:locale" property="og:locale" :content="$page.props.global_settings?.seo_settings?.og_locale || 'id_ID'" />
        
        <meta head-key="twitter:card" name="twitter:card" content="summary_large_image" />
        <meta head-key="twitter:site" name="twitter:site" :content="$page.props.global_settings?.seo_settings?.twitter_handle" />
        <meta head-key="twitter:title" name="twitter:title" :content="($page.props.global_settings?.seo_settings?.home_title || 'Premium Car Rental in Bali') + ' - VINS BALI'" />
        <meta head-key="twitter:description" name="twitter:description" :content="$page.props.global_settings?.seo_settings?.home_description || heroSubtitle" />
        <meta head-key="twitter:image" name="twitter:image" :content="$page.props.global_settings?.seo_settings?.default_og_image_path ? '/storage/' + $page.props.global_settings.seo_settings.default_og_image_path : '/images/logo.png'" />
    </Head>
    <FlashMessage />

    <!-- STAGE 1: Hero Section & USPs -->
    <section class="relative flex min-h-[100vh] flex-col overflow-hidden bg-gradient-to-br from-gray-950 via-gray-900 to-gray-800 pt-24">
        
        <!-- Image Slider Background -->
        <!-- Background Video -->
        <video
            class="absolute inset-0 h-full w-full object-cover object-center opacity-40"
            src="/hero-vids.mp4"
            autoplay
            loop
            muted
            playsinline
            preload="auto"
        ></video>
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
                        Booking Sekarang
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
    <section v-if="brandLogos.length" class="border-y border-border/30 bg-muted/10 py-10 overflow-hidden" data-reveal="fade">
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
            <div class="text-center sm:text-left" data-reveal>
                <h2 class="text-3xl font-bold tracking-tight text-foreground sm:text-4xl">
                    Temukan Mobil Ideal Anda
                </h2>
                <p class="mt-3 text-base text-muted-foreground">
                    Filter berdasarkan gaya berkendara dan tujuan perjalanan Anda.
                </p>
            </div>

            <!-- Categories Filter -->
            <div class="mt-8 flex flex-wrap gap-2 justify-center sm:justify-start" data-reveal data-reveal-delay="200">
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
            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4" data-reveal data-reveal-delay="300">
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
            <div class="text-center" data-reveal>
                <h2 class="text-3xl font-bold tracking-tight text-foreground">Layanan Premium Eksklusif</h2>
                <p class="mx-auto mt-4 max-w-2xl text-muted-foreground">Lebih dari sekadar sewa mobil harian, kami menyediakan solusi mobilitas untuk setiap kebutuhan VIP Anda.</p>
            </div>

            <div class="mt-16 grid gap-8 md:grid-cols-3" data-reveal data-reveal-delay="200">
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

    <!-- Testimonials Section -->
    <section v-if="homeTestimonials.length > 0" class="border-t border-border/50 bg-background py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center" data-reveal>
                <div class="inline-flex items-center gap-1.5 rounded-full bg-primary/10 px-3 py-1 text-xs font-semibold text-primary mb-4">
                    <MessageSquare class="size-3.5" /> Ulasan Pelanggan
                </div>
                <h2 class="text-3xl font-bold tracking-tight text-foreground sm:text-4xl">Apa Kata Mereka Tentang VINS BALI?</h2>
                <p class="mx-auto mt-4 max-w-2xl text-muted-foreground">Komitmen kami adalah kepuasan penuh Anda. Berikut adalah pengalaman tulus dari pelanggan setia kami.</p>
            </div>

            <!-- Carousel Wrapper -->
            <div class="relative mt-16" data-reveal data-reveal-delay="200" @mouseenter="handleMouseEnter" @mouseleave="handleMouseLeave">
                <!-- Carousel Container -->
                <div 
                    ref="testiContainer"
                    @scroll="onScroll"
                    class="flex gap-6 overflow-x-auto snap-x snap-mandatory scrollbar-none pb-4"
                >
                    <div 
                        v-for="testi in homeTestimonials" 
                        :key="testi.name" 
                        class="w-full md:w-[calc(33.333%-1rem)] shrink-0 snap-start relative rounded-2xl border border-border bg-card p-8 shadow-sm transition-all duration-300 hover:shadow-md flex flex-col justify-between overflow-hidden"
                    >
                        <div class="absolute -right-4 -top-4 opacity-5 text-muted-foreground pointer-events-none">
                            <Quote class="size-24" />
                        </div>
                        <div>
                            <div class="flex gap-1 text-amber-500 mb-5">
                                <Star v-for="i in testi.rating" :key="i" class="size-4 fill-current" />
                                <Star v-for="i in (5 - testi.rating)" :key="i" class="size-4 opacity-20" />
                            </div>
                            <p class="text-sm leading-relaxed text-muted-foreground italic mb-6">
                                "{{ testi.review }}"
                            </p>
                        </div>
                        <div class="border-t border-border/60 pt-4 flex items-center justify-between">
                            <div>
                                <h4 class="font-bold text-foreground text-sm">{{ testi.name }}</h4>
                                <span class="text-[11px] text-muted-foreground font-medium">Verified Customer</span>
                            </div>
                            <div class="size-8 rounded-full bg-primary/5 flex items-center justify-center text-primary font-bold text-xs border border-primary/15">
                                {{ testi.name.charAt(0) }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Dot Indicators -->
                <div v-if="totalSlides > 1" class="mt-8 flex justify-center gap-2">
                    <button 
                        v-for="index in totalSlides" 
                        :key="index"
                        @click="activeTestiIndex = index - 1; scrollToActiveIndex()"
                        class="size-2 rounded-full transition-all duration-300"
                        :class="activeTestiIndex === (index - 1) ? 'bg-primary w-6' : 'bg-muted-foreground/30 hover:bg-muted-foreground/50'"
                        aria-label="Go to slide"
                    />
                </div>
            </div>
        </div>
    </section>

    <!-- Founder's Note Section -->
    <section v-if="founderName && founderText" class="relative overflow-hidden bg-muted/40 py-24 border-t border-b border-border/50">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-primary/5 via-transparent to-transparent pointer-events-none"></div>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 relative">
            <div class="grid gap-12 lg:grid-cols-12 items-center">
                <!-- Photo Column -->
                <div class="lg:col-span-4 flex flex-col items-center text-center lg:text-left" data-reveal>
                    <div class="relative group">
                        <div class="absolute -inset-1 rounded-3xl bg-gradient-to-tr from-primary to-amber-500 opacity-20 blur transition duration-500 group-hover:opacity-30"></div>
                        <div class="relative size-64 overflow-hidden rounded-3xl border-2 border-primary/20 bg-card shadow-xl">
                            <img :src="displayFounderPhoto" :alt="founderName" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" />
                        </div>
                    </div>
                    <h3 class="mt-6 text-xl font-bold text-foreground">{{ founderName }}</h3>
                    <p class="text-xs font-semibold text-primary tracking-wider uppercase mt-1">{{ founderTitle }}</p>
                </div>
                <!-- Text Column -->
                <div class="lg:col-span-8 space-y-6 lg:border-l lg:border-border/60 lg:pl-12" data-reveal data-reveal-delay="200">
                    <div class="inline-flex items-center gap-1 text-primary">
                        <Quote class="size-8 fill-current opacity-20" />
                    </div>
                    <h4 class="text-2xl font-bold text-foreground">Pesan Pribadi dari Pendiri Kami</h4>
                    <p class="text-base leading-relaxed text-muted-foreground italic font-light whitespace-pre-line">
                        "{{ founderText }}"
                    </p>
                    <div class="pt-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <span class="text-xs text-muted-foreground italic">Menghubungkan Anda dengan Kenyamanan Terbaik di Bali.</span>
                        <div class="font-signature text-4xl text-primary select-none font-medium -rotate-2 tracking-wider">
                            {{ founderName }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- STAGE 4: FAQ & Fast Rules -->
    <section class="py-24 bg-background border-b border-border/50">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-12 items-start" :class="homeFaqs.length > 0 ? 'lg:grid-cols-12' : 'max-w-4xl mx-auto'">
                
                <!-- Left Column: Kriteria Penyewaan Cepat -->
                <div :class="homeFaqs.length > 0 ? 'lg:col-span-5' : 'w-full'" class="space-y-6" data-reveal>
                    <div class="rounded-3xl border border-border bg-card p-8 shadow-sm">
                        <h2 class="text-2xl font-bold tracking-tight text-foreground flex items-center gap-2">
                            <CheckCircle2 class="size-6 text-primary" />
                            Syarat & Ketentuan Sewa
                        </h2>
                        <p class="mt-2 text-sm text-muted-foreground">Persyaratan dokumen sederhana untuk kelancaran penyewaan Anda.</p>
                        
                        <div class="mt-8 space-y-6">
                            <div>
                                <h4 class="font-bold text-sm text-foreground flex items-center gap-2">
                                    <span class="size-2 rounded-full bg-primary"></span> Turis / Wisatawan
                                </h4>
                                <ul class="mt-3 space-y-2 text-xs text-muted-foreground pl-4">
                                    <li v-for="req in rentalRequirements.tourist" :key="req">• {{ req }}</li>
                                </ul>
                            </div>
                            <div class="border-t border-border/60 pt-6">
                                <h4 class="font-bold text-sm text-foreground flex items-center gap-2">
                                    <span class="size-2 rounded-full bg-primary"></span> Warga Lokal / Resident
                                </h4>
                                <ul class="mt-3 space-y-2 text-xs text-muted-foreground pl-4">
                                    <li v-for="req in rentalRequirements.resident" :key="req">• {{ req }}</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="mt-8 border-t border-border pt-6 text-[11px] leading-relaxed text-muted-foreground">
                            {{ requirementsFooter }}
                        </div>
                    </div>
                </div>

                <!-- Right Column: Interactive FAQ Accordion -->
                <div v-if="homeFaqs.length > 0" class="lg:col-span-7 space-y-6" data-reveal data-reveal-delay="200">
                    <div class="flex items-center gap-2 mb-2">
                        <HelpCircle class="size-6 text-primary" />
                        <h3 class="text-2xl font-bold tracking-tight text-foreground">Tanya Jawab (FAQ)</h3>
                    </div>
                    <p class="text-sm text-muted-foreground mb-6">Punya pertanyaan lain? Berikut adalah beberapa jawaban atas pertanyaan yang sering diajukan pelanggan.</p>
                    
                    <div class="space-y-4">
                        <div 
                            v-for="(faq, index) in homeFaqs" 
                            :key="index" 
                            class="rounded-2xl border border-border bg-card transition-all duration-200 overflow-hidden"
                            :class="{'border-primary/45 shadow-sm': activeFaqIndex === index}"
                        >
                            <button 
                                type="button"
                                @click="toggleFaq(index)"
                                class="w-full flex items-center justify-between p-5 text-left font-semibold text-sm text-foreground focus:outline-none hover:bg-muted/10 transition"
                            >
                                <span>{{ faq.question }}</span>
                                <ChevronDown 
                                    class="size-4 text-muted-foreground transition-transform duration-300 shrink-0 ml-4"
                                    :class="{'rotate-180 text-primary': activeFaqIndex === index}"
                                />
                            </button>
                            
                            <div 
                                class="transition-all duration-300 ease-in-out"
                                :style="{
                                    maxHeight: activeFaqIndex === index ? '400px' : '0px',
                                    opacity: activeFaqIndex === index ? '1' : '0'
                                }"
                            >
                                <div class="p-5 pt-0 border-t border-border/40 text-xs leading-relaxed text-muted-foreground whitespace-pre-line">
                                    {{ faq.answer }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- STAGE 5: CTA Booking & Callback -->
    <section class="py-24 bg-gradient-to-b from-background to-muted/30">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-16 lg:grid-cols-2 items-center">
                <div data-reveal>
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

                <div class="rounded-2xl border border-border bg-card p-8 shadow-xl" data-reveal="right" data-reveal-delay="200">
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
@import url('https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap');

.font-signature {
    font-family: 'Great Vibes', cursive;
}

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

.scrollbar-none::-webkit-scrollbar {
    display: none;
}
.scrollbar-none {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
