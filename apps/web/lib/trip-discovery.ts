import type { Trip } from './types.ts';

export type DeparturePeriod = 'morning'|'afternoon'|'evening'|'night';
export type TripSort = 'recommended'|'cheapest'|'fastest'|'earliest'|'seats';
export type DiscoveryFilters = {
  operators:string[];
  busTypes:string[];
  periods:DeparturePeriod[];
  amenities:string[];
  maxFare:number|null;
  minSeats:number;
};

function dhakaHour(iso:string): number {
  const value = new Intl.DateTimeFormat('en-GB',{hour:'2-digit',hourCycle:'h23',timeZone:'Asia/Dhaka'}).format(new Date(iso));
  return Number(value);
}

export function departurePeriod(iso:string): DeparturePeriod {
  const hour=dhakaHour(iso);
  if(hour>=5&&hour<12)return 'morning';
  if(hour>=12&&hour<17)return 'afternoon';
  if(hour>=17&&hour<21)return 'evening';
  return 'night';
}

export function scoreTrip(trip:Trip): number {
  const rating=Math.min(100,Math.max(0,(Number(trip.bus.operator.rating)/5)*100));
  const punctuality=Math.min(100,Math.max(0,Number(trip.bus.operator.punctuality_percent)));
  const availability=Math.min(100,(trip.available_seats/24)*100);
  const duration=Math.max(0,100-(trip.route.duration_minutes/7));
  const fare=Math.max(0,100-(Number(trip.base_fare)/24));
  return Math.round((rating*.28)+(punctuality*.22)+(availability*.16)+(duration*.17)+(fare*.17));
}

export function filterTrips(trips:Trip[],filters:DiscoveryFilters):Trip[]{
  return trips.filter(trip=>{
    if(filters.operators.length&&!filters.operators.includes(trip.bus.operator.id))return false;
    if(filters.busTypes.length&&!filters.busTypes.includes(trip.bus.type))return false;
    if(filters.periods.length&&!filters.periods.includes(departurePeriod(trip.departure_at)))return false;
    if(filters.amenities.length&&!filters.amenities.every(item=>trip.bus.amenities.includes(item)))return false;
    if(filters.maxFare!==null&&Number(trip.base_fare)>filters.maxFare)return false;
    if(trip.available_seats<filters.minSeats)return false;
    return true;
  });
}

export function sortTrips(trips:Trip[],sort:TripSort):Trip[]{
  const copy=[...trips];
  return copy.sort((a,b)=>{
    if(sort==='cheapest')return Number(a.base_fare)-Number(b.base_fare);
    if(sort==='fastest')return a.route.duration_minutes-b.route.duration_minutes;
    if(sort==='earliest')return new Date(a.departure_at).getTime()-new Date(b.departure_at).getTime();
    if(sort==='seats')return b.available_seats-a.available_seats;
    return scoreTrip(b)-scoreTrip(a);
  });
}

export function labelsForTrip(trip:Trip,trips:Trip[]):string[]{
  if(!trips.length)return [];
  const labels:string[]=[];
  const minFare=Math.min(...trips.map(item=>Number(item.base_fare)));
  const minDuration=Math.min(...trips.map(item=>item.route.duration_minutes));
  const maxSeats=Math.max(...trips.map(item=>item.available_seats));
  const maxScore=Math.max(...trips.map(scoreTrip));
  if(Number(trip.base_fare)===minFare)labels.push('Cheapest');
  if(trip.route.duration_minutes===minDuration)labels.push('Fastest');
  if(trip.available_seats===maxSeats)labels.push('Most Seats');
  if(scoreTrip(trip)===maxScore)labels.unshift('Best Value');
  return [...new Set(labels)].slice(0,3);
}
