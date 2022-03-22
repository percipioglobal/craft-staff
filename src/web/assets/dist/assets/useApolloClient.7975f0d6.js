var f=Object.defineProperty,m=Object.defineProperties;var y=Object.getOwnPropertyDescriptors;var l=Object.getOwnPropertySymbols;var _=Object.prototype.hasOwnProperty,k=Object.prototype.propertyIsEnumerable;var i=(e,t,a)=>t in e?f(e,t,{enumerable:!0,configurable:!0,writable:!0,value:a}):e[t]=a,u=(e,t)=>{for(var a in t||(t={}))_.call(t,a)&&i(e,a,t[a]);if(l)for(var a of l(t))k.call(t,a)&&i(e,a,t[a]);return e},d=(e,t)=>m(e,y(t));import{o as w,c as p,a as r,k as v,l as o,H as x,s as L,m as $,A as C,I as b,n as B}from"./vendor.c20e18bf.js";var E=(e,t)=>{const a=e.__vccOpts||e;for(const[n,g]of t)a[n]=g;return a};const P={},q={class:"flex items-center justify-center p-4 border-b border-solid border-gray-200"},F=r("svg",{class:"animate-spin mr-3 h-5 w-5 text-indigo-900",xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24"},[r("circle",{class:"opacity-25",cx:"12",cy:"12",r:"10",stroke:"currentColor","stroke-width":"4"}),r("path",{class:"opacity-75",fill:"currentColor",d:"M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"})],-1),R=r("span",{class:"text-base"}," Loading data ... ",-1),z=[F,R];function A(e,t){return w(),p("div",q,z)}var S=E(P,[["render",A]]);const c=v("payrun",{state:()=>({queue:0,fetching:!1,logs:[]})});var h;const s=(h=window.api.cpUrl)!=null?h:"https://localhost:8003",T=e=>{const t=c();t.loadingFetched=!0,o({method:"get",url:`${s}/staff-management/pay-runs/fetch-pay-runs/${e}`}).then(()=>{t.loadingFetched=!1})},V=e=>{const t=c();t.loadingFetched=!0,o({method:"get",url:`${s}/staff-management/pay-runs/fetch-pay-run/${e}`}).then(()=>{t.loadingFetched=!1})},j=()=>{const e=c();o({method:"get",url:`${s}/staff-management/pay-runs/queue`}).then(t=>{var a;e.queue=((a=t==null?void 0:t.data)==null?void 0:a.total)?t.data.total:0})},D=e=>{const t=c();o({method:"get",url:`${s}/staff-management/pay-runs/get-logs/${e}`}).then(a=>{var n;t.logs=((n=a==null?void 0:a.data)==null?void 0:n.logs)?a.data.logs:[]})},H=async()=>{let e={value:null};return await o({method:"get",url:`${s}/staff-management/settings/get-gql-token`}).then(t=>{var a;e.value=((a=t==null?void 0:t.data)==null?void 0:a.token)?t.data.token:null}),e.value||null},I=new x({uri:"http://localhost:8001/api",credentials:"include"}),N=L(async(e,{headers:t})=>{const a=await H();return{headers:d(u({},t),{authorization:`Bearer ${a}`})}}),M=$(({graphQLErrors:e,networkError:t,operation:a,forward:n})=>{}),U=new C({cache:new b,link:B([M,N,I])});export{S as L,E as _,D as a,V as b,U as d,T as f,j as g,c as u};
//# sourceMappingURL=useApolloClient.7975f0d6.js.map
