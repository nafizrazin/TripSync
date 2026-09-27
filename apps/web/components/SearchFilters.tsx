'use client';
import { SlidersHorizontal, Wifi, X } from 'lucide-react';
import type { DiscoveryFilters,DeparturePeriod } from '@/lib/trip-discovery';
import type { Trip } from '@/lib/types';

function unique(values:string[]):string[]{return [...new Set(values)].sort();}

export function SearchFilters({trips,filters,onChange}:{trips:Trip[];filters:DiscoveryFilters;onChange:(filters:DiscoveryFilters)=>void}){
  const operators=[...new Map(trips.map(t=>[t.bus.operator.id,t.bus.operator])).values()].sort((a,b)=>a.name.localeCompare(b.name));
  const busTypes=unique(trips.map(t=>t.bus.type));
  const amenities=unique(trips.flatMap(t=>t.bus.amenities));
  const maxPossible=Math.ceil(Math.max(0,...trips.map(t=>Number(t.base_fare)))/100)*100;
  const toggle=(key:'operators'|'busTypes'|'amenities',value:string)=>{
    const current=filters[key];
    onChange({...filters,[key]:current.includes(value)?current.filter(item=>item!==value):[...current,value]});
  };
  const togglePeriod=(value:DeparturePeriod)=>onChange({...filters,periods:filters.periods.includes(value)?filters.periods.filter(item=>item!==value):[...filters.periods,value]});
  const reset=()=>onChange({operators:[],busTypes:[],periods:[],amenities:[],maxFare:null,minSeats:filters.minSeats});
  return <aside className="search-filter-panel">
    <div className="filter-head"><span><SlidersHorizontal size={16}/> Filters</span><button type="button" onClick={reset}><X size={14}/> Reset</button></div>
    <div className="filter-group"><strong>Departure</strong><div className="filter-chip-grid">{(['morning','afternoon','evening','night'] as DeparturePeriod[]).map(period=><button type="button" key={period} onClick={()=>togglePeriod(period)} className={filters.periods.includes(period)?'filter-chip active':'filter-chip'}>{period}</button>)}</div></div>
    <div className="filter-group"><strong>Operator</strong>{operators.map(operator=><label className="filter-check" key={operator.id}><input type="checkbox" checked={filters.operators.includes(operator.id)} onChange={()=>toggle('operators',operator.id)}/><span>{operator.name}</span><small>★ {Number(operator.rating).toFixed(1)}</small></label>)}</div>
    <div className="filter-group"><strong>Coach type</strong><div className="filter-chip-grid">{busTypes.map(type=><button type="button" className={filters.busTypes.includes(type)?'filter-chip active':'filter-chip'} onClick={()=>toggle('busTypes',type)} key={type}>{type.replaceAll('_',' ')}</button>)}</div></div>
    <div className="filter-group"><strong><Wifi size={14}/> Amenities</strong>{amenities.slice(0,8).map(item=><label className="filter-check" key={item}><input type="checkbox" checked={filters.amenities.includes(item)} onChange={()=>toggle('amenities',item)}/><span>{item}</span></label>)}</div>
    {maxPossible>0&&<div className="filter-group"><strong>Maximum fare</strong><input className="filter-range" type="range" min="400" max={maxPossible} step="50" value={filters.maxFare??maxPossible} onChange={e=>onChange({...filters,maxFare:Number(e.target.value)===maxPossible?null:Number(e.target.value)})}/><span className="filter-range-value">Up to ৳{Intl.NumberFormat('en-BD').format(filters.maxFare??maxPossible)}</span></div>}
  </aside>;
}
