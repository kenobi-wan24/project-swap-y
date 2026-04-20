import './bootstrap'
import { createApp } from 'vue'
import axios from 'axios'

import GuestNavbar    from './components/global/GuestNavbar.vue'
import AuthTabs       from './components/auth/Authtabs.vue'

import BrowsePage     from './components/global/BrowsePage.vue'
import GarageSalePage from './components/global/GarageSalePage.vue'
import ServicesPage from './components/global/ServicesPage.vue'
import ItemDetailPage from './components/global/ItemDetailPage.vue'
import HowItWorksPage from './components/global/HowItWorks.vue'

import OnboardingFlow from './components/onboarding/OnboardingFlow.vue'
import DashboardPage  from './components/global/DashboardPage.vue'

// Axios global config
axios.defaults.baseURL = '/'
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
const token = document.querySelector('meta[name="csrf-token"]')
if (token) axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content
window.axios = axios

// Navbar
if (document.getElementById('navbar-app')) {
    createApp(GuestNavbar).mount('#navbar-app')
}

// Auth
const authEl = document.getElementById('auth-app')
if (authEl) {
    createApp(AuthTabs, {
        defaultTab: authEl.dataset.defaultTab || 'register'
    }).mount('#auth-app')
}

// Browse
if (document.getElementById('browse-app')) {
    createApp(BrowsePage).mount('#browse-app')
}

// Garage Sale
if (document.getElementById('garage-sale-app')) {
    createApp(GarageSalePage).mount('#garage-sale-app')
}

// Item Detail
if (document.getElementById('item-detail-app')) {
    createApp(ItemDetailPage).mount('#item-detail-app')
}

// Services
if (document.getElementById('services-app')) {
    createApp(ServicesPage, {
        services: JSON.parse(document.getElementById('services-app')?.dataset.services || '[]'),
    }).mount('#services-app')
}

// How it works
if (document.getElementById('how-it-works-app')) {
    const el = document.getElementById('how-it-works-app')
    createApp(HowItWorksPage, {
        guides:   JSON.parse(el.dataset.guides   || '[]'),
        featured: JSON.parse(el.dataset.featured || '{}'),
    }).mount('#how-it-works-app')
}

// Onboarding Flow
if (document.getElementById('onboarding-app')) {
    createApp(OnboardingFlow, {
        user: JSON.parse(document.getElementById('onboarding-app')?.dataset.user || '{}'),
    }).mount('#onboarding-app')
}

// Dashboard
if (document.getElementById('dashboard-app')) {
    createApp(DashboardPage).mount('#dashboard-app')
}