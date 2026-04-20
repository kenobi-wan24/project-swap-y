<script setup>
import { ref } from 'vue'

const props = defineProps({
    activeRoute: { type: String, default: '' },
    auth: { type: Boolean, default: false },
})

const mobileOpen = ref(false)

const links = [
    { label: 'Home',         route: 'home',        href: '/' },
    { label: 'Browse',       route: 'browse',      href: '/browse' },
    { label: 'Garage Sale',  route: 'garage-sale', href: '/garage-sale' },
    { label: 'Services',     route: 'services',    href: '/services' },
    { label: 'How It Works', route: 'how-it-works',href: '/how-it-works' },
]

function isActive(route) {
    return props.activeRoute === route
}
</script>

<template>
    <header class="bg-white border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                <!-- Logo -->
                <a href="/" class="flex items-center gap-2 flex-shrink-0">
                    <img :src="'/images/swapy-logo.png'" alt="SWAPY" class="w-8 h-8 object-contain">
                    <span class="font-bold text-lg text-heading tracking-wide">SWAPY</span>
                </a>

                <!-- Desktop Nav -->
                <nav class="hidden md:flex items-center gap-1">
                    <a
                        v-for="link in links"
                        :key="link.route"
                        :href="link.href"
                        :class="[
                            'px-3 py-1.5 rounded-lg text-sm font-medium transition-colors',
                            isActive(link.route)
                                ? 'text-primary font-semibold'
                                : 'text-gray-500 hover:text-heading hover:bg-gray-50'
                        ]"
                    >
                        {{ link.label }}
                        <!-- Active underline -->
                        <div
                            v-if="isActive(link.route)"
                            class="h-0.5 bg-primary rounded-full mt-0.5"
                        ></div>
                    </a>
                </nav>

                <!-- Desktop Auth -->
                <div class="hidden md:flex items-center gap-3">
                    <a
                        href="/login"
                        class="text-sm font-medium text-gray-600 hover:text-heading transition-colors px-3 py-1.5"
                    >
                        Login
                    </a>
                    <a
                        href="/register"
                        class="text-sm font-semibold text-white bg-primary hover:bg-orange-600 transition-colors px-5 py-2 rounded-full"
                    >
                        Sign Up
                    </a>
                </div>

                <!-- Mobile Hamburger -->
                <button
                    @click="mobileOpen = !mobileOpen"
                    class="md:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-50 transition-colors"
                    aria-label="Toggle menu"
                >
                    <svg v-if="!mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

            </div>
        </div>

        <!-- Mobile Menu -->
        <div v-if="mobileOpen" class="md:hidden border-t border-gray-100 bg-white">
            <div class="px-4 pt-3 pb-4 space-y-1">
                <a
                    v-for="link in links"
                    :key="link.route"
                    :href="link.href"
                    :class="[
                        'block px-3 py-2.5 rounded-lg text-sm font-medium transition-colors',
                        isActive(link.route)
                            ? 'bg-orange-50 text-primary font-semibold'
                            : 'text-gray-600 hover:bg-gray-50'
                    ]"
                >
                    {{ link.label }}
                </a>
                <div class="border-t border-gray-100 pt-3 mt-3 flex gap-3">
                    <a href="/login"
                       class="flex-1 text-center text-sm font-medium border border-gray-200 rounded-full py-2 text-gray-600 hover:bg-gray-50">
                        Login
                    </a>
                    <a href="/register"
                       class="flex-1 text-center text-sm font-semibold bg-primary text-white rounded-full py-2 hover:bg-orange-600">
                        Sign Up
                    </a>
                </div>
            </div>
        </div>

    </header>
</template>