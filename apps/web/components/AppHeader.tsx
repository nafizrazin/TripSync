'use client';
import Link from 'next/link';
import { BusFront, Menu, Radar, Ticket, UserRound } from 'lucide-react';
import { useState } from 'react';
export function AppHeader() {
  const [open,setOpen]=useState(false);
  return <header className="site-header nova-header"><div className="shell nav-shell">
    <Link className="brand nova-brand" href="/"><span className="brand-mark"><BusFront size={21}/></span><span>TripSync</span><small><Radar size={10}/> LIVE</small></Link>
    <button className="mobile-menu" onClick={()=>setOpen(!open)} aria-label="Toggle navigation"><Menu size={21}/></button>
    <nav className={open?'nav-links open':'nav-links'}>
      <Link href="/search">Discover</Link><Link href="/bookings"><Ticket size={17}/> My trips</Link><Link href="/admin">Control</Link><Link className="nav-signin" href="/login"><UserRound size={17}/> Sign in</Link>
    </nav>
  </div></header>;
}
