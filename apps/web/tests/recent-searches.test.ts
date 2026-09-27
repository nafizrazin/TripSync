import test from 'node:test';
import assert from 'node:assert/strict';
import { addRecentSearch, type RecentSearch } from '../lib/recent-searches.ts';

const base: RecentSearch = { fromId:'a', fromName:'Dhaka', toId:'b', toName:'Chattogram', date:'2026-10-01', passengers:2 };

test('recent searches de-duplicate the same route and keep the latest search first', () => {
  const list = addRecentSearch([base], { ...base, date:'2026-10-03', passengers:3 });
  assert.equal(list.length, 1);
  assert.equal(list[0].date, '2026-10-03');
  assert.equal(list[0].passengers, 3);
});

test('recent searches are capped at four items', () => {
  let list: RecentSearch[] = [];
  for (let i=0;i<6;i++) list = addRecentSearch(list,{...base,fromId:`f${i}`,toId:`t${i}`,fromName:`F${i}`,toName:`T${i}`});
  assert.equal(list.length, 4);
  assert.equal(list[0].fromId, 'f5');
});
