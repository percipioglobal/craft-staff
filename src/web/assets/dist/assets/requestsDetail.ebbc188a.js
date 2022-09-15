import{d as h,a as m,o as r,b as n,e,u as f,t as w,f as a,F as _,I as k,l as C,h as D,p as q}from"./vue.esm-bundler.fd9c3e39.js";import{a as S,d as A,D as B}from"./useApolloClient.83c3f34b.js";import{U as z}from"./requests.bb90d423.js";import"./index.734efbd9.js";const E=e("span",{class:"text-xs font-bold text-gray-400 block mb-2"},"Note",-1),M=e("textarea",{class:"block w-full bg-gray-50 p-4 rounded-lg border border-solid border-gray-200 box-border mb-6",name:"note",placeholder:"Type a note as additional information when approving or declining"},null,-1),V={key:0,class:"bg-red-100 mb-0 flex-grow p-2 rounded-md mb-6"},j={class:"text-right mb-0 justify-end"},I={key:2,class:"mb-0 animate-spin ml-1 h-5 w-5 text-gray-500 mb-0",xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24"},N=e("circle",{class:"opacity-25",cx:"12",cy:"12",r:"10",stroke:"currentColor","stroke-width":"4"},null,-1),T=e("path",{class:"opacity-75",fill:"currentColor",d:"M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"},null,-1),F=[N,T],R={key:0,class:"relative z-10","aria-labelledby":"modal-title",role:"dialog","aria-modal":"true"},U=e("div",{class:"fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"},null,-1),L={class:"fixed z-10 inset-0 overflow-y-auto"},H={class:"flex items-end sm:items-center justify-center min-h-full p-4 text-center sm:p-0"},P={class:"relative bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full sm:p-6"},Q=k('<div class="sm:flex sm:items-start"><div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10"><svg class="h-6 w-6 text-red-600 mb-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg></div><div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left"><h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Confirmation on approval</h3><div class="mt-2"><p class="text-sm text-gray-500">Are you sure you want to approve this request? By approving, the data in Staffology will be updated. This action cannot be undone.</p></div></div></div>',1),G={class:"mt-5 sm:mt-4 sm:flex sm:flex-row-reverse items-center"},J=h({__name:"RequestsDetail",setup(g){const i=m(null),l=m(!1),d=m(!1),{mutate:v,error:c,onDone:b,onError:x}=S(z);x(()=>{l.value=!1}),b(s=>{window.location.reload(!1)});const y=s=>{s.preventDefault()},u=(s,t)=>{s.preventDefault();const o=new FormData(i.value);l.value=!0,d.value=!1,v({id:parseInt(window.request.request.id),adminId:parseInt(window.request.admin),status:t,note:o.get("note")})},p=(s,t)=>{s.preventDefault(),d.value=t};return(s,t)=>(r(),n(_,null,[E,e("form",{onSubmit:y,ref_key:"form",ref:i},[M,f(c)?(r(),n("div",V,[e("p",null,"Staffology "+w(f(c)),1)])):a("",!0),e("div",j,[l.value?a("",!0):(r(),n("button",{key:0,onClick:t[0]||(t[0]=o=>u(o,"declined")),class:"cursor-pointer inline-block bg-red-300 text-red-900 font-bold mr-2 py-2 px-3 text-sm rounded-lg cursor-pointer"},"Decline")),l.value?a("",!0):(r(),n("button",{key:1,onClick:t[1]||(t[1]=o=>p(o,!0)),class:"cursor-pointer inline-block bg-emerald-300 text-emerald-900 font-bold mr-2 py-2 px-3 text-sm rounded-lg cursor-pointer"},"Approve")),l.value?(r(),n("svg",I,F)):a("",!0)])],544),d.value?(r(),n("div",R,[U,e("div",L,[e("div",H,[e("div",P,[Q,e("div",G,[e("button",{type:"button",onClick:t[2]||(t[2]=o=>u(o,"approved")),class:"mt-3 cursor-pointer inline-block bg-emerald-300 text-emerald-900 font-bold mr-2 py-2 px-3 text-sm rounded-lg cursor-pointermt"},"Approve"),e("button",{type:"button",onClick:t[3]||(t[3]=o=>p(o,!1)),class:"mt-3 cursor-pointer inline-block bg-gray-300 text-gray-900 font-bold mr-2 py-2 px-3 text-sm rounded-lg cursor-pointermt"},"Cancel")])])])])])):a("",!0)],64))}}),K=async()=>C({setup(){q(B,A)},render:()=>D(J)}).mount("#request-container");K().then(()=>{console.log()});
//# sourceMappingURL=requestsDetail.ebbc188a.js.map