<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Menu, X } from 'lucide-vue-next';
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { useCurrency, loadStoredCurrency } from '@/composables/useCurrency';

const page = usePage();
const auth = computed(() => page.props.auth as { user: { role: string } | null });

const { currentCurrency } = useCurrency();

const mobileMenuOpen = ref(false);
const scrolled = ref(false);
const currentLang = ref('ID');
const isChangingLang = ref(false);
const currentYear = ref(new Date().getFullYear());

function handleScroll() {
    scrolled.value = window.scrollY > 20;
}

// Detect language from localStorage or cookies
function detectLanguage() {
    try {
        // Check localStorage first (most reliable)
        const storedLang = localStorage.getItem('vins_language');
        if (storedLang) {
            currentLang.value = storedLang;
            return;
        }
        
        // Fallback to cookie detection
        const cookies = document.cookie;
        if (cookies.includes('googtrans=/auto/en') || cookies.includes('googtrans=/id/en')) {
            currentLang.value = 'EN';
        } else {
            currentLang.value = 'ID';
        }
    } catch {
        currentLang.value = 'ID';
    }
}

function changeLanguage(event: Event) {
    if (isChangingLang.value) return;
    isChangingLang.value = true;
    
    try {
        const select = event.target as HTMLSelectElement;
        const targetLang = select.value; // 'ID' or 'EN'

        // Store in localStorage
        localStorage.setItem('vins_language', targetLang);
        
        const domain = window.location.hostname;
        const expires = 'expires=' + new Date(Date.now() + 365 * 24 * 60 * 60 * 1000).toUTCString();

        if (targetLang === 'EN') {
            // Translate to English
            const cookieValue = '/auto/en';
            document.cookie = `googtrans=${cookieValue}; path=/; ${expires}; SameSite=Lax`;
            document.cookie = `googtrans=${cookieValue}; path=/; domain=${domain}; ${expires}; SameSite=Lax`;
            document.cookie = `googtrans=${cookieValue}; path=/; domain=.${domain}; ${expires}; SameSite=Lax`;
        } else {
            // Back to original (Indonesian) — clear googtrans cookie
            document.cookie = `googtrans=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT; SameSite=Lax`;
            document.cookie = `googtrans=; path=/; domain=${domain}; expires=Thu, 01 Jan 1970 00:00:00 GMT; SameSite=Lax`;
            document.cookie = `googtrans=; path=/; domain=.${domain}; expires=Thu, 01 Jan 1970 00:00:00 GMT; SameSite=Lax`;
        }
        
        setTimeout(() => {
            window.location.reload();
        }, 300);
    } catch {
        isChangingLang.value = false;
        window.location.reload();
    }
}

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    handleScroll();
    detectLanguage();
    currentYear.value = new Date().getFullYear();
    loadStoredCurrency();
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});

const navLinks = [
    { name: 'Home', href: '/' },
    { name: 'Katalog', href: '/catalog' },
];

function formatPhone(num: string) {
    if (!num) return '';
    // e.g. 6281234567890 → +62 812 3456 7890
    const clean = num.replace(/\D/g, '');
    if (clean.startsWith('62')) {
        const code = clean.slice(0, 2);
        const rest = clean.slice(2);
        return `+${code} ${rest.slice(0, 3)} ${rest.slice(3, 7)} ${rest.slice(7)}`;
    }
    return `+${clean}`;
}
</script>

<template>
    <div class="min-h-screen bg-background text-foreground">
        <!-- Navbar -->
        <header
            class="fixed top-0 right-0 left-0 z-50 transition-all duration-300"
            :class="[
                scrolled
                    ? 'border-b border-border/50 shadow-sm'
                    : 'bg-transparent',
            ]"
            :style="scrolled ? {
                backdropFilter: 'blur(12px) saturate(180%)',
                WebkitBackdropFilter: 'blur(12px) saturate(180%)',
                backgroundColor: 'rgba(10, 10, 10, 0.78)',
            } : {}"
        >
            <!-- Top Bar — Currency & Language (separate row) -->
            
                <div class="mx-auto flex h-8 max-w-7xl items-center justify-end gap-3 px-4 sm:px-6 lg:px-8">
                    <!-- WhatsApp -->
                    <a
                        :href="`https://wa.me/${$page.props.global_settings?.whatsapp_number || ''}`"
                        target="_blank"
                        class="flex items-center gap-1.5 rounded-md px-2 py-1 text-xs font-medium text-green-400 transition-all hover:text-green-300"
                        title="Chat WhatsApp"
                    >
                        <svg class="size-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        <span class="hidden sm:inline">{{ formatPhone($page.props.global_settings?.whatsapp_number) }}</span>
                    </a>

                    <div class="h-4 w-px bg-white/20"></div>

                    <select
                        v-model="currentCurrency"
                        class="h-7 cursor-pointer appearance-none rounded-md border border-white/15 bg-white/10 px-2.5 pr-6 text-xs font-medium text-white/90 transition-all hover:border-white/30 hover:bg-white/15 hover:text-white focus:outline-none"
                        style="background-image: url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2212%22 height=%2212%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22rgba(255,255,255,0.6)%22 stroke-width=%222%22><polyline points=%226 9 12 15 18 9%22/></svg>'); background-repeat: no-repeat; background-position: right 6px center;"
                    >
                        <option value="IDR" class="bg-neutral-900 text-white">IDR</option>
                        <option value="USD" class="bg-neutral-900 text-white">USD</option>
                    </select>

                    <select
                        :value="currentLang"
                        @change="changeLanguage"
                        class="h-7 cursor-pointer appearance-none rounded-md border border-white/15 bg-white/10 px-2.5 pr-6 text-xs font-medium text-white/90 transition-all hover:border-white/30 hover:bg-white/15 hover:text-white focus:outline-none"
                        style="background-image: url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2212%22 height=%2212%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22rgba(255,255,255,0.6)%22 stroke-width=%222%22><polyline points=%226 9 12 15 18 9%22/></svg>'); background-repeat: no-repeat; background-position: right 6px center;"
                    >
                        <option value="ID" class="bg-neutral-900 text-white">ID</option>
                        <option value="EN" class="bg-neutral-900 text-white">EN</option>
                    </select>
                </div>
          

            <!-- Main Bar — Logo left, Nav centered, Hamburger right (mobile) -->
            <div class="relative mx-auto flex h-14 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <!-- Logo -->
                <Link href="/" class="flex items-center gap-2.5">
                    <img src="/images/logo.png" alt="VINS BALI" class="h-9 w-auto rounded-md" />
                </Link>

                <!-- Desktop Nav (centered) -->
                <nav class="absolute left-1/2 hidden -translate-x-1/2 items-center gap-1 md:flex">
                    <Link
                        v-for="link in navLinks"
                        :key="link.name"
                        :href="link.href"
                        class="rounded-md px-3.5 py-2 text-sm font-medium text-muted-foreground transition-colors hover:bg-accent/50 hover:text-foreground"
                    >
                        {{ link.name }}
                    </Link>
                </nav>

                <!-- Spacer for desktop balance -->
                <div class="hidden w-9 md:block"></div>

                <!-- Mobile Hamburger -->
                <div class="flex items-center md:hidden">
                    <Button variant="ghost" size="icon" @click="mobileMenuOpen = !mobileMenuOpen">
                        <X v-if="mobileMenuOpen" class="size-5" />
                        <Menu v-else class="size-5" />
                    </Button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <Transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0 -translate-y-2"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 -translate-y-2"
            >
                <div v-if="mobileMenuOpen" class="border-b border-border/50 md:hidden" style="backdrop-filter: blur(12px) saturate(180%); -webkit-backdrop-filter: blur(12px) saturate(180%); background-color: rgba(10, 10, 10, 0.78);">
                    <div class="space-y-1 px-4 py-3">
                        <Link
                            v-for="link in navLinks"
                            :key="link.name"
                            :href="link.href"
                            class="block rounded-md px-3 py-2.5 text-sm font-medium text-muted-foreground transition-colors hover:bg-accent hover:text-foreground"
                            @click="mobileMenuOpen = false"
                        >
                            {{ link.name }}
                        </Link>
                    </div>
                </div>
            </Transition>
        </header>

        <!-- Main Content -->
        <main>
            <slot />
        </main>

        <!-- Footer -->
        <footer class="border-t border-border bg-muted/30">
            <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                <div class="grid gap-8 md:grid-cols-3">
                    <!-- Brand -->
                    <div>
                        <div class="flex items-center gap-2.5">
                            <img src="/images/logo.png" alt="VINS BALI" class="h-8 w-auto rounded-md" />
                        </div>
                        <p class="mt-3 text-sm leading-relaxed text-muted-foreground">
                            Premium car rental service in Bali. Experience the island with luxury and style.
                        </p>
                    </div>

                    <!-- Quick Links -->
                    <div>
                        <h3 class="mb-3 text-sm font-semibold uppercase tracking-wider text-foreground">Quick Links</h3>
                        <ul class="space-y-2">
                            <li v-for="link in navLinks" :key="link.name">
                                <Link :href="link.href" class="text-sm text-muted-foreground transition-colors hover:text-foreground">
                                    {{ link.name }}
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <!-- Contact -->
                    <div>
                        <h3 class="mb-3 text-sm font-semibold uppercase tracking-wider text-foreground">Contact</h3>
                        <ul class="space-y-2 text-sm text-muted-foreground">
                            <li>{{ $page.props.global_settings?.company_address || 'Bali, Indonesia' }}</li>
                            <li>
                                <a :href="`mailto:${$page.props.global_settings?.company_email || 'info@vinsbali.com'}`" class="transition-colors hover:text-foreground">
                                    {{ $page.props.global_settings?.company_email || 'info@vinsbali.com' }}
                                </a>
                            </li>
                            <li>
                                <a :href="`https://wa.me/${$page.props.global_settings?.whatsapp_number || '6281234567890'}`" target="_blank" class="transition-colors hover:text-foreground">
                                    WhatsApp: +{{ $page.props.global_settings?.whatsapp_number || '6281234567890' }}
                                </a>
                            </li>
                            <li v-if="$page.props.global_settings?.instagram_url" class="mt-2 block">
                                <a :href="$page.props.global_settings.instagram_url" target="_blank" class="transition-colors hover:text-foreground underline underline-offset-4">
                                    Instagram
                                </a>
                            </li>
                            <li v-if="$page.props.global_settings?.facebook_url" class="block">
                                <a :href="$page.props.global_settings.facebook_url" target="_blank" class="transition-colors hover:text-foreground underline underline-offset-4">
                                    Facebook
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="mt-10 border-t border-border pt-6 text-center text-xs text-muted-foreground">
                    &copy; {{ currentYear }} VINS BALI. All rights reserved.
                </div>
            </div>
        </footer>
    </div>
</template>
