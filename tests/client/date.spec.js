import { describe, expect, it } from 'vitest';
import { getISOWeek, getSwedishMonthName } from '../../resources/js/utils/date';

describe('date utils', () => {
    describe('getISOWeek', () => {
        it('should return the correct week number for a date in January', () => {
            const date = new Date(2025, 0, 1);
            expect(getISOWeek(date)).toBe(1);
        });

        it('should return the correct week number for a date in June', () => {
            let date = new Date(2025, 5, 1);
            expect(getISOWeek(date)).toBe(22);

            date = new Date(2025, 5, 2);
            expect(getISOWeek(date)).toBe(23);
        });

        it('should handle edge case at the start of a year', () => {
            const date = new Date(2024, 11, 31);
            expect(getISOWeek(date)).toBe(1);
        });

        it('should handle edge case at the end of a year', () => {
            const date = new Date(2026, 0, 1);
            expect(getISOWeek(date)).toBe(1);
        });

        it('should return the correct week number for a day in last week of year', () => {
            const date = new Date(2025, 11, 28);
            expect(getISOWeek(date)).toBe(52);
        });

        it('handles a week 53 case', () => {
            const date = new Date(2026, 11, 29);
            expect(getISOWeek(date)).toBe(53);
        });
    });

    describe('getSwedishMonthName', () => {
        it('should return the correct Swedish month names', () => {
            expect(getSwedishMonthName(0)).toBe('januari');
            expect(getSwedishMonthName(1)).toBe('februari');
            expect(getSwedishMonthName(2)).toBe('mars');
            expect(getSwedishMonthName(3)).toBe('april');
            expect(getSwedishMonthName(4)).toBe('maj');
            expect(getSwedishMonthName(5)).toBe('juni');
            expect(getSwedishMonthName(6)).toBe('juli');
            expect(getSwedishMonthName(7)).toBe('augusti');
            expect(getSwedishMonthName(8)).toBe('september');
            expect(getSwedishMonthName(9)).toBe('oktober');
            expect(getSwedishMonthName(10)).toBe('november');
            expect(getSwedishMonthName(11)).toBe('december');
        });
    });
});
