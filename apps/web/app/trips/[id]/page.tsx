'use client';
import { useEffect,useMemo,useState } from 'react';
import { useParams,useRouter,useSearchParams } from 'next/navigation';
import { ArrowRight,Clock3,Lightbulb,LockKeyhole,MapPinned,Sparkles } from 'lucide-react';
import { SeatMap } from '@/components/SeatMap';
import { apiFetch,ApiError } from '@/lib/api';
import { formatBdt } from '@/lib/booking';
import { recommendAdjacentSeats } from '@/lib/seat-recommendation';
import type { SeatHold,TripSeat } from '@/lib/types';

export default function TripSeatsPage(){
  const {id}=useParams<{id:string}>(); const router=useRouter(); const q=useSearchParams();
  const passengers=Math.min(6,Math.max(1,Number(q.get('passengers')??1)||1));
  const [seats,setSeats]=useState<TripSeat[]>([]); const [selected,setSelected]=useState<Set<string>>(new Set());
  const [loading,setLoading]=useState(true); const [error,setError]=useState(''); const [holding,setHolding]=useState(false);
  useEffect(()=>{void apiFetch<{data:TripSeat[]}>(`/trips/${id}/seats`).then(r=>setSeats(r.data)).catch(e=>setError(e instanceof ApiError?e.message:'Unable to load seats.')).finally(()=>setLoading(false));},[id]);
  const chosen=useMemo(()=>seats.filter(s=>selected.has(s.id)),[seats,selected]);
  const suggested=useMemo(()=>recommendAdjacentSeats(seats,passengers),[seats,passengers]);
  const total=chosen.reduce((n,s)=>n+Number(s.fare),0);
  function toggle(seat:TripSeat){setSelected(prev=>{const next=new Set(prev);if(next.has(seat.id))next.delete(seat.id);else if(next.size<passengers)next.add(seat.id);return next})}
  function suggestBest(){setSelected(new Set(suggested.slice(0,passengers).map(seat=>seat.id)))}
  async function continueBooking(){if(selected.size!==passengers)return;setHolding(true);setError('');try{const result=await apiFetch<{data:SeatHold}>('/seat-holds',{method:'POST',body:JSON.stringify({trip_id:id,seat_ids:[...selected]})});router.push(`/checkout?hold=${result.data.id}`);}catch(e){const a=e as ApiError;if(a.status===401)router.push(`/login?next=${encodeURIComponent(`/trips/${id}?passengers=${passengers}`)}`);else setError(a.message);}finally{setHolding(false)}}
  return <main className="page-main nova-seat-page"><div className="shell booking-layout"><section><span className="eyebrow"><Sparkles size={13}/> Cabin intelligence</span><h1>Choose {passengers} seat{passengers===1?'':'s'}</h1><p className="muted">Live inventory is verified again by the server when you continue. Your successful selection is protected for 10 minutes.</p>
    <div className="seat-recommendation"><div><Lightbulb size={18}/><span><strong>Traveling together?</strong><small>We can choose the closest available seats for your group.</small></span></div><button type="button" className="secondary-button compact-action" disabled={suggested.length<passengers} onClick={suggestBest}>Suggest best {passengers}</button></div>
    {error&&<div className="notice error">{error}</div>}{loading?<div className="seat-map skeleton-box"/>:<SeatMap seats={seats} selected={selected} onToggle={toggle}/>}</section>
    <aside className="summary-card sticky nova-booking-summary"><div className="summary-title"><LockKeyhole size={19}/><span>Protected booking</span></div>{chosen.length?<div className="seat-detail-grid">{chosen.map(s=>{const position=s.layout.is_window?'window':s.layout.is_aisle?'aisle':'center';return <div className="seat-detail" key={s.id}><div><strong>{s.seat_number}</strong><span>{s.layout.type}</span></div><div><span><MapPinned size={12}/>{position}</span><strong>{formatBdt(s.fare)}</strong></div></div>})}</div>:<p className="muted">Select {passengers} seat{passengers===1?'':'s'} to continue.</p>}<div className="summary-total"><span>Seat total</span><strong>{formatBdt(total)}</strong></div><p className="fine-print"><Clock3 size={14}/> Service fee is calculated at checkout. Seat locks are server-authoritative.</p><button disabled={chosen.length!==passengers||holding} className="primary-button wide" onClick={continueBooking}>{holding?'Protecting seats…':<>Continue <ArrowRight size={17}/></>}</button></aside>
  </div></main>;
}
