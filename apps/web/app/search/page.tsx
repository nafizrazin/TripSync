'use client';
import { Suspense,useEffect,useMemo,useState } from 'react';
import { useRouter,useSearchParams } from 'next/navigation';
import { ArrowDownUp, SearchX, Sparkles } from 'lucide-react';
import { SearchForm } from '@/components/SearchForm';
import { TripCard } from '@/components/TripCard';
import { FareCalendar } from '@/components/FareCalendar';
import { SearchFilters } from '@/components/SearchFilters';
import { apiFetch,ApiError } from '@/lib/api';
import { filterTrips,labelsForTrip,scoreTrip,sortTrips,type DiscoveryFilters,type TripSort } from '@/lib/trip-discovery';
import type { FareCalendarDay,Trip } from '@/lib/types';

const emptyFilters=(passengers:number):DiscoveryFilters=>({operators:[],busTypes:[],periods:[],amenities:[],maxFare:null,minSeats:passengers});

function Results(){
  const q=useSearchParams(); const router=useRouter();
  const [trips,setTrips]=useState<Trip[]>([]); const [calendar,setCalendar]=useState<FareCalendarDay[]>([]);
  const [loading,setLoading]=useState(false); const [error,setError]=useState(''); const [sort,setSort]=useState<TripSort>('recommended');
  const passengers=Math.min(6,Math.max(1,Number(q.get('passengers')??1)||1));
  const [filters,setFilters]=useState<DiscoveryFilters>(()=>emptyFilters(passengers));
  const from=q.get('from'),to=q.get('to'),date=q.get('date');
  useEffect(()=>{setFilters(current=>({...current,minSeats:passengers}));},[passengers]);
  useEffect(()=>{
    if(!from||!to||!date)return; setLoading(true);setError('');
    const params=`from=${encodeURIComponent(from)}&to=${encodeURIComponent(to)}&date=${encodeURIComponent(date)}&passengers=${passengers}`;
    const calendarParams=`from=${encodeURIComponent(from)}&to=${encodeURIComponent(to)}&start_date=${encodeURIComponent(date)}&days=7&passengers=${passengers}`;
    void Promise.all([apiFetch<{data:Trip[]}>(`/trips/search?${params}`),apiFetch<{data:FareCalendarDay[]}>(`/trips/fare-calendar?${calendarParams}`)])
      .then(([result,fares])=>{setTrips(result.data);setCalendar(fares.data);})
      .catch(e=>setError(e instanceof ApiError?e.message:'Unable to load trips.')).finally(()=>setLoading(false));
  },[from,to,date,passengers]);
  const visible=useMemo(()=>sortTrips(filterTrips(trips,filters),sort),[trips,filters,sort]);
  function selectDate(nextDate:string){const params=new URLSearchParams(q.toString());params.set('date',nextDate);router.push(`/search?${params.toString()}`)}
  if(!from||!to||!date)return <div className="empty-state"><SearchX size={26}/><h2>Choose a route to begin</h2><p>Select your origin, destination and travel date above.</p></div>;
  return <>
    <FareCalendar days={calendar} currentDate={date} onSelect={selectDate}/>
    <div className="results-top nova-results-top"><div><span className="eyebrow"><Sparkles size={13}/> Smart comparison</span><h1>{loading?'Scanning live inventory…':`${visible.length} of ${trips.length} journeys`}</h1><p>{passengers} passenger{passengers===1?'':'s'} · ranked by fare, duration, availability and operator performance.</p></div><label className="sort-control"><ArrowDownUp size={15}/><span>Sort</span><select value={sort} onChange={e=>setSort(e.target.value as TripSort)}><option value="recommended">Recommended</option><option value="cheapest">Cheapest</option><option value="fastest">Fastest</option><option value="earliest">Earliest</option><option value="seats">Most seats</option></select></label></div>
    {error&&<div className="notice error">{error}</div>}
    {loading?<div className="skeleton-list">{[1,2,3].map(i=><div className="trip-skeleton" key={i}/>)}</div>:<div className="search-results-layout"><SearchFilters trips={trips} filters={filters} onChange={setFilters}/><div className="trip-list">{visible.length?visible.map(t=><TripCard trip={t} passengers={passengers} score={scoreTrip(t)} labels={labelsForTrip(t,trips)} key={t.id}/>):<div className="empty-state"><SearchX size={26}/><h2>No journeys match these filters</h2><p>Reset a filter or choose another date from the fare calendar.</p></div>}</div></div>}
  </>;
}

export default function SearchPage(){return <main className="page-main nova-search-page"><div className="shell"><SearchForm compact/><Suspense fallback={<div className="trip-skeleton"/>}><Results/></Suspense></div></main>}
