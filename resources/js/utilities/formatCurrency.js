/**
 * Format a number into USD currency.
 *
 * @param {number|string|null} value
 * @param {object} options
 * @param {number} options.minimumFractionDigits
 * @param {number} options.maximumFractionDigits
 * @returns {string}
 */
export function formatCurrency(
    value,
    { minimumFractionDigits = 2, maximumFractionDigits = 2 } = {}
) {
    if (value === null || value === undefined || value === '') {
        return '$0.00';
    }

    const number = Number(value);

    if (Number.isNaN(number)) {
        return '$0.00';
    }

    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits,
        maximumFractionDigits,
    }).format(number);
}
