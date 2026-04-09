import { ref, watch, onMounted } from 'vue';

type Currency = 'IDR' | 'USD';

// Global state so it persists across layout / component boundaries without props drilling
// Always default to 'IDR' to match SSR
const currentCurrency = ref<Currency>('IDR');

let initialized = false;

export function useCurrency() {
    if (!initialized) {
        watch(currentCurrency, (newVal) => {
            if (typeof window !== 'undefined') {
                localStorage.setItem('vins_currency', newVal);
            }
        });
        initialized = true;
    }

    const setCurrency = (currency: Currency) => {
        currentCurrency.value = currency;
    };

    return {
        currentCurrency,
        setCurrency,
    };
}

// Call this in onMounted to load stored currency preference
export function loadStoredCurrency() {
    if (typeof window === 'undefined') return;
    
    const stored = localStorage.getItem('vins_currency') as Currency;
    if (stored === 'IDR' || stored === 'USD') {
        currentCurrency.value = stored;
    }
}
