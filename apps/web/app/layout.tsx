import type { Metadata } from 'next';
import './globals.css';
import { AppHeader } from '@/components/AppHeader';

export const metadata: Metadata={title:{default:'TripSync',template:'%s · TripSync'},description:'Futuristic intercity travel discovery and booking for Bangladesh.'};
export default function RootLayout({children}:Readonly<{children:React.ReactNode}>){return <html lang="en"><body><AppHeader/>{children}<footer className="footer nova-footer"><div className="shell footer-inner"><div><strong>TripSync</strong><span>Intercity travel, synchronized.</span></div><span>© 2026 · Built for safer, smarter journeys.</span></div></footer></body></html>}
