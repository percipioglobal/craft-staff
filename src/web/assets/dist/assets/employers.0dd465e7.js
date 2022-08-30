import{d,o as r,c as y,b as t,t as o,e as c,f as x,g as m,F as u,u as i,i as f,a as g,h,p as v}from"./vue.esm-bundler.d0e0ddeb.js";import{u as b,d as _,D as w}from"./useApolloClient.ce93ded0.js";import{E as $}from"./employers.e54832ef.js";import{L as k}from"./listitem--loading.c74c18a7.js";const C=["href","title"],E={class:"flex items-center col-span-2 whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-indigo-800 sm:pl-6"},L={class:"object-cover object-center w-6 h-6 rounded-full overflow-hidden mb-0"},R=["src"],P={style:{"margin-bottom":"0"}},S={class:"flex items-center whitespace-nowrap px-3 py-4 text-sm text-gray-500"},B={class:"flex items-center whitespace-nowrap px-3 py-4 text-sm text-gray-500"},N={class:"flex items-center whitespace-nowrap px-3 py-4 text-sm text-gray-500"},j={class:"flex items-center whitespace-nowrap px-3 py-4 text-sm text-gray-500"},D=d({__name:"listitem--employer",props:{employer:null},setup(e){return(l,n)=>{var s,a,p;return e.employer?(r(),y("a",{key:0,href:`/admin/staff-management/pay-runs/${e.employer.id}/${(s=e.employer.currentPayRun)==null?void 0:s.taxYear}`,title:`Go to pay runs of ${e.employer.name}`,class:"grid grid-cols-6 border-b border-solid border-gray-200 no-underline hover:bg-gray-200"},[t("div",E,[t("div",L,[t("img",{src:e.employer.logoUrl,class:"w-full h-full"},null,8,R)]),t("span",P,o(e.employer.name),1)]),t("div",S,o(e.employer.crn?e.employer.crn:"-"),1),t("div",B,o(e.employer.employeeCount),1),t("div",N,o((a=e.employer.currentPayRun)==null?void 0:a.taxYear)+" / "+o((p=e.employer.currentPayRun)==null?void 0:p.period),1),t("div",j,o(e.employer.dateSynced),1)],8,C)):c("",!0)}}}),V=d({__name:"list--employers",props:{employers:Object},setup(e){return(l,n)=>(r(!0),y(u,null,x(e.employers,s=>(r(),m(D,{key:s.id,employer:s},null,8,["employer"]))),128))}}),Y=t("div",{class:"sm:flex"},[t("div",{class:"sm:flex-auto"},[t("h1",{class:"text-xl font-semibold text-gray-900"}," Employers "),t("p",{class:"mt-2 text-sm text-gray-700"}," Select a company below to begin bulk pay run management. ")])],-1),A={class:"mt-8 flex flex-col w-full"},F={class:"-my-2 overflow-x-auto w-full"},O={class:"inline-block min-w-full py-2 align-middle"},G={class:"overflow-hidden shadow border border-solid border-gray-300 md:rounded-lg"},M=f('<div class="grid grid-cols-6 border-b border-solid border-gray-300"><div class="col-span-2 py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6"> Company </div><div class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"> CRN </div><div class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"> Employee count </div><div class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"> Current Pay Run </div><div class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"> Last synced </div></div>',1),Q=d({__name:"Employers",setup(e){const{result:l,loading:n}=b($);return(s,a)=>(r(),y(u,null,[Y,t("div",A,[t("div",F,[t("div",O,[t("div",G,[M,i(n)?(r(),m(k,{key:0})):c("",!0),i(l)?(r(),m(V,{key:1,employers:i(l).employers},null,8,["employers"])):c("",!0)])])])])],64))}});console.log("employer test");const U=async()=>g({setup(){v(w,_)},render:()=>h(Q)}).mount("#employer-container");U().then(()=>{console.log()});
//# sourceMappingURL=employers.0dd465e7.js.map
