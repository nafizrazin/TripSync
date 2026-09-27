'use client';
import { ArrowRightLeft, CalendarDays, MapPin, Search, UsersRound } from 'lucide-react';
import { useEffect, useMemo, useState } from 'react';
import { useRouter } from 'next/navigation';
import { apiFetch } from '@/lib/api';
import { persistRecentSearch } from '@/lib/recent-searches';
import type { Location } from '@/lib/types';

function dhakaDate(offsetDays=0): string {
  return new Intl.DateTimeFormat('en-CA',{timeZone:'Asia/Dhaka',year:'numeric',month:'2-digit',day:'2-digit'}).format(new Date(Date.now()+offsetDays*86400000));
}

export function SearchForm({compact=false}:{compact?:boolean}) {
  const router=useRouter();
  const [locations,setLocations]=useState<Location[]>([]);
  const [from,setFrom]=useState(''); const [to,setTo]=useState('');
  const [date,setDate]=useState(()=>dhakaDate(1)); const [passengers,setPassengers]=useState(1);
  useEffect(()=>{ void apiFetch<{data:Location[]}>('/locations').then(r=>setLocations(r.data)).catch(()=>{}); },[]);
  const minDate=useMemo(()=>dhakaDate(),[]);
  function swap(){ setFrom(to); setTo(from); }
  function submit(e:React.FormEvent){
    e.preventDefault(); if(!from||!to||!date||from===to)return;
    const fromLocation=locations.find(x=>x.id===from); const toLocation=locations.find(x=>x.id===to);
    if(typeof window!=='undefined'&&fromLocation&&toLocation) persistRecentSearch({fromId:from,fromName:fromLocation.name,toId:to,toName:toLocation.name,date,passengers},window.localStorage);
    router.push(`/search?from=${encodeURIComponent(from)}&to=${encodeURIComponent(to)}&date=${encodeURIComponent(date)}&passengers=${passengers}`);
  }
  return <form className={compact?'search-panel compact nova-search-panel':'search-panel nova-search-panel'} onSubmit={submit}>
    <label><span><MapPin size={16}/> From</span><select value={from} onChange={e=>setFrom(e.target.value)} required><option value="">Select city</option>{locations.map(x=><option key={x.id} value={x.id}>{x.name}</option>)}</select></label>
    <button type="button" className="swap-button" onClick={swap} aria-label="Swap origin and destination"><ArrowRightLeft size={18}/></button>
    <label><span><MapPin size={16}/> To</span><select value={to} onChange={e=>setTo(e.target.value)} required><option value="">Select city</option>{locations.map(x=><option key={x.id} value={x.id}>{x.name}</option>)}</select></label>
    <label><span><CalendarDays size={16}/> Journey date</span><input type="date" min={minDate} value={date} onChange={e=>setDate(e.target.value)} required/></label>
    <label className="passenger-field"><span><UsersRound size={16}/> Passengers</span><select value={passengers} onChange={e=>setPassengers(Number(e.target.value))}>{[1,2,3,4,5,6].map(value=><option key={value} value={value}>{value} passenger{value===1?'':'s'}</option>)}</select></label>
    <button className="primary-button search-button" type="submit"><Search size={18}/> Find journeys</button>
  </form>;
}
