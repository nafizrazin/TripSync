export function formatBdt(value: string | number): string {
  const numeric = Number(value);
  const hasFraction = Math.round(numeric * 100) % 100 !== 0;
  return new Intl.NumberFormat('en-BD', {
    style: 'currency', currency: 'BDT', currencyDisplay: 'narrowSymbol',
    minimumFractionDigits: hasFraction ? 2 : 0, maximumFractionDigits: 2,
  }).format(numeric);
}

export function formatDuration(minutes: number): string {
  const hours = Math.floor(minutes / 60);
  const rest = minutes % 60;
  return rest === 0 ? `${hours}h` : `${hours}h ${rest}m`;
}

export function holdSecondsRemaining(expiresAt: string, now = new Date()): number {
  return Math.max(0, Math.floor((new Date(expiresAt).getTime() - now.getTime()) / 1000));
}

export function formatCountdown(seconds: number): string {
  const minutes = Math.floor(seconds / 60);
  return `${String(minutes).padStart(2, '0')}:${String(seconds % 60).padStart(2, '0')}`;
}
