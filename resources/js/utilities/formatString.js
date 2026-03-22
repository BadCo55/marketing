export const camelToNormal = (value) => {
    if (!value) return ''

    return String(value)
        .replaceAll('_', ' ')
        .replace(/\b\w/g, (char) => char.toUpperCase())
}
