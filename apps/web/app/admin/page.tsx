'use client';
import Link from 'next/link';
import { useEffect,useState } from 'react';
import { Activity,ArrowUpRight,CalendarClock,CircleDollarSign,Gauge,HeartPulse,ReceiptText,Route,ShieldCheck,UsersRound } from 'lucide-react';
import { apiFetch,ApiError } from '@/lib/api';
import { formatBdt } from '@/lib/booking';
import { TrendBars } from '@/components/TrendBars';

type Overview={
  metrics:{gross_booking_value:string;confirmed_bookings:number;bookings_today:number;upcoming_trips:number;occupancy_percent:number;successful_payments:number};
  recent_bookings:Array<{id:string;reference:string;customer:string;route:string;status:string;amount:string;created_at:string}>;
  upcoming_trips:Array<{id:string;public_id:string;route:string;operator:string;departure_at:string;booking_status:string}>;
  revenue_trend:Array<{date:string;total:number}>;
  top_routes:Array<{id:string;route:string;bookings:number;revenue:number}>;
  operator_performance:Array<{id:string;name:string;rating:number;punctuality_percent:number;brand_color:string|null;trips:number;bookings:number}>;
  booking_statuses:Array<{status:string;total:number}>;
  system_health:{api:string;database:string;redis:string;queue_backlog:number;failed_jobs:number};
};

export default function AdminPage(){
  const [data,setData]=useState<Overview|null>(null);const [error,setError]=useState('');
  useEffect(()=>{void apiFetch<{data:Overview}>('/admin/overview').then(r=>setData(r.data)).catch(e=>setError(e instanceof ApiError?e.status===403?'Your account does not have admin access.':e.status===401?'Sign in with an admin account to continue.':e.message:'Unable to load operations data.'));},[]);
  if(error)return <main className="page-main"><div className="shell narrow"><div className="notice error">{error}</div><Link className="primary-button small" href="/login">Sign in</Link></div></main>;
  if(!data)return <main className="page-main admin-page nova-control"><div className="shell"><div className="trip-skeleton"/></div></main>;
  const cards=[['Gross booking value',formatBdt(data.metrics.gross_booking_value),CircleDollarSign],['Confirmed bookings',String(data.metrics.confirmed_bookings),ReceiptText],['Upcoming trips',String(data.metrics.upcoming_trips),CalendarClock],['7-day occupancy',`${data.metrics.occupancy_percent}%`,Gauge]] as const;
  const maxRoute=Math.max(1,...data.top_routes.map(item=>item.bookings));
  return <main className="admin-page nova-control"><div className="shell">
    <div className="admin-title nova-admin-title"><div><span className="eyebrow"><Activity size={14}/> Network intelligence</span><h1>TripSync Control</h1><p>Commercial performance, dispatch signals and platform health across the live mobility network.</p></div><div className="admin-title-stat"><span>Bookings today</span><strong>{data.metrics.bookings_today}</strong><small>{data.metrics.successful_payments} successful payments</small></div></div>
    <div className="metric-grid nova-metric-grid">{cards.map(([label,value,Icon])=><article className="metric-card" key={label}><span className="metric-icon"><Icon size={19}/></span><span>{label}</span><strong>{value}</strong><small>Live platform aggregate</small></article>)}</div>
    <div className="control-grid">
      <section className="admin-panel revenue-panel"><div className="panel-head"><div><span className="eyebrow">Commercial pulse</span><h2>7-day revenue</h2></div><CircleDollarSign size={20}/></div><TrendBars data={data.revenue_trend}/></section>
      <section className="admin-panel health-panel"><div className="panel-head"><div><span className="eyebrow">Infrastructure</span><h2>System health</h2></div><HeartPulse size={20}/></div><div className="health-grid">{Object.entries(data.system_health).map(([key,value])=><div key={key}><span className={typeof value==='string'&&value==='degraded'?'health-dot degraded':'health-dot'}/><span>{key.replaceAll('_',' ')}</span><strong>{String(value)}</strong></div>)}</div></section>
    </div>
    <div className="admin-grid nova-admin-grid"><section className="admin-panel"><div className="panel-head"><div><span className="eyebrow"><Route size={13}/> Demand</span><h2>Top routes</h2></div></div><div className="route-performance-list">{data.top_routes.map(route=><div key={route.id}><div><strong>{route.route}</strong><span>{route.bookings} bookings · {formatBdt(route.revenue)}</span></div><i><b style={{width:`${(route.bookings/maxRoute)*100}%`}}/></i></div>)}</div></section><section className="admin-panel"><div className="panel-head"><div><span className="eyebrow"><ShieldCheck size={13}/> Quality</span><h2>Operator performance</h2></div></div><div className="operator-performance-list">{data.operator_performance.map(operator=><div key={operator.id}><span className="operator-health-mark" style={{background:operator.brand_color??'#22d3ee'}}/><div><strong>{operator.name}</strong><span>★ {operator.rating.toFixed(1)} · {operator.punctuality_percent.toFixed(0)}% on-time</span></div><small>{operator.bookings} bookings</small></div>)}</div></section></div>
    <div className="admin-grid"><section className="admin-panel"><div className="panel-head"><div><span className="eyebrow">Commercial</span><h2>Recent bookings</h2></div><UsersRound size={20}/></div><div className="data-list">{data.recent_bookings.map(b=><div className="data-row" key={b.id}><div><strong>{b.reference}</strong><span>{b.customer} · {b.route}</span></div><div><strong>{formatBdt(b.amount)}</strong><span className={`status-text ${b.status}`}>{b.status.replaceAll('_',' ')}</span></div></div>)}</div></section><section className="admin-panel"><div className="panel-head"><div><span className="eyebrow">Dispatch</span><h2>Upcoming trips</h2></div><CalendarClock size={20}/></div><div className="data-list">{data.upcoming_trips.map(t=><div className="data-row" key={t.id}><div><strong>{t.route}</strong><span>{t.operator} · {new Intl.DateTimeFormat('en-BD',{dateStyle:'medium',timeStyle:'short',timeZone:'Asia/Dhaka'}).format(new Date(t.departure_at))}</span></div><span className="row-action">{t.booking_status}<ArrowUpRight size={14}/></span></div>)}</div></section></div>
  </div></main>;
}
