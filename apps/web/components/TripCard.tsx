import Link from 'next/link';
import { ArrowRight, Armchair, Clock3, Gauge, PlugZap, Snowflake, Star, Wifi } from 'lucide-react';
import type { Trip } from '@/lib/types';
import { formatBdt, formatDuration } from '@/lib/booking';

export function TripCard({trip,labels=[],score,passengers=1}:{trip:Trip;labels?:string[];score?:number;passengers?:number}) {
  const departure=new Date(trip.departure_at); const arrival=new Date(trip.arrival_at); const time=(d:Date)=>new Intl.DateTimeFormat('en-BD',{hour:'numeric',minute:'2-digit',timeZone:'Asia/Dhaka'}).format(d);
  const amenities=trip.bus.amenities.slice(0,4);
  return <article className="trip-card nova-trip-card" style={{'--operator-accent':trip.bus.operator.brand_color??'#22d3ee'} as React.CSSProperties}>
    <div className="trip-labels">{labels.map(label=><span className={`comparison-label ${label.toLowerCase().replaceAll(' ','-')}`} key={label}>{label}</span>)}{score!==undefined&&<span className="trip-score" title="TripScore combines fare, duration, availability, rating and punctuality"><Gauge size={13}/> TripScore {score}</span>}</div>
    <div className="operator-row"><div className="operator-identity"><span className="operator-mini-logo">{trip.bus.operator.short_code??trip.bus.operator.name.slice(0,2)}</span><div><span className="operator-name">{trip.bus.operator.name}</span><span className="bus-label">{trip.bus.label} · {trip.bus.comfort_class}</span></div></div><div className="operator-trust"><span><Star size={13} fill="currentColor"/>{Number(trip.bus.operator.rating).toFixed(1)}</span><small>{Number(trip.bus.operator.punctuality_percent).toFixed(0)}% on-time</small></div></div>
    <div className="trip-main"><div className="time-block"><strong>{time(departure)}</strong><span>{trip.route.origin.name}</span></div><div className="journey-line"><span/><div><Clock3 size={14}/>{formatDuration(trip.route.duration_minutes)}</div><span/></div><div className="time-block right"><strong>{time(arrival)}</strong><span>{trip.route.destination.name}</span></div></div>
    <div className="amenity-row"><span className="type-pill"><Snowflake size={13}/>{trip.bus.type.replace('_',' ').toUpperCase()}</span>{amenities.map(item=><span className="amenity-pill" key={item}>{item==='WiFi'?<Wifi size={12}/>:item.includes('USB')||item.includes('Charging')?<PlugZap size={12}/>:null}{item}</span>)}</div>
    <div className="trip-footer"><div className="availability"><Armchair size={17}/><strong>{trip.available_seats}</strong> seats available</div><div className="fare"><span>From</span><strong>{formatBdt(trip.base_fare)}</strong></div><Link className="primary-button small" href={`/trips/${trip.id}?passengers=${passengers}`}>Select seats <ArrowRight size={16}/></Link></div>
  </article>;
}
