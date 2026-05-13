import { onMounted, onUnmounted } from 'vue';

/**
 * Composable that triggers reveal animations on elements with `data-reveal`
 * when they scroll into view using Intersection Observer.
 *
 * Usage: call `useScrollReveal()` in your page's <script setup>.
 * Then add `data-reveal` to any element you want animated on scroll.
 *
 * Optional attributes:
 *   data-reveal="up"       (default) slide up
 *   data-reveal="left"     slide from left
 *   data-reveal="right"    slide from right
 *   data-reveal="fade"     fade in only
 *   data-reveal-delay="200" delay in ms
 */
export function useScrollReveal() {
    let observer: IntersectionObserver | null = null;

    onMounted(() => {
        observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('revealed');
                        observer?.unobserve(entry.target); // animate once only
                    }
                });
            },
            {
                threshold: 0.15,
                rootMargin: '0px 0px -40px 0px',
            },
        );

        document.querySelectorAll('[data-reveal]').forEach((el) => {
            observer!.observe(el);
        });
    });

    onUnmounted(() => {
        observer?.disconnect();
    });
}
