import{d as _,o as t,b as s,u as l,f as p,F as u,i as g,e as o,t as r,l as y,h as w,p as b}from"./vue.esm-bundler.fd9c3e39.js";import{u as x,d as V,D as C}from"./useApolloClient.83c3f34b.js";import{b as k}from"./variants.95f266f4.js";import"./index.734efbd9.js";const A={key:0,class:"animate-spin mr-3 h-5 w-5 text-indigo-900",xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24"},B=o("circle",{class:"opacity-25",cx:"12",cy:"12",r:"10",stroke:"currentColor","stroke-width":"4"},null,-1),I=o("path",{class:"opacity-75",fill:"currentColor",d:"M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"},null,-1),D=[B,I],N={key:1,class:"grid sm:grid-cols-2 md:grid-cols-3"},S=["href"],z={class:"text-base mb-1 pt-0 w-full",style:{"margin-right":"0!important"}},F={class:"font-light text-4xl text-indigo-900 mt-2 mb-4 w-full",style:{"margin-right":"0!important"}},R=_({__name:"Variants",setup(h){const{result:n,loading:f}=x(k,{policyId:parseInt(policyId)});return(H,L)=>{var i;return t(),s(u,null,[l(f)?(t(),s("svg",A,D)):p("",!0),l(n)?(t(),s("div",N,[(t(!0),s(u,null,g((i=l(n))==null?void 0:i.BenefitVariants,e=>{var a,c,d,m;return t(),s("a",{href:`/admin/staff-management/benefits/variant/${e.id}`,class:"bg-white shadow rounded-lg overflow-hidden no-underline p-4"},[o("h2",z,r(e.name),1),o("h3",F," \xA3 "+r((c=(a=e==null?void 0:e.totalRewardsStatement)==null?void 0:a.monetaryValue)!=null?c:"-"),1),o("p",null,r((m=(d=e==null?void 0:e.employees)==null?void 0:d.length)!=null?m:0)+" employees attached",1)],8,S)}),256))])):p("",!0)],64)}}}),E=async()=>y({setup(){b(C,V)},render:()=>w(R)}).mount("#benefit-variants-container");E().then(()=>{console.log()});
//# sourceMappingURL=benefitVariants.89894c28.js.map
