import test from 'node:test';
import assert from 'node:assert/strict';
import { filterTrips, labelsForTrip, scoreTrip, sortTrips, type DiscoveryFilters } from '../lib/trip-discovery.ts';
import type { Trip } from '../lib/types.ts';

function trip(overrides: Partial<Trip> = {}): Trip {
  return {
    id:'1', public_id:'TRP-1', departure_at:'2026-10-01T02:00:00Z', arrival_at:'2026-10-01T08:00:00Z', base_fare:'1000.00',
    trip_status:'scheduled', booking_status:'open', available_seats:20,
    route:{code:'DAC-CTG',origin:{id:'a',name:'Dhaka',slug:'dhaka'},destination:{id:'b',name:'Chattogram',slug:'chattogram'},duration_minutes:360},
    bus:{id:'bus',label:'GL-1',type:'ac',comfort_class:'business',amenities:['WiFi','USB','AC'],registration_number:'X',operator:{id:'op',name:'Green Line',short_code:'GL',rating:'4.80',review_count:100,punctuality_percent:'96.00',brand_color:'#22d3ee'}},
    ...overrides,
  };
}
const empty: DiscoveryFilters={operators:[],busTypes:[],periods:[],amenities:[],maxFare:null,minSeats:1};

test('TripScore rewards stronger operator performance and availability',()=>{
  const strong=trip();
  const weak=trip({available_seats:2,base_fare:'1800.00',route:{...trip().route,duration_minutes:600},bus:{...trip().bus,operator:{...trip().bus.operator,rating:'3.50',punctuality_percent:'75.00'}}});
  assert.ok(scoreTrip(strong)>scoreTrip(weak));
});

test('filters apply operator, type, period, amenity, fare and capacity',()=>{
  const morning=trip();
  const night=trip({id:'2',departure_at:'2026-10-01T16:30:00Z',base_fare:'1500.00',available_seats:3,bus:{...trip().bus,type:'non_ac',amenities:['USB'],operator:{...trip().bus.operator,id:'op2'}}});
  assert.deepEqual(filterTrips([morning,night],{...empty,operators:['op'],busTypes:['ac'],periods:['morning'],amenities:['WiFi'],maxFare:1200,minSeats:4}).map(t=>t.id),['1']);
});

test('sort and labels identify cheapest, fastest, most seats and recommended',()=>{
  const a=trip({id:'a',base_fare:'900.00',available_seats:10});
  const b=trip({id:'b',base_fare:'1200.00',available_seats:28,route:{...trip().route,duration_minutes:300}});
  const list=[a,b];
  assert.equal(sortTrips(list,'cheapest')[0].id,'a');
  assert.equal(sortTrips(list,'fastest')[0].id,'b');
  assert.ok(labelsForTrip(a,list).includes('Cheapest'));
  assert.ok(labelsForTrip(b,list).includes('Fastest'));
  assert.ok(labelsForTrip(b,list).includes('Most Seats'));
});
