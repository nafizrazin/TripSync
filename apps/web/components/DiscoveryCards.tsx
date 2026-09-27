import Link from 'next/link';
import { ArrowUpRight, BusFront, Clock3, Sparkles, Star, UsersRound } from 'lucide-react';
import { formatBdt } from '@/lib/booking';
import type { DiscoveryOverview } from '@/lib/types';
import type { RecentSearch } from '@/lib/recent-searches';

function nextTravelDate(): string {
  const date = new Date(Date.now() + 86400000);
  return new Intl.DateTimeFormat('en-CA',{timeZone:'Asia/Dhaka',year:'numeric',month:'2-digit',day:'2-digit'}).format(date);
}

export function DiscoveryCards({overview,recent}:{overview:DiscoveryOverview|null;recent:RecentSearch[]}) {
  const travelDate=nextTravelDate();
  return <>
    <section className="shell nova-section" id="discover">
      <div className="nova-section-head"><div><span className="eyebrow"><Sparkles size={14}/> Explore the network</span><h2>Popular journeys, live right now.</h2></div><Link href="/search" className="text-link">View all journeys <ArrowUpRight size={16}/></Link></div>
      <div className="route-card-grid">
        {(overview?.popular_routes ?? []).slice(0,6).map(route=><Link key={route.id} className="route-discovery-card" href={`/search?from=${route.origin_id}&to=${route.destination_id}&date=${travelDate}&passengers=1`}>
          <div className="route-card-top"><span>{route.origin_name}</span><span className="route-arrow">→</span><span>{route.destination_name}</span></div>
          <div className="route-card-meta"><span><Clock3 size={13}/>{route.trip_count} departures</span><span><UsersRound size={13}/>{route.available_seats} seats</span></div>
          <div className="route-card-fare"><small>from</small><strong>{formatBdt(route.min_fare)}</strong><ArrowUpRight size={17}/></div>
        </Link>)}
        {!overview && [1,2,3].map(item=><div className="route-discovery-card discovery-skeleton" key={item}/>) }
      </div>
    </section>

    <section className="shell nova-section operator-section">
      <div className="nova-section-head"><div><span className="eyebrow"><BusFront size={14}/> Trusted operators</span><h2>Travel with proven performers.</h2></div></div>
      <div className="operator-card-grid">
        {(overview?.top_operators ?? []).slice(0,6).map(operator=><article className="operator-discovery-card" key={operator.id} style={{'--operator-accent':operator.brand_color ?? '#22d3ee'} as React.CSSProperties}>
          <div className="operator-monogram">{operator.short_code ?? operator.name.slice(0,2).toUpperCase()}</div>
          <div className="operator-copy"><strong>{operator.name}</strong><span>{operator.tagline ?? 'Intercity travel partner'}</span></div>
          <div className="operator-score"><span><Star size={13} fill="currentColor"/>{Number(operator.rating).toFixed(1)}</span><small>{Number(operator.punctuality_percent).toFixed(0)}% on-time</small></div>
        </article>)}
      </div>
    </section>

    {recent.length>0 && <section className="shell nova-section recent-searches">
      <div className="nova-section-head"><div><span className="eyebrow">Continue planning</span><h2>Your recent searches.</h2></div></div>
      <div className="recent-search-grid">{recent.map(item=><Link className="recent-search-card" key={`${item.fromId}-${item.toId}`} href={`/search?from=${item.fromId}&to=${item.toId}&date=${item.date}&passengers=${item.passengers}`}><div><strong>{item.fromName} → {item.toName}</strong><span>{item.date} · {item.passengers} passenger{item.passengers===1?'':'s'}</span></div><ArrowUpRight size={17}/></Link>)}</div>
    </section>}
  </>;
}
