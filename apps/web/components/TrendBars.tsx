export function TrendBars({data}:{data:Array<{date:string;total:number}>}){
  const max=Math.max(1,...data.map(item=>item.total));
  return <div className="trend-bars" aria-label="Seven day revenue trend">{data.map(item=><div className="trend-column" key={item.date} title={`${item.date}: ৳${item.total.toLocaleString('en-BD')}`}><span className="trend-value">৳{Math.round(item.total/1000)}k</span><div className="trend-track"><i style={{height:`${Math.max(5,(item.total/max)*100)}%`}}/></div><small>{new Intl.DateTimeFormat('en-BD',{weekday:'short',timeZone:'Asia/Dhaka'}).format(new Date(`${item.date}T00:00:00+06:00`))}</small></div>)}</div>;
}
