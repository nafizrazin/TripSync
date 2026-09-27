'use client';
import Link from 'next/link';
import { useEffect,useMemo,useState } from 'react';
import { ArrowRight,CalendarClock,Clock3,MapPin,Ticket } from 'lucide-react';
import { apiFetch,ApiError } from '@/lib/api';
import { formatBdt } from '@/lib/booking';
import type { Booking } from '@/lib/types';

function countdown(iso?:string):string{
  if(!iso)return '';
  const diff=new Date(iso).getTime()-Date.now(); if(diff<=0)return 'Departure time reached';
  const hours=Math.floor(diff/3600000); const days=Math.floor(hours/24);
  return days>0?`${days}d ${hours%24}h to departure`:`${hours}h ${Math.floor((diff%3600000)/60000)}m to departure`;
}
function BookingCard({booking,upcoming}:{booking:Booking;upcoming:boolean}){
  const trip=booking.trip;
  return <Link className="travel-wallet-card" href={`/bookings/${booking.id}`}><div className="wallet-route-icon"><MapPin size={18}/></div><div className="wallet-main"><div className="wallet-topline"><strong>{trip?`${trip.route.origin.name} → ${trip.route.destination.name}`:booking.booking_reference}</strong><span className={`status-pill ${booking.status}`}>{booking.status.replaceAll('_',' ')}</span></div><span>{trip?new Intl.DateTimeFormat('en-BD',{dateStyle:'medium',timeStyle:'short',timeZone:'Asia/Dhaka'}).format(new Date(trip.departure_at)):booking.booking_reference}</span><div className="wallet-meta"><span><Ticket size={13}/>{booking.booking_reference}</span><span>{formatBdt(booking.total_amount)}</span></div>{upcoming&&trip&&<div className="departure-countdown"><Clock3 size={13}/>{countdown(trip.departure_at)}</div>}</div><ArrowRight size={18}/></Link>;
}
export default function BookingsPage(){
  const [items,setItems]=useState<Booking[]>([]);const [error,setError]=useState('');
  useEffect(()=>{void apiFetch<{data:Booking[]}>('/bookings').then(r=>setItems(r.data)).catch(e=>setError(e instanceof ApiError?e.message:'Unable to load bookings.'));},[]);
  const {upcoming,past}=useMemo(()=>{const now=Date.now();return {upcoming:items.filter(b=>b.trip&&new Date(b.trip.departure_at).getTime()>now&&!['cancelled','completed'].includes(b.status)),past:items.filter(b=>!b.trip||new Date(b.trip.departure_at).getTime()<=now||['cancelled','completed'].includes(b.status))}},[items]);
  return <main className="page-main travel-wallet"><div className="shell"><div className="wallet-hero"><div><span className="eyebrow"><Ticket size={13}/> Your travel wallet</span><h1>My journeys</h1><p>Tickets, payments and travel status in one synchronized view.</p></div><div className="wallet-stat"><CalendarClock size={19}/><strong>{upcoming.length}</strong><span>upcoming</span></div></div>{error&&<div className="notice error">{error}</div>}
    {!!upcoming.length&&<section className="wallet-section"><h2>Upcoming journeys</h2><div className="booking-list nova-booking-list">{upcoming.map(b=><BookingCard booking={b} upcoming key={b.id}/>)}</div></section>}
    {!!past.length&&<section className="wallet-section"><h2>Past journeys</h2><div className="booking-list nova-booking-list">{past.map(b=><BookingCard booking={b} upcoming={false} key={b.id}/>)}</div></section>}
    {!items.length&&!error&&<div className="empty-state"><h2>No journeys yet</h2><p>Your bookings will become your personal travel timeline.</p><Link href="/search" className="primary-button small">Discover a trip</Link></div>}
  </div></main>;
}
