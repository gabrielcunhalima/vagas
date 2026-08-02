export function asset(path) {
    const base = document.querySelector('meta[name="asset-base-url"]')?.content ?? '';
    return `${base}/${path.replace(/^\/+/, '')}`;
}
