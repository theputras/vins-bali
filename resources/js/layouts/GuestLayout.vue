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
const isChangingLang = ref(false); // Prevent multiple rapid changes
const currentYear = ref(new Date().getFullYear());

function handleScroll() {
    scrolled.value = window.scrollY > 20;
}

// Detect language from cookies or localStorage
function detectLanguage() {
    try {
        console.log('[Lang] Detecting language...');
        
        // Check localStorage first (most reliable)
        const storedLang = localStorage.getItem('vins_language');
        if (storedLang) {
            currentLang.value = storedLang;
            console.log('[Lang] From localStorage:', storedLang);
            return;
        }
        
        // Fallback to cookie detection
        const cookies = document.cookie;
        console.log('[Lang] Cookies:', cookies.substring(0, 100));
        
        if (cookies.includes('googtrans=/auto/en') || cookies.includes('googtrans=/id/en')) {
            currentLang.value = 'EN';
            console.log('[Lang] From cookies: EN');
        } else if (cookies.includes('googtrans=/auto/id') || cookies.includes('googtrans=/id/id')) {
            currentLang.value = 'ID';
            console.log('[Lang] From cookies: ID');
        } else {
            currentLang.value = 'ID';
            console.log('[Lang] Using default: ID');
        }
    } catch (error) {
        console.error('[Lang] Error detecting:', error);
        currentLang.value = 'ID';
    }
}

function changeLanguage() {
    if (isChangingLang.value) {
        console.log('[Lang] Change already in progress, skipping...');
        return;
    }
    
    console.log('[Lang] changeLanguage called, currentLang:', currentLang.value);
    isChangingLang.value = true;
    
    try {
        // Determine target language (opposite of current)
        const targetLang = currentLang.value === 'ID' ? 'EN' : 'ID';
        const targetLangLower = targetLang.toLowerCase();
        console.log('[Lang] Switching to:', targetLang);

        // Store in localStorage
        localStorage.setItem('vins_language', targetLang);
        console.log('[Lang] Stored in localStorage:', targetLang);
        
        // Set GTranslate cookies with proper format
        const cookieValue = `/auto/${targetLangLower}`;
        const domain = window.location.hostname;
        const expires = 'expires=' + new Date(Date.now() + 365 * 24 * 60 * 60 * 1000).toUTCString();
        
        // Set cookies with different scopes and long expiry
        document.cookie = `googtrans=${cookieValue}; path=/; ${expires}; SameSite=Lax`;
        document.cookie = `googtrans=${cookieValue}; path=/; domain=${domain}; ${expires}; SameSite=Lax`;
        document.cookie = `googtrans=${cookieValue}; path=/; domain=.${domain}; ${expires}; SameSite=Lax`;
        
        console.log('[Lang] Cookies set, reloading in 500ms...');
        
        // Small delay to ensure cookies are saved
        setTimeout(() => {
            window.location.reload();
        }, 500);
    } catch (error) {
        console.error('[Lang] Error:', error);
        isChangingLang.value = false;
        window.location.reload();
    }
}

onMounted(() => {
    console.log('GuestLayout mounted');
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
</script>

<template>
    <div class="min-h-screen bg-background text-foreground">
        <!-- Navbar -->
        <header
            class="fixed top-0 right-0 left-0 z-50 transition-all duration-300"
            :class="[
                scrolled
                    ? 'glass border-b border-border/50 shadow-sm'
                    : 'bg-transparent',
            ]"
        >
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <!-- Logo -->
                <Link href="/" class="flex items-center gap-2.5">
                    <img src="/images/logo.png" alt="VINS BALI" class="h-9 w-auto rounded-md" />
                </Link>

                <!-- Desktop Nav -->
                <nav class="hidden items-center gap-1 md:flex">
                    <Link
                        v-for="link in navLinks"
                        :key="link.name"
                        :href="link.href"
                        class="rounded-md px-3.5 py-2 text-sm font-medium text-muted-foreground transition-colors hover:bg-accent hover:text-foreground"
                    >
                        {{ link.name }}
                    </Link>
                </nav>

                <!-- Desktop Actions -->
                <div class="hidden items-center gap-3 md:flex">
                    <!-- Currency Dropdown -->
                    <select
                        v-model="currentCurrency"
                        class="h-9 cursor-pointer rounded-md border border-input bg-background px-3 py-1 text-sm font-medium shadow-sm transition-colors hover:bg-accent hover:text-accent-foreground focus:outline-none focus:ring-1 focus:ring-ring dark:bg-input/30 dark:hover:bg-input/50"
                    >
                        <option value="IDR">IDR</option>
                        <option value="USD">USD</option>
                    </select>

                    <!-- Language Dropdown -->
                    <select
                        :value="currentLang"
                        @change="changeLanguage"
                        class="h-9 cursor-pointer rounded-md border border-input bg-background px-3 py-1 text-sm font-medium shadow-sm transition-colors hover:bg-accent hover:text-accent-foreground focus:outline-none focus:ring-1 focus:ring-ring dark:bg-input/30 dark:hover:bg-input/50"
                    >
                        <option value="ID">ID</option>
                        <option value="EN">EN</option>
                    </select>
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex items-center gap-2 md:hidden">
                    <!-- Mobile Dropdowns -->
                    <select
                        v-model="currentCurrency"
                        class="h-9 rounded-md border border-input bg-background px-2.5 py-1 text-xs font-semibold focus:outline-none"
                    >
                        <option value="IDR">IDR</option>
                        <option value="USD">USD</option>
                    </select>

                    <select
                        :value="currentLang"
                        @change="changeLanguage"
                        class="h-9 rounded-md border border-input bg-background px-2.5 py-1 text-xs font-semibold focus:outline-none"
                    >
                        <option value="ID">ID</option>
                        <option value="EN">EN</option>
                    </select>

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
                <div v-if="mobileMenuOpen" class="glass border-b border-border/50 md:hidden">
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
                            <!-- Login section explicitly removed from mobile menu per request -->
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
