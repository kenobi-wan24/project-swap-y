import './bootstrap'
import { createApp } from 'vue'
import axios from 'axios'

import GuestNavbar    from './components/global/GuestNavbar.vue'
import AuthTabs       from './components/auth/Authtabs.vue'

import ItemsPage        from './components/global/ItemsPage.vue'
import ItemsSectionPage from './components/global/ItemsSectionPage.vue'
import SearchResultsPage from './components/global/SearchResultsPage.vue'
import HomesPage      from './components/global/HomesPage.vue'
import HomesSectionPage from './components/global/HomesSectionPage.vue'
import HomeDetailPage from './components/global/HomeDetailPage.vue'
import HomeCreatePage from './components/global/HomeCreatePage.vue'
import GarageSalePage       from './components/global/GarageSalePage.vue'
import GarageSaleSectionPage from './components/global/GarageSaleSectionPage.vue'
import GarageSaleDetailPage from './components/global/GarageSaleDetailPage.vue'
import GarageSaleCreatePage from './components/global/GarageSaleCreatePage.vue'
import ItemCreatePage        from './components/global/ItemCreatePage.vue'
import ServicesPage from './components/global/ServicesPage.vue'
import ServicesSectionPage from './components/global/ServicesSectionPage.vue'
import ServiceCreatePage from './components/global/ServiceCreatePage.vue'
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

// Items
if (document.getElementById('items-app')) {
    createApp(ItemsPage).mount('#items-app')
}

// Items section ("See all" view)
if (document.getElementById('items-section-app')) {
    createApp(ItemsSectionPage).mount('#items-section-app')
}

// Universal search results
if (document.getElementById('search-app')) {
    createApp(SearchResultsPage).mount('#search-app')
}

// Homes
if (document.getElementById('homes-app')) {
    createApp(HomesPage).mount('#homes-app')
}

// Homes section ("See all" view)
if (document.getElementById('homes-section-app')) {
    createApp(HomesSectionPage).mount('#homes-section-app')
}

// Home Detail
if (document.getElementById('home-detail-app')) {
    createApp(HomeDetailPage).mount('#home-detail-app')
}

// Home Create
if (document.getElementById('home-create-app')) {
    createApp(HomeCreatePage).mount('#home-create-app')
}

// Garage Sale
if (document.getElementById('garage-sale-app')) {
    createApp(GarageSalePage).mount('#garage-sale-app')
}

// Garage Sale section ("See all" view)
if (document.getElementById('garage-sale-section-app')) {
    createApp(GarageSaleSectionPage).mount('#garage-sale-section-app')
}

// Garage Sale Detail
if (document.getElementById('garage-sale-detail-app')) {
    createApp(GarageSaleDetailPage).mount('#garage-sale-detail-app')
}

// Garage Sale Create
if (document.getElementById('garage-sale-create-app')) {
    createApp(GarageSaleCreatePage).mount('#garage-sale-create-app')
}

// Item Create
if (document.getElementById('item-create-app')) {
    createApp(ItemCreatePage).mount('#item-create-app')
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

// Services section ("See all" view)
if (document.getElementById('services-section-app')) {
    createApp(ServicesSectionPage).mount('#services-section-app')
}

// Service Create
if (document.getElementById('service-create-app')) {
    createApp(ServiceCreatePage).mount('#service-create-app')
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