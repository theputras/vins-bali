<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Eye, EyeOff, Shield, User } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import FlashMessage from '@/components/FlashMessage.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

interface UserData {
    id: number;
    name: string;
    email: string;
    role: 'admin' | 'user';
    email_verified_at: string | null;
    two_factor_confirmed_at: string | null;
    created_at: string;
    updated_at: string;
}

const props = defineProps<{
    user: UserData | null;
    isEditing: boolean;
}>();

const form = useForm({
    name: props.user?.name ?? '',
    email: props.user?.email ?? '',
    password: '',
    password_confirmation: '',
    role: props.user?.role ?? 'user',
});

const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

function submit() {
    if (props.isEditing && props.user) {
        form.put(`/admin/users/${props.user.id}`, {
            preserveScroll: true,
        });
    } else {
        form.post('/admin/users', {
            preserveScroll: true,
        });
    }
}

const pageTitle = computed(() => props.isEditing ? `Edit: ${props.user?.name}` : 'Tambah User Baru');

function formatDateTime(date: string) {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}
</script>

<template>
    <Head :title="pageTitle" />
    <FlashMessage />

    <div class="flex flex-col gap-6 p-4">
        <!-- Header -->
        <div>
            <Link href="/admin/users" class="mb-3 inline-flex items-center gap-1.5 text-sm text-muted-foreground transition-colors hover:text-foreground">
                <ArrowLeft class="size-4" />
                Kembali
            </Link>
            <h1 class="text-2xl font-bold tracking-tight text-foreground">{{ pageTitle }}</h1>
        </div>

        <form @submit.prevent="submit" class="grid gap-6 lg:grid-cols-3">
            <!-- Left Column: User Info -->
            <div class="space-y-6 lg:col-span-2">
                <!-- Basic Info -->
                <Card>
                    <CardHeader>
                        <CardTitle class="text-base">Informasi Akun</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <!-- Name -->
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Nama Lengkap *</label>
                            <input
                                v-model="form.name"
                                type="text"
                                placeholder="Masukkan nama lengkap"
                                class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                                :class="{ 'border-destructive': form.errors.name }"
                            />
                            <p v-if="form.errors.name" class="mt-1 text-xs text-destructive">{{ form.errors.name }}</p>
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Email *</label>
                            <input
                                v-model="form.email"
                                type="email"
                                placeholder="contoh@email.com"
                                class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                                :class="{ 'border-destructive': form.errors.email }"
                            />
                            <p v-if="form.errors.email" class="mt-1 text-xs text-destructive">{{ form.errors.email }}</p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Password -->
                <Card>
                    <CardHeader>
                        <CardTitle class="text-base">
                            Password
                            <span v-if="isEditing" class="text-xs font-normal text-muted-foreground ml-2">
                                (kosongkan jika tidak ingin mengubah)
                            </span>
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">
                                {{ isEditing ? 'Password Baru' : 'Password *' }}
                            </label>
                            <div class="relative">
                                <input
                                    v-model="form.password"
                                    :type="showPassword ? 'text' : 'password'"
                                    placeholder="Minimal 8 karakter"
                                    class="h-9 w-full rounded-md border border-input bg-background px-3 pr-10 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                                    :class="{ 'border-destructive': form.errors.password }"
                                />
                                <button
                                    type="button"
                                    class="absolute top-1/2 right-3 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                                    @click="showPassword = !showPassword"
                                >
                                    <EyeOff v-if="showPassword" class="size-4" />
                                    <Eye v-else class="size-4" />
                                </button>
                            </div>
                            <p v-if="form.errors.password" class="mt-1 text-xs text-destructive">{{ form.errors.password }}</p>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Konfirmasi Password</label>
                            <div class="relative">
                                <input
                                    v-model="form.password_confirmation"
                                    :type="showPasswordConfirmation ? 'text' : 'password'"
                                    placeholder="Ketik ulang password"
                                    class="h-9 w-full rounded-md border border-input bg-background px-3 pr-10 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                                />
                                <button
                                    type="button"
                                    class="absolute top-1/2 right-3 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                                    @click="showPasswordConfirmation = !showPasswordConfirmation"
                                >
                                    <EyeOff v-if="showPasswordConfirmation" class="size-4" />
                                    <Eye v-else class="size-4" />
                                </button>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- User Detail (edit only) -->
                <Card v-if="isEditing && user">
                    <CardHeader>
                        <CardTitle class="text-base">Detail Akun</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="rounded-lg border border-border/50 bg-muted/20 p-3">
                                <p class="text-[10px] uppercase tracking-wider text-muted-foreground">Email Verifikasi</p>
                                <div class="mt-1 flex items-center gap-2">
                                    <Badge :variant="user.email_verified_at ? 'default' : 'destructive'" class="text-[10px]">
                                        {{ user.email_verified_at ? 'Terverifikasi' : 'Belum Verifikasi' }}
                                    </Badge>
                                    <span v-if="user.email_verified_at" class="text-xs text-muted-foreground">
                                        {{ formatDateTime(user.email_verified_at) }}
                                    </span>
                                </div>
                            </div>

                            <div class="rounded-lg border border-border/50 bg-muted/20 p-3">
                                <p class="text-[10px] uppercase tracking-wider text-muted-foreground">Two-Factor Auth</p>
                                <div class="mt-1">
                                    <Badge :variant="user.two_factor_confirmed_at ? 'default' : 'secondary'" class="text-[10px]">
                                        {{ user.two_factor_confirmed_at ? 'Aktif' : 'Nonaktif' }}
                                    </Badge>
                                </div>
                            </div>

                            <div class="rounded-lg border border-border/50 bg-muted/20 p-3">
                                <p class="text-[10px] uppercase tracking-wider text-muted-foreground">Dibuat</p>
                                <p class="mt-1 text-sm font-medium text-foreground">{{ formatDateTime(user.created_at) }}</p>
                            </div>

                            <div class="rounded-lg border border-border/50 bg-muted/20 p-3">
                                <p class="text-[10px] uppercase tracking-wider text-muted-foreground">Terakhir Diperbarui</p>
                                <p class="mt-1 text-sm font-medium text-foreground">{{ formatDateTime(user.updated_at) }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Right Column: Role & Submit -->
            <div class="space-y-6">
                <!-- Role -->
                <Card>
                    <CardHeader>
                        <CardTitle class="text-base">Role & Hak Akses</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-3">
                        <label
                            class="flex items-center gap-3 rounded-lg border-2 p-4 cursor-pointer transition hover:bg-muted/30"
                            :class="[form.role === 'admin' ? 'border-primary bg-primary/5' : 'border-border']"
                        >
                            <input
                                v-model="form.role"
                                type="radio"
                                value="admin"
                                class="size-4 accent-primary"
                            />
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <Shield class="size-4 text-amber-600 dark:text-amber-400" />
                                    <p class="text-sm font-semibold">Admin</p>
                                </div>
                                <p class="mt-0.5 text-xs text-muted-foreground">
                                    Akses penuh: kelola mobil, user, dan semua fitur admin
                                </p>
                            </div>
                        </label>

                        <label
                            class="flex items-center gap-3 rounded-lg border-2 p-4 cursor-pointer transition hover:bg-muted/30"
                            :class="[form.role === 'user' ? 'border-primary bg-primary/5' : 'border-border']"
                        >
                            <input
                                v-model="form.role"
                                type="radio"
                                value="user"
                                class="size-4 accent-primary"
                            />
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <User class="size-4 text-blue-600 dark:text-blue-400" />
                                    <p class="text-sm font-semibold">User</p>
                                </div>
                                <p class="mt-0.5 text-xs text-muted-foreground">
                                    Akses terbatas: hanya bisa melihat profil sendiri
                                </p>
                            </div>
                        </label>

                        <p v-if="form.errors.role" class="text-xs text-destructive">{{ form.errors.role }}</p>
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
                            {{ form.processing ? 'Menyimpan...' : isEditing ? 'Simpan Perubahan' : 'Tambah User' }}
                        </Button>
                        <Link href="/admin/users" class="block">
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
