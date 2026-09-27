import type { TripSeat } from './types.ts';

function seatDistance(group: TripSeat[]): number {
  const rows=group.map(seat=>seat.layout.row);
  const columns=group.map(seat=>seat.layout.column);
  return (Math.max(...rows)-Math.min(...rows))*20 + (Math.max(...columns)-Math.min(...columns));
}

export function recommendAdjacentSeats(seats:TripSeat[],passengerCount:number):TripSeat[]{
  const count=Math.max(1,Math.min(6,Math.floor(passengerCount||1)));
  const available=seats.filter(seat=>seat.status==='available').sort((a,b)=>a.layout.row-b.layout.row||a.layout.column-b.layout.column);
  if(available.length<=count)return available;
  const byRow=new Map<number,TripSeat[]>();
  for(const seat of available)byRow.set(seat.layout.row,[...(byRow.get(seat.layout.row)??[]),seat]);
  const sameRow:TripSeat[][]=[];
  for(const rowSeats of byRow.values()){
    const sorted=[...rowSeats].sort((a,b)=>a.layout.column-b.layout.column);
    for(let start=0;start<=sorted.length-count;start++)sameRow.push(sorted.slice(start,start+count));
  }
  if(sameRow.length)return sameRow.sort((a,b)=>seatDistance(a)-seatDistance(b)||a[0].layout.row-b[0].layout.row)[0];
  const windows:TripSeat[][]=[];
  for(let start=0;start<=available.length-count;start++)windows.push(available.slice(start,start+count));
  return windows.sort((a,b)=>seatDistance(a)-seatDistance(b))[0]??available.slice(0,count);
}
