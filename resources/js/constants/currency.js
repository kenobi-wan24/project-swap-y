// Location-based display currency. Single source of truth for every price
// or value rendered in the UI — components must not hardcode ₱ / $ / P.
//
// Detection order:
//   1. country cached from a previous geolocation fix (localStorage)
//   2. the browser locale's region (e.g. "en-PH" → PH, "en-US" → US)
//   3. PH — Swapy's home market
//
// Browse pages refine the guess by passing the `country_code` from their
// existing Nominatim reverse-geocode response to setCurrencyFromCountry().
import { ref } from 'vue'

const EURO_COUNTRIES = ['DE', 'FR', 'ES', 'IT', 'NL', 'PT', 'IE', 'BE', 'AT', 'FI', 'GR', 'SK', 'SI', 'LV', 'LT', 'EE', 'LU', 'CY', 'MT', 'HR']

const CURRENCIES = {
    PH: { code: 'PHP', symbol: '₱' },
    US: { code: 'USD', symbol: '$' },
    GB: { code: 'GBP', symbol: '£' },
    AU: { code: 'AUD', symbol: 'A$' },
    CA: { code: 'CAD', symbol: 'C$' },
    NZ: { code: 'NZD', symbol: 'NZ$' },
    SG: { code: 'SGD', symbol: 'S$' },
    HK: { code: 'HKD', symbol: 'HK$' },
    JP: { code: 'JPY', symbol: '¥' },
    KR: { code: 'KRW', symbol: '₩' },
    CN: { code: 'CNY', symbol: '¥' },
    TW: { code: 'TWD', symbol: 'NT$' },
    IN: { code: 'INR', symbol: '₹' },
    MY: { code: 'MYR', symbol: 'RM' },
    TH: { code: 'THB', symbol: '฿' },
    ID: { code: 'IDR', symbol: 'Rp' },
    VN: { code: 'VND', symbol: '₫' },
    AE: { code: 'AED', symbol: 'AED ' },
    SA: { code: 'SAR', symbol: 'SAR ' },
}
EURO_COUNTRIES.forEach(cc => { CURRENCIES[cc] = { code: 'EUR', symbol: '€' } })

const DEFAULT_COUNTRY = 'PH'
const STORAGE_KEY = 'swapy-country'

function countryFromLocale() {
    const region = (navigator.language || '').split('-')[1]
    return region ? region.toUpperCase() : null
}

function initialCountry() {
    try {
        const cached = localStorage.getItem(STORAGE_KEY)
        if (cached && CURRENCIES[cached]) return cached
    } catch { /* storage unavailable (private mode) */ }
    const fromLocale = countryFromLocale()
    if (fromLocale && CURRENCIES[fromLocale]) return fromLocale
    return DEFAULT_COUNTRY
}

export const currency = ref(CURRENCIES[initialCountry()])

export function setCurrencyFromCountry(countryCode) {
    const cc = String(countryCode || '').toUpperCase()
    if (!CURRENCIES[cc]) return
    currency.value = CURRENCIES[cc]
    try { localStorage.setItem(STORAGE_KEY, cc) } catch { /* storage unavailable */ }
}

export function formatMoney(value) {
    return currency.value.symbol + Number(value || 0).toLocaleString()
}
