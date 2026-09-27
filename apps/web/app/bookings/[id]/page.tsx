'use client';
import { useEffect,useState } from 'react';
import { useParams } from 'next/navigation';
import { CheckCircle2,Clock3,Download,MapPin,ReceiptText,Route,ShieldCheck,TicketCheck } from 'lucide-react';
import { apiFetch,ApiError } from '@/lib/api';
import { formatBdt } from '@/lib/booking';
import type { Booking } from '@/lib/types';

export default function BookingDetailPage(){
  const {id}=useParams<{id:string}>();const [booking,setBooking]=useState<Booking|null>(null);const [error,setError]=useState('');const [busy,setBusy]=useState(false);
  const load=()=>apiFetch<{data:Booking}>(`/bookings/${id}`).then(r=>setBooking(r.data)).catch(e=>setError(e instanceof ApiError?e.message:'Unable to load booking.'));
  useEffect(()=>{void load()},[id]);
  async function cancel(){if(!confirm('Cancel this booking? Seats will be released and the applicable simulated refund will be recorded.'))return;setBusy(true);try{await apiFetch(`/bookings/${id}/cancel`,{method:'POST',body:JSON.stringify({reason:'Customer cancellation'})});await load();}catch(e){setError(e instanceof ApiError?e.message:'Cancellation failed.')}finally{setBusy(false)}}
  if(!booking)return <main className="page-main"><div className="shell narrow">{error?<div className="notice error">{error}</div>:<div className="trip-skeleton"/>}</div></main>;
  const confirmed=['confirmed','completed'].includes(booking.status); const departure=booking.trip?new Date(booking.trip.departure_at):null;
  return <main className="page-main booking-command"><div className="shell narrow"><div className="command-heading"><span className="eyebrow"><Route size={13}/> Journey command</span><h1>{booking.trip?`${booking.trip.route.origin.name} → ${booking.trip.route.destination.name}`:booking.booking_reference}</h1><p>{departure?new Intl.DateTimeFormat('en-BD',{dateStyle:'full',timeStyle:'short',timeZone:'Asia/Dhaka'}).format(departure):''}</p></div>
    {confirmed&&<div className="success-banner"><CheckCircle2 size={20}/><div><strong>Booking confirmed</strong><span>Your inventory, payment and ticket records are synchronized.</span></div></div>}
    <div className="boarding-readiness"><div><ShieldCheck size={20}/><span><strong>Boarding readiness</strong><small>{booking.status==='confirmed'?'Ticket active · arrive at least 20 minutes before departure':booking.status.replaceAll('_',' ')}</small></span></div><span className={`status-pill ${booking.status}`}>{booking.status.replaceAll('_',' ')}</span></div>
    <div className="travel-timeline"><div className={confirmed?'complete':''}><i/><span>Booked</span></div><div className={confirmed?'complete':''}><i/><span>Paid</span></div><div className={confirmed?'active':''}><i/><span>Ready to board</span></div><div><i/><span>Journey complete</span></div></div>
    <article className="ticket-sheet nova-ticket-sheet"><div className="ticket-head"><div><span className="eyebrow">TripSync booking</span><h2>{booking.booking_reference}</h2></div><span className={`status-pill ${booking.status}`}>{booking.status.replaceAll('_',' ')}</span></div><div className="ticket-route"><MapPin size={20}/><div><strong>{booking.trip?.route.origin.name ?? 'Journey'} → {booking.trip?.route.destination.name ?? ''}</strong><span>{departure?new Intl.DateTimeFormat('en-BD',{dateStyle:'full',timeStyle:'short',timeZone:'Asia/Dhaka'}).format(departure):''}</span></div></div><div className="ticket-grid"><div><span>Total</span><strong>{formatBdt(booking.total_amount)}</strong></div><div><span>Passengers</span><strong>{booking.passengers?.length??0}</strong></div><div><span>Payment</span><strong>{booking.payments?.at(0)?.status??'pending'}</strong></div><div><span>Tickets</span><strong>{booking.tickets?.length??0}</strong></div></div>{booking.passengers?.map(p=><div className="passenger-ticket" key={p.id}><ReceiptText size={18}/><div><strong>{p.full_name}</strong><span>{p.phone}</span></div><span className="seat-tag"><TicketCheck size={12}/>{p.seat??'Seat'}</span></div>)}<div className="ticket-actions"><button className="secondary-button" onClick={()=>window.print()}><Download size={16}/> Print / save PDF</button>{booking.status==='confirmed'&&<button className="danger-button" onClick={cancel} disabled={busy}>{busy?'Cancelling…':'Cancel booking'}</button>}</div></article>
    <p className="fine-print command-footnote"><Clock3 size={14}/> Payment and seat ownership remain server-authoritative even when this page is cached by your browser.</p>
  </div></main>;
}
