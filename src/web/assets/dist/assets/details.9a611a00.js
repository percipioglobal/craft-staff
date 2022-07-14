import{d as x,o as n,a as c,b as t,t as r,u as o,g as M,D as G,E as Y,f as _,e as u,F as U,r as K,s as Q,i as W,G as $,c as q,h as J,p as X}from"./vue.esm-bundler.737af873.js";import{a as H,b as Z,L as tt,u as et,f as st,c as ot,d as nt,D as at}from"./useApolloClient.71b035f7.js";import{f as b,_ as rt,P as it}from"./status--synced.8524e11b.js";import{_ as lt}from"./plugin-vue_export-helper.21dcd24c.js";const dt={class:"mt-5 grid grid-cols-2 md:grid-cols-3 gap-3 lg:gap-5 lg:grid-cols-5"},ct={class:"px-4 py-5 bg-gray-200 shadow rounded-lg overflow-hidden sm:p-6"},mt=t("dt",{class:"text-sm font-medium text-gray-500 truncate text-center"}," Payment Date ",-1),ut={class:"mt-1 text-2xl font-semibold text-gray-900 text-center"},pt={class:"px-4 py-5 bg-gray-200 shadow rounded-lg overflow-hidden sm:p-6"},xt=t("dt",{class:"text-sm font-medium text-gray-500 truncate text-center"}," Total pay ",-1),gt={class:"mt-1 text-2xl font-semibold text-gray-900 text-center"},yt={class:"px-4 py-5 bg-gray-200 shadow rounded-lg overflow-hidden sm:p-6"},ft=t("dt",{class:"text-sm font-medium text-gray-500 truncate text-center"}," Tax ",-1),ht={class:"mt-1 text-2xl font-semibold text-gray-900 text-center"},_t={class:"px-4 py-5 bg-gray-200 shadow rounded-lg overflow-hidden sm:p-6"},vt=t("dt",{class:"text-sm font-medium text-gray-500 truncate text-center"}," Employee NI ",-1),wt={class:"mt-1 text-2xl font-semibold text-gray-900 text-center"},bt={class:"px-4 py-5 bg-gray-200 shadow rounded-lg overflow-hidden sm:p-6"},$t=t("dt",{class:"text-sm font-medium text-gray-500 truncate text-center"}," Employer NI ",-1),kt={class:"mt-1 text-2xl font-semibold text-gray-900 text-center"},Ct=x({__name:"stats--payrun",props:{payrun:Object},setup(s){return(i,d)=>{var e,l,a,p,v,w,g,y,m;return n(),c("dl",dt,[t("div",ct,[mt,t("dd",ut,r((e=s.payrun)==null?void 0:e.paymentDate),1)]),t("div",pt,[xt,t("dd",gt," \xA3 "+r(o(b)((a=(l=s.payrun)==null?void 0:l.totals)==null?void 0:a.gross)),1)]),t("div",yt,[ft,t("dd",ht," \xA3 "+r(o(b)((v=(p=s.payrun)==null?void 0:p.totals)==null?void 0:v.tax)),1)]),t("div",_t,[vt,t("dd",wt," \xA3 "+r(o(b)((g=(w=s.payrun)==null?void 0:w.totals)==null?void 0:g.employeeNi)),1)]),t("div",bt,[$t,t("dd",kt," \xA3 "+r(o(b)((m=(y=s.payrun)==null?void 0:y.totals)==null?void 0:m.employerNi)),1)])])}}}),Nt={},Rt={class:"mt-5 grid grid-cols-2 md:grid-cols-3 gap-3 lg:gap-5 lg:grid-cols-5"},zt=M('<div class="px-4 py-5 bg-gray-200 shadow rounded-lg overflow-hidden sm:p-6"><dt class="text-sm font-medium text-gray-500 truncate text-center"> Payment Date </dt><dd class="mt-1 text-2xl font-semibold text-gray-900 text-center"><svg class="animate-spin mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></dd></div><div class="px-4 py-5 bg-gray-200 shadow rounded-lg overflow-hidden sm:p-6"><dt class="text-sm font-medium text-gray-500 truncate text-center"> Total pay </dt><dd class="mt-1 text-2xl font-semibold text-gray-900 text-center"><svg class="animate-spin mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></dd></div><div class="px-4 py-5 bg-gray-200 shadow rounded-lg overflow-hidden sm:p-6"><dt class="text-sm font-medium text-gray-500 truncate text-center"> Tax </dt><dd class="mt-1 text-2xl font-semibold text-gray-900 text-center"><svg class="animate-spin mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></dd></div><div class="px-4 py-5 bg-gray-200 shadow rounded-lg overflow-hidden sm:p-6"><dt class="text-sm font-medium text-gray-500 truncate text-center"> Employee NI </dt><dd class="mt-1 text-2xl font-semibold text-gray-900 text-center"><svg class="animate-spin mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></dd></div><div class="px-4 py-5 bg-gray-200 shadow rounded-lg overflow-hidden sm:p-6"><dt class="text-sm font-medium text-gray-500 truncate text-center"> Employer NI </dt><dd class="mt-1 text-2xl font-semibold text-gray-900 text-center"><svg class="animate-spin mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></dd></div>',5),At=[zt];function St(s,i){return n(),c("dl",Rt,At)}var Bt=lt(Nt,[["render",St]]);const Dt={class:"fixed z-10 w-full h-full overflow-y-auto top-0 left-0 bg-gray-500 bg-opacity-75 transition-opacity","aria-labelledby":"modal-title",role:"dialog","aria-modal":"true"},Vt={class:"flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"},Pt=t("span",{class:"hidden sm:inline-block sm:align-middle sm:h-screen","aria-hidden":"true"},"\u200B",-1),Tt={class:"relative inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-sm sm:w-full sm:p-6"},It=t("div",{class:"mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100"},[t("svg",{class:"mt-2 h-6 w-6 text-red-600",xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor","aria-hidden":"true"},[t("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"})])],-1),Lt={class:"mt-3 text-center sm:mt-5"},jt=t("h3",{id:"modal-title",class:"text-lg leading-6 font-medium text-gray-900"}," An error has occured ",-1),Et={class:"mt-2"},Ft={class:"text-sm text-gray-500"},Mt={class:"mt-5 sm:mt-6"},Ut=x({__name:"banner--error",emits:{close:Boolean},setup(s,{emit:i}){return(d,e)=>(n(),c("div",Dt,[t("div",Vt,[Pt,t("div",Tt,[t("div",null,[It,t("div",Lt,[jt,t("div",Et,[t("p",Ft,r(d.error),1)])])]),t("div",Mt,[t("button",{type:"button",class:"inline-flex justify-center w-full rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm",onClick:e[0]||(e[0]=l=>i("close"))}," Go back to the pay run ")])])])]))}}),Ht={method:"post","accept-charset":"UTF-8",enctype:"multipart/form-data",class:"inline-flex"},Ot=["value"],Gt=t("input",{type:"hidden",name:"action",value:"staff-management/pay-run/import"},null,-1),Yt=["value"],Kt=["value"],Qt=t("input",{id:"attendee-csv-file",type:"file",class:"fixed top-full",name:"file",accept:".csv",onchange:"this.form.submit();"},null,-1),Wt=t("label",{for:"attendee-csv-file",class:"cursor-pointer inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto"},"Upload CSV To Staffology",-1),qt=x({__name:"form--import",props:{payrun:Object},setup(s){const i=window.api.csrf;return(d,e)=>{var l,a;return n(),c("form",Ht,[t("input",{type:"hidden",name:"CRAFT_CSRF_TOKEN",value:o(i)},null,8,Ot),Gt,t("input",{type:"hidden",name:"payRunId",value:(l=s.payrun)==null?void 0:l.id},null,8,Yt),t("input",{type:"hidden",name:"employerId",value:(a=s.payrun)==null?void 0:a.employerId},null,8,Kt),Qt,Wt])}}}),Jt={class:"grid grid-cols-7 border-b border-solid border-gray-200"},Xt={class:"col-span-2 whitespace-nowrap px-3 py-4 flex text-sm text-gray-500"},Zt={class:"whitespace-nowrap px-3 py-4 flex text-sm text-gray-500"},te={class:"col-span-2 whitespace-nowrap px-3 py-4 flex text-sm text-gray-500"},ee={class:"whitespace-nowrap px-3 py-4 flex text-sm text-gray-500"},se={class:"items-center px-3 py-2 flex"},oe=x({__name:"listitem--log",props:{log:Object},setup(s){const i=e=>(e==null?void 0:e.firstName)||(e==null?void 0:e.lastName)?`${e.firstName} ${e.lastName}`:e.username,d=e=>{var a;const l={Succeeded:"bg-emerald-400 text-emerald-900",Failed:"bg-red-400 text-red-900",default:"bg-orange-400 text-orange-900"};return(a=l[e.status])!=null?a:l.default};return(e,l)=>(n(),c("div",Jt,[t("div",Xt,r(s.log.filename),1),t("div",Zt,r(s.log.rowCount),1),t("div",te,r(i(s.log)),1),t("div",ee,r(s.log.dateCreated),1),t("div",se,[t("div",{class:G(["whitespace-nowrap rounded-full text-xs inline-block px-3 py-1 mb-0 font-bold",d(s.log)])},r(s.log.status?s.log.status:"Unknown"),3)])]))}}),ne={class:"mt-8 flex flex-col w-full"},ae={class:"-my-2 overflow-x-auto w-full"},re={class:"inline-block min-w-full py-2 align-middle"},ie={class:"overflow-hidden border border-solid border-gray-300 md:rounded-lg"},le=M('<div class="grid grid-cols-7 border-b border-solid border-gray-300"><div class="col-span-2 py-3 px-3 text-left text-sm font-semibold text-gray-900"> Filename </div><div class="py-3 px-3 text-left text-sm font-semibold text-gray-900"> Row count </div><div class="col-span-2 px-3 py-3.5 text-left text-sm font-semibold text-gray-900"> Uploaded By </div><div class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"> Uploaded </div><div class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"> Status </div></div>',1),de={key:1,class:"grid grid-cols-7 border-b border-solid border-gray-200 py-4 px-3 text-center"},ce=t("div",{class:"col-span-7"}," There are currently no logs for this pay run ",-1),me=[ce],ue=x({__name:"list--logs",props:{payrun:String},setup(s){const i=s,d=H();return Y(()=>{i.payrun&&Z(i.payrun)}),(e,l)=>(n(),c("div",ne,[t("div",ae,[t("div",re,[t("div",ie,[le,s.payrun?u("",!0):(n(),_(tt,{key:0})),s.payrun&&o(d).logs.length===0?(n(),c("div",de,me)):u("",!0),(n(!0),c(U,null,K(o(d).logs,a=>(n(),_(oe,{key:a.id,log:a},null,8,["log"]))),128))])])])]))}}),pe={class:"md:flex items-start"},xe={class:"flex-grow pr-4",style:{"margin-bottom":"0"}},ge={class:"flex items-center"},ye=["href"],fe={class:"ml-2 text-xl font-semibold text-gray-900"},he=t("p",{class:"mt-2 text-sm text-gray-700"}," Download the current pay run CSV using the Download button below. When uploading, ensure table headings and the file format (CSV) remain unchanged. ",-1),_e={class:"mt-4 md:mt-0 flex",style:{"margin-bottom":"0"}},ve=["disabled"],we=t("span",null,"Refresh Pay Run",-1),be={key:0,class:"animate-spin ml-1 h-3 w-3 text-white",xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24",style:{"margin-bottom":"0"}},$e=t("circle",{class:"opacity-25",cx:"12",cy:"12",r:"10",stroke:"currentColor","stroke-width":"4"},null,-1),ke=t("path",{class:"opacity-75",fill:"currentColor",d:"M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"},null,-1),Ce=[$e,ke],Ne={class:"mt-8"},Re={class:"mt-8 flex"},ze={class:"sm:flex-auto",style:{"margin-bottom":"0"}},Ae=t("h2",null,"Uploaded Pay Run Entries",-1),Se={class:"mt-4 md:mt-0 text-xs inline-flex mr-2 flex-grow",style:{"margin-bottom":"0"}},Be={key:1,class:"mt-4 sm:mt-0 sm:ml-16 sm:flex-none space-x-2",style:{"margin-bottom":"0"}},De=x({__name:"PayRunDetail",setup(s){var g;const i=window.location.href.split("/").pop(),d=Q({current:window.location.href.split("/").at(-2)}),{result:e,loading:l}=et(it,{id:i},{pollInterval:5e3}),a=H(),p=W((g=window==null?void 0:window.validation)==null?void 0:g.error),v=y=>{const m=`/admin/staff-management/pay-runs/download-template/${y}`;window.open(m)},w=()=>{p.value=""};return(y,m)=>{var k,C,N,R,z,A,S,B,D,V,P,T,I,L,j,E,F;return n(),c(U,null,[t("div",pe,[t("div",xe,[t("div",ge,[t("a",{href:`/admin/staff-management/pay-runs/${(C=(k=o(e))==null?void 0:k.payrun)==null?void 0:C.employerId}/${d.current}`,title:"Go back to overview",class:"inline-flex no-underline items-center px-2.5 py-1.5 rounded-full text-sm text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500",style:{"margin-bottom":"0"}},"\u2190",8,ye),t("h1",fe,r((R=(N=o(e))==null?void 0:N.payrun)==null?void 0:R.taxYear)+" / "+r((A=(z=o(e))==null?void 0:z.payrun)==null?void 0:A.period),1)]),he]),t("div",_e,[$(rt,{date:(B=(S=o(e))==null?void 0:S.payrun)==null?void 0:B.dateSynced},null,8,["date"]),t("button",{disabled:o(a).loadingFetched,class:"cursor-pointer inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 disabled:bg-indigo-400 disabled:cursor-not-allowed px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto",style:{"margin-bottom":"0"},onClick:m[0]||(m[0]=O=>{var f,h;return o(st)((h=(f=o(e))==null?void 0:f.payrun)==null?void 0:h.id,d.current)})},[we,o(a).loadingFetched?(n(),c("svg",be,Ce)):u("",!0)],8,ve)])]),t("div",Ne,[(D=o(e))!=null&&D.payrun?u("",!0):(n(),_(Bt,{key:0})),(V=o(e))!=null&&V.payrun?(n(),_(Ct,{key:1,payrun:(P=o(e))==null?void 0:P.payrun},null,8,["payrun"])):u("",!0)]),t("div",Re,[p.value?(n(),_(Ut,{key:0,error:p.value,onClose:w},null,8,["error"])):u("",!0),t("div",ze,[Ae,t("span",Se," Last Synced: "+r((I=(T=o(e))==null?void 0:T.payrun)==null?void 0:I.dateSynced),1)]),(L=o(e))!=null&&L.payrun?(n(),c("div",Be,[t("button",{class:"cursor-pointer inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500",onClick:m[1]||(m[1]=O=>{var f,h;return v((h=(f=o(e))==null?void 0:f.payrun)==null?void 0:h.id)})}," Download Latest Pay Run Template "),$(qt,{payrun:(j=o(e))==null?void 0:j.payrun},null,8,["payrun"])])):u("",!0),$(ue,{payrun:(F=(E=o(e))==null?void 0:E.payrun)==null?void 0:F.id},null,8,["payrun"])])],64)}}}),Ve=async()=>{const s=q({setup(){X(at,nt)},render:()=>J(De)});return s.use(ot()),s.mount("#detail-container")};Ve().then(()=>{console.log()});
//# sourceMappingURL=details.9a611a00.js.map
