import test from 'node:test';
import assert from 'node:assert/strict';
import { recommendAdjacentSeats } from '../lib/seat-recommendation.ts';
import type { TripSeat } from '../lib/types.ts';

const seat=(id:string,row:number,column:number,status:TripSeat['status']='available'):TripSeat=>({id,seat_number:id,fare:'1000.00',status,hold_expires_at:null,layout:{row,column,deck:1,type:'standard',is_window:[1,5].includes(column),is_aisle:[2,4].includes(column)}});

test('prefers a same-row group with the smallest spread',()=>{
  const seats=[seat('1A',1,1),seat('1B',1,2),seat('1C',1,4),seat('1D',1,5),seat('2A',2,1),seat('2B',2,2)];
  assert.deepEqual(recommendAdjacentSeats(seats,3).map(s=>s.id),['1A','1B','1C']);
});

test('never recommends sold or held seats',()=>{
  const seats=[seat('1A',1,1,'sold'),seat('1B',1,2),seat('1C',1,4,'held'),seat('2A',2,1),seat('2B',2,2)];
  const selected=recommendAdjacentSeats(seats,2);
  assert.equal(selected.length,2);
  assert.ok(selected.every(s=>s.status==='available'));
});

test('caps recommendations at available inventory',()=>{
  assert.equal(recommendAdjacentSeats([seat('1A',1,1)],4).length,1);
});
