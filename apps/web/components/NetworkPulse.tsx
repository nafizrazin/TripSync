import { BusFront, MapPin, Radio } from 'lucide-react';

export function NetworkPulse() {
  return <div className="network-pulse" aria-hidden="true">
    <div className="network-orbit orbit-one"/><div className="network-orbit orbit-two"/>
    <div className="network-route route-a"/><div className="network-route route-b"/><div className="network-route route-c"/>
    <span className="network-node node-dhaka"><MapPin size={15}/><b>Dhaka</b></span>
    <span className="network-node node-sylhet"><MapPin size={13}/><b>Sylhet</b></span>
    <span className="network-node node-ctg"><MapPin size={13}/><b>Chattogram</b></span>
    <span className="network-vehicle"><BusFront size={16}/></span>
    <span className="network-live"><Radio size={13}/> LIVE NETWORK</span>
  </div>;
}
