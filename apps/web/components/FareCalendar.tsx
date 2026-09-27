'use client';
import { CalendarDays } from 'lucide-react';
import { formatBdt } from '@/lib/booking';
import type { FareCalendarDay } from '@/lib/types';

export function FareCalendar({days,currentDate,onSelect}:{days:FareCalendarDay[];currentDate:string;onSelect:(date:string)=>void}){
  return <div className="fare-calendar" aria-label="Fare calendar">
    <div className="fare-calendar-title"><CalendarDays size={16}/><span>Flexible dates</span></div>
    <div className="fare-day-strip">{days.map(day=>{
      const date=new Date(`${day.date}T00:00:00+06:00`);
      const active=day.date===currentDate;
      return <button type="button" onClick={()=>onSelect(day.date)} className={active?'fare-day active':'fare-day'} key={day.date}>
        <span>{new Intl.DateTimeFormat('en-BD',{weekday:'short',timeZone:'Asia/Dhaka'}).format(date)}</span>
        <strong>{new Intl.DateTimeFormat('en-BD',{day:'2-digit',month:'short',timeZone:'Asia/Dhaka'}).format(date)}</strong>
        <small>{day.min_fare?formatBdt(day.min_fare):'No trips'}</small>
      </button>;
    })}</div>
  </div>;
}
