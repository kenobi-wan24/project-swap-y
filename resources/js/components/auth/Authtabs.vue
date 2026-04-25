<script setup>
import { ref, reactive } from 'vue'
import axios from 'axios'

const props = defineProps({
    defaultTab: { type: String, default: 'register' }
})

const activeTab = ref(props.defaultTab)

// ── SIGN UP state ─────────────────────────────────────────────────────────────
const signUp = reactive({
    name: '',
    username: '',
    email: '',
    password: '',
    confirmPassword: '',
    agreeTerms: false,
    errors: {},
    loading: false,
})

// Username availability
const usernameStatus = ref('') // '' | 'checking' | 'available' | 'taken'
let usernameTimer = null
function checkUsername() {
    clearTimeout(usernameTimer)
    if (!signUp.username || signUp.username.length < 3) {
        usernameStatus.value = ''
        return
    }
    usernameStatus.value = 'checking'
    usernameTimer = setTimeout(async () => {
    try {
        const res = await axios.get('/api/check-username', {
            params: { username: signUp.username }
        })
        usernameStatus.value = res.data.available ? 'available' : 'taken'
    } catch {
        usernameStatus.value = ''
    }
}, 500)
}

async function handleSignUp() {
    signUp.errors = {}
    if (!signUp.name)             signUp.errors.name = 'Full name is required.'
    if (!signUp.username)         signUp.errors.username = 'Username is required.'
    if (!signUp.email)            signUp.errors.email = 'Email is required.'
    if (signUp.password.length < 8) signUp.errors.password = 'Password must be at least 8 characters.'
    if (signUp.password !== signUp.confirmPassword) signUp.errors.confirmPassword = 'Passwords do not match.'
    if (!signUp.agreeTerms)       signUp.errors.terms = 'You must agree to the terms.'
    if (Object.keys(signUp.errors).length) return

    signUp.loading = true
    try {
        const response = await axios.post('/register', {
            name:     signUp.name,
            username: signUp.username,
            email:    signUp.email,
            password: signUp.password,
        })
        window.location.href = response.data.redirect || '/onboarding/preferences'
    } catch (err) {
        const errors = err.response?.data?.errors
        if (errors?.name)     signUp.errors.name     = errors.name[0]
        if (errors?.username) signUp.errors.username = errors.username[0]
        if (errors?.email)    signUp.errors.email    = errors.email[0]
        if (errors?.password) signUp.errors.password = errors.password[0]
        signUp.loading = false
    }
}

// ── LOGIN state ───────────────────────────────────────────────────────────────
const login = reactive({
    email: '',
    password: '',
    errors: {},
    loading: false,
})

async function handleLogin() {
    login.errors = {}
    if (!login.email)    login.errors.email = 'Email is required.'
    if (!login.password) login.errors.password = 'Password is required.'
    if (Object.keys(login.errors).length) return

    login.loading = true
    try {
        const response = await axios.post('/login', {
            email:    login.email,
            password: login.password,
        })
        // axios follows the redirect URL from the JSON response
        window.location.href = response.data.redirect || '/onboarding/preferences'
    } catch (err) {
        const errors = err.response?.data?.errors
        if (errors?.email)    login.errors.email    = errors.email[0]
        if (errors?.password) login.errors.password = errors.password[0]
        login.loading = false
    }
}

// ── Password visibility toggles ───────────────────────────────────────────────
const showPassword        = ref(false)
const showConfirmPassword = ref(false)
const showLoginPassword   = ref(false)
</script>

<template>
<div>

    <!-- ══ TABS ══ -->
    <div class="auth-tabs">
        <button
            class="auth-tab-btn"
            :class="{ active: activeTab === 'register' }"
            @click="activeTab = 'register'"
        >Sign Up</button>
        <button
            class="auth-tab-btn"
            :class="{ active: activeTab === 'login' }"
            @click="activeTab = 'login'"
        >Login</button>
    </div>

    <!-- ════════════════════════════════════════
         SIGN UP FORM
    ════════════════════════════════════════ -->
    <Transition name="tab-fade" mode="out-in">
    <div v-if="activeTab === 'register'" key="register">

        <!-- Full Name -->
        <div style="margin-bottom:24px;">
            <label class="field-label">Full Name</label>
            <input
                v-model="signUp.name"
                type="text"
                placeholder="John Doe"
                class="field-input"
                :style="signUp.errors.name ? 'border-color:#ef4444;' : ''"
            >
            <p v-if="signUp.errors.name" style="font-size:0.75rem;color:#ef4444;margin-top:4px;">{{ signUp.errors.name }}</p>
        </div>

        <!-- Username -->
        <div style="margin-bottom:24px;">
            <label class="field-label">Username</label>
            <div style="position:relative;">
                <input
                    v-model="signUp.username"
                    @input="checkUsername"
                    type="text"
                    placeholder="swaps_john24"
                    class="field-input"
                    style="padding-right:100px;"
                    :style="signUp.errors.username ? 'border-color:#ef4444;' : ''"
                >
                <!-- Availability badge -->
                <span v-if="usernameStatus === 'checking'" class="avail-badge avail-chk">Checking...</span>
                <span v-else-if="usernameStatus === 'available'" class="avail-badge avail-ok">✓ Available</span>
                <span v-else-if="usernameStatus === 'taken'" class="avail-badge avail-no">✗ Taken</span>
            </div>
            <p v-if="signUp.errors.username" style="font-size:0.75rem;color:#ef4444;margin-top:4px;">{{ signUp.errors.username }}</p>
        </div>

        <!-- Email -->
        <div style="margin-bottom:24px;">
            <label class="field-label">Email</label>
            <input
                v-model="signUp.email"
                type="email"
                placeholder="j.doe123@example.com"
                class="field-input"
                :style="signUp.errors.email ? 'border-color:#ef4444;' : ''"
            >
            <p v-if="signUp.errors.email" style="font-size:0.75rem;color:#ef4444;margin-top:4px;">{{ signUp.errors.email }}</p>
        </div>

        <!-- Password row -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:18px;">
            <div>
                <label class="field-label">Password</label>
                <div style="position:relative;">
                    <input
                        v-model="signUp.password"
                        :type="showPassword ? 'text' : 'password'"
                        placeholder="••••••••"
                        class="field-input"
                        style="padding-right:40px;"
                        :style="signUp.errors.password ? 'border-color:#ef4444;' : ''"
                    >
                    <button @click="showPassword = !showPassword" type="button"
                        style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;">
                        <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path v-if="!showPassword" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
                <p v-if="signUp.errors.password" style="font-size:0.75rem;color:#ef4444;margin-top:4px;">{{ signUp.errors.password }}</p>
            </div>
            <div>
                <label class="field-label">Confirm</label>
                <div style="position:relative;">
                    <input
                        v-model="signUp.confirmPassword"
                        :type="showConfirmPassword ? 'text' : 'password'"
                        placeholder="••••••••"
                        class="field-input"
                        style="padding-right:40px;"
                        :style="signUp.errors.confirmPassword ? 'border-color:#ef4444;' : ''"
                    >
                    <button @click="showConfirmPassword = !showConfirmPassword" type="button"
                        style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;">
                        <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                <p v-if="signUp.errors.confirmPassword" style="font-size:0.75rem;color:#ef4444;margin-top:4px;">{{ signUp.errors.confirmPassword }}</p>
            </div>
        </div>

        <!-- Terms checkbox -->
        <div style="display:flex;align-items:flex-start;gap:10px;margin-bottom:24px;">
            <input
                v-model="signUp.agreeTerms"
                type="checkbox"
                id="terms-check"
                style="margin-top:3px;width:16px;height:16px;accent-color:#ED730C;flex-shrink:0;cursor:pointer;"
            >
            <label for="terms-check" style="font-size:0.85rem;color:#6b7280;cursor:pointer;line-height:1.5;">
                I agree to the
                <a href="#" style="color:#ED730C;text-decoration:none;font-weight:600;">Terms of Service</a>
                and
                <a href="#" style="color:#ED730C;text-decoration:none;font-weight:600;">Privacy Policy</a>.
            </label>
        </div>
        <p v-if="signUp.errors.terms" style="font-size:0.75rem;color:#ef4444;margin-top:-16px;margin-bottom:16px;">{{ signUp.errors.terms }}</p>

        <!-- Submit -->
        <button class="btn-primary" @click="handleSignUp" :disabled="signUp.loading" style="margin-bottom:28px;">
            <span v-if="!signUp.loading">Create Account &nbsp;→</span>
            <span v-else style="display:inline-flex;align-items:center;gap:8px;">
                <svg style="width:16px;height:16px;animation:spin 1s linear infinite;" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5">
                    <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4"/>
                </svg>
                Creating account...
            </span>
        </button>

        <!-- OR divider — uses .or-divider CSS class from layout -->
        <div class="or-divider" style="margin-bottom:16px;">
            <span>Or join with</span>
        </div>

        <!-- OAuth — uses .oauth-row + .btn-oauth from layout -->
        <div class="oauth-row" style="margin-bottom:28px;">
            <a href="#" class="btn-oauth">
                <svg width="18" height="18" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Google
            </a>
            <a href="#" class="btn-oauth">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="#1877F2">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
                Facebook
            </a>
        </div>

        <p style="text-align:center;font-size:0.875rem;color:#6b7280;">
            Already have an account?
            <button @click="activeTab = 'login'"
                style="color:#ED730C;font-weight:700;background:none;border:none;cursor:pointer;font-family:'DM Sans',sans-serif;font-size:0.875rem;">
                Login here
            </button>
        </p>

    </div>
    </Transition>

    <!-- ════════════════════════════════════════
         LOGIN FORM
    ════════════════════════════════════════ -->
    <Transition name="tab-fade" mode="out-in">
    <div v-if="activeTab === 'login'" key="login">

        <!-- Email — point 11: 24px spacing -->
        <div style="margin-bottom:24px;">
            <label class="field-label">Email</label>
            <input
                v-model="login.email"
                type="email"
                placeholder="j.doe123@example.com"
                class="field-input"
                :style="login.errors.email ? 'border-color:#ef4444;' : ''"
            >
            <p v-if="login.errors.email" style="font-size:0.75rem;color:#ef4444;margin-top:4px;">{{ login.errors.email }}</p>
        </div>

        <!-- Password — point 7: label + forgot on same row -->
        <div style="margin-bottom:28px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:7px;">
                <label class="field-label" style="margin-bottom:0;">Password</label>
                <a href="#" style="font-size:0.72rem;font-weight:700;color:#ED730C;text-decoration:none;letter-spacing:.04em;text-transform:uppercase;">
                    Forgot Password?
                </a>
            </div>
            <div style="position:relative;">
                <input
                    v-model="login.password"
                    :type="showLoginPassword ? 'text' : 'password'"
                    placeholder="••••••••"
                    class="field-input"
                    style="padding-right:44px;"
                    :style="login.errors.password ? 'border-color:#ef4444;' : ''"
                    @keyup.enter="handleLogin"
                >
                <button @click="showLoginPassword = !showLoginPassword" type="button"
                    style="position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;display:flex;align-items:center;">
                    <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>
            <p v-if="login.errors.password" style="font-size:0.75rem;color:#ef4444;margin-top:4px;">{{ login.errors.password }}</p>
        </div>

        <!-- Submit — point 8: pill, arrow -->
        <button class="btn-primary" @click="handleLogin" :disabled="login.loading" style="margin-bottom:28px;">
            <span v-if="!login.loading">Login &nbsp;→</span>
            <span v-else style="display:inline-flex;align-items:center;gap:8px;">
                <svg style="width:16px;height:16px;animation:spin 1s linear infinite;" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5">
                    <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4"/>
                </svg>
                Signing in...
            </span>
        </button>

        <!-- OR divider -->
        <div class="or-divider" style="margin-bottom:16px;">
            <span>Or join with</span>
        </div>

        <!-- OAuth -->
        <div class="oauth-row" style="margin-bottom:28px;">
            <a href="#" class="btn-oauth">
                <svg width="18" height="18" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Google
            </a>
            <a href="#" class="btn-oauth">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="#1877F2">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
                Facebook
            </a>
        </div>

        <p style="text-align:center;font-size:0.875rem;color:#6b7280;">
            Don't have an account?
            <button @click="activeTab = 'register'"
                style="color:#ED730C;font-weight:700;background:none;border:none;cursor:pointer;font-family:'DM Sans',sans-serif;font-size:0.875rem;">
                Sign up here
            </button>
        </p>

    </div>
    </Transition>

</div>
</template>

<style scoped>
@keyframes spin { to { transform: rotate(360deg); } }

.tab-fade-enter-active,
.tab-fade-leave-active {
    transition: opacity 0.2s ease, transform 0.2s ease;
}
.tab-fade-enter-from {
    opacity: 0;
    transform: translateY(8px);
}
.tab-fade-leave-to {
    opacity: 0;
    transform: translateY(-8px);
}
</style>