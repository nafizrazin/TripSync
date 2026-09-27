'use client';
import { Armchair, CircleDot, Crown, DoorOpen } from 'lucide-react';
import type { TripSeat } from '@/lib/types';

export function SeatMap({seats,selected,onToggle}:{seats:TripSeat[];selected:Set<string>;onToggle:(seat:TripSeat)=>void}) {
  const rows=new Map<number,TripSeat[]>(); seats.forEach(seat=>{const r=seat.layout.row; rows.set(r,[...(rows.get(r)??[]),seat]);});
  return <div className="seat-map nova-cabin" aria-label="Bus seat map">
    <div className="cabin-front"><div className="driver"><CircleDot size={22}/><span>Driver</span></div><div className="cabin-door"><DoorOpen size={18}/><span>Door</span></div></div>
    <div className="cabin-labels"><span>WINDOW</span><span>AISLE</span><span>WINDOW</span></div>
    {[...rows.entries()].sort((a,b)=>a[0]-b[0]).map(([row,rowSeats])=><div className="seat-row" key={row}>{[1,2,3,4,5].map(col=>{const seat=rowSeats.find(s=>s.layout.column===col); if(col===3)return <span className="aisle" key={`a-${row}`}>{row}</span>; if(!seat)return <span key={`${row}-${col}`} className="seat-placeholder"/>; const state=selected.has(seat.id)?'selected':seat.status; const position=seat.layout.is_window?'window':seat.layout.is_aisle?'aisle':'center'; return <button key={seat.id} type="button" disabled={seat.status!=='available'} onClick={()=>onToggle(seat)} className={`seat ${state} ${seat.layout.type}`} aria-label={`Seat ${seat.seat_number}: ${state}, ${position}`} title={`${seat.seat_number} · ${position} · ${seat.layout.type}`}><span className="seat-icon-wrap">{seat.layout.type==='premium'?<Crown size={13}/>:null}<Armchair size={19}/></span><span>{seat.seat_number}</span><small className="seat-position">{position}</small></button>;})}</div>)}
    <div className="seat-legend"><span><i className="legend available"/>Available</span><span><i className="legend selected"/>Selected</span><span><i className="legend held"/>Held</span><span><i className="legend sold"/>Sold</span></div>
  </div>;
}
