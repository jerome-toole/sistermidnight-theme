/**
 * Delay calling `fn` until `wait` ms have passed without another call.
 * The returned function has `.cancel()` to drop a pending call.
 *
 *   window.addEventListener('resize', debounce(measure, 150));
 *
 * @param {Function} fn
 * @param {number} [wait=100]
 */
export default function debounce(fn, wait = 100) {
    let timer;

    const debounced = (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn(...args), wait);
    };

    debounced.cancel = () => clearTimeout(timer);

    return debounced;
}
