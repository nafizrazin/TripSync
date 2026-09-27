export type RecentSearch = {
  fromId: string;
  fromName: string;
  toId: string;
  toName: string;
  date: string;
  passengers: number;
};

export const RECENT_SEARCHES_KEY = 'tripsync:recent-searches';

export function addRecentSearch(current: RecentSearch[], next: RecentSearch): RecentSearch[] {
  const withoutSameRoute = current.filter(item => !(item.fromId === next.fromId && item.toId === next.toId));
  return [next, ...withoutSameRoute].slice(0, 4);
}

export function loadRecentSearches(storage?: Pick<Storage,'getItem'>): RecentSearch[] {
  if (!storage) return [];
  try {
    const value = storage.getItem(RECENT_SEARCHES_KEY);
    if (!value) return [];
    const parsed = JSON.parse(value) as RecentSearch[];
    return Array.isArray(parsed) ? parsed.slice(0,4) : [];
  } catch {
    return [];
  }
}

export function persistRecentSearch(search: RecentSearch, storage?: Pick<Storage,'getItem'|'setItem'>): void {
  if (!storage) return;
  const next = addRecentSearch(loadRecentSearches(storage), search);
  storage.setItem(RECENT_SEARCHES_KEY, JSON.stringify(next));
}
