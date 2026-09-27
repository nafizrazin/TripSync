import test from 'node:test';
import assert from 'node:assert/strict';
import { formatBdt, formatDuration, holdSecondsRemaining } from '../lib/booking.ts';

test('formats BDT without losing decimal precision', () => {
  assert.equal(formatBdt('2460.00'), '৳2,460');
  assert.equal(formatBdt('1200.50'), '৳1,200.50');
});

test('formats trip duration in hours and minutes', () => {
  assert.equal(formatDuration(360), '6h');
  assert.equal(formatDuration(395), '6h 35m');
});

test('hold countdown never becomes negative', () => {
  assert.equal(holdSecondsRemaining('2026-09-28T00:10:00+06:00', new Date('2026-09-28T00:09:00+06:00')), 60);
  assert.equal(holdSecondsRemaining('2026-09-28T00:08:00+06:00', new Date('2026-09-28T00:09:00+06:00')), 0);
});
