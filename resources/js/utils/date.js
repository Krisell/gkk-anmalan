/**
 * Calculate ISO week number according to Swedish standard
 * This function handles the Swedish/ISO week numbering system where:
 * - Weeks start on Monday
 * - First week of the year is the one with the first Thursday
 *
 * @param {Date} date - The date to get the week number for
 * @return {number} - The ISO week number
 */
export function getISOWeek(date) {
    // Copy date to avoid modifying the original
    const d = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
    
    // Set to nearest Thursday: current date + 4 - current day number
    // Make Sunday's day number 7
    const dayNum = d.getUTCDay() || 7;
    d.setUTCDate(d.getUTCDate() + 4 - dayNum);
    
    // Get first day of year
    const yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1));
    
    // Calculate full weeks between the first day of year and the current date's Thursday
    return Math.ceil((((d - yearStart) / 86400000) + 1) / 7);
}

/**
 * Get Swedish month name for a given month index
 *
 * @param {number} monthIndex - Month index (0-11)
 * @return {string} - Swedish month name
 */
export function getSwedishMonthName(monthIndex) {
    const monthNames = [
        'januari', 'februari', 'mars', 'april', 'maj', 'juni',
        'juli', 'augusti', 'september', 'oktober', 'november', 'december'
    ];
    return monthNames[monthIndex];
}
