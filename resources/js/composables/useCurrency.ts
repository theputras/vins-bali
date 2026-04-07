import { ref, watch } from 'vue';

type Currency = 'IDR' | 'USD';

// Global state so it persists across layout / component boundaries without props drilling
const currentCurrency = ref<Currency>('IDR');

let initialized = false;

export function useCurrency() {
    if (!initialized && typeof window !== 'undefined') {
        const stored = localStorage.getItem('vins_currency') as Currency;
        if (stored === 'IDR' || stored === 'USD') {
            currentCurrency.value = stored;
        }
        
        watch(currentCurrency, (newVal) => {
            localStorage.setItem('vins_currency', newVal);
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
