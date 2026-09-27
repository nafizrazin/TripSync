'use client';
import { BadgeCheck, Clock3, Radar, ShieldCheck, Sparkles } from 'lucide-react';
import { useEffect, useState } from 'react';
import { SearchForm } from '@/components/SearchForm';
import { NetworkPulse } from '@/components/NetworkPulse';
import { DiscoveryCards } from '@/components/DiscoveryCards';
import { apiFetch } from '@/lib/api';
import { loadRecentSearches, type RecentSearch } from '@/lib/recent-searches';
import type { DiscoveryOverview } from '@/lib/types';

export default function HomePage(){
  const [overview,setOverview]=useState<DiscoveryOverview|null>(null);
  const [recent,setRecent]=useState<RecentSearch[]>([]);
  useEffect(()=>{
    void apiFetch<{data:DiscoveryOverview}>('/discovery/overview').then(result=>setOverview(result.data)).catch(()=>{});
    if(typeof window!=='undefined') setRecent(loadRecentSearches(window.localStorage));
  },[]);
  return <main className="home-main nova-home">
    <section className="nova-hero"><div className="aurora aurora-one"/><div className="aurora aurora-two"/><div className="nova-grid"/>
      <div className="shell nova-hero-grid"><div className="nova-hero-copy"><span className="eyebrow luminous"><Radar size={14}/> Bangladesh mobility network · live</span><h1>Your journey.<br/><em>Perfectly synchronized.</em></h1><p>Compare operators, discover smarter fares, lock your exact seat and keep every trip in one intelligent travel wallet.</p><div className="hero-proof nova-proof"><span><ShieldCheck size={17}/> Transaction-safe seats</span><span><Clock3 size={17}/> Live availability</span><span><BadgeCheck size={17}/> Performance-ranked operators</span></div></div><NetworkPulse/></div>
      <div className="shell hero-search-dock"><div className="hero-search-label"><span><Sparkles size={14}/> Plan your next journey</span><small>Live inventory · transparent fares</small></div><SearchForm/></div>
    </section>
    <section className="shell nova-stats-strip">
      <div><span className="metric-dot cyan"/><strong>{overview?.stats.active_operators ?? '—'}</strong><span>active operators</span></div>
      <div><span className="metric-dot violet"/><strong>{overview?.stats.active_routes ?? '—'}</strong><span>live route pairs</span></div>
      <div><span className="metric-dot emerald"/><strong>{overview?.stats.journeys_today ?? '—'}</strong><span>journeys today</span></div>
      <div><span className="metric-dot amber"/><strong>{overview ? Intl.NumberFormat('en-BD').format(overview.stats.available_seats) : '—'}</strong><span>seats in next 14 days</span></div>
    </section>
    <DiscoveryCards overview={overview} recent={recent}/>
    <section className="shell nova-section trust-section"><div className="nova-section-head"><div><span className="eyebrow">Built differently</span><h2>High-speed experience. Database-grade confidence.</h2></div></div><div className="feature-grid nova-feature-grid"><article><span className="feature-number">01</span><h3>Compare beyond price</h3><p>See comfort class, operator score, punctuality, amenities, duration and availability before you choose.</p></article><article><span className="feature-number">02</span><h3>Seats protected atomically</h3><p>PostgreSQL locks selected inventory while you check out, preventing two travelers from buying the same seat.</p></article><article><span className="feature-number">03</span><h3>One travel wallet</h3><p>Bookings, payments, tickets, cancellations and refunds stay synchronized around a single journey record.</p></article></div></section>
  </main>;
}
