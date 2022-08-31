import{d as m,r as i,o as l,c as a,a as e,b as u,F as f,e as p,f as x,h as g}from"./vue.esm-bundler.806d9d8b.js";const b={key:0,class:"relative z-10","aria-labelledby":"modal-title",role:"dialog","aria-modal":"true"},h=e("div",{class:"fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"},null,-1),y={class:"fixed z-10 inset-0 overflow-y-auto"},v={class:"flex items-end sm:items-center justify-center min-h-full p-4 text-center sm:p-0"},w={class:"relative bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full sm:p-6"},_=p('<div class="sm:flex sm:items-start"><div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10"><svg class="h-6 w-6 text-red-600 mb-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg></div><div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left"><h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Delete Policy</h3><div class="mt-2"><p class="text-sm text-gray-500">Are you sure you want to delete the policy? This action cannot\xA0be\xA0undone.</p></div></div></div>',1),k={class:"mt-5 sm:mt-4 sm:flex sm:flex-row-reverse items-center"},C=["href"],P=m({__name:"Policy",setup(d){const o=i(policyId),n=i(!1),r=(c,t)=>{n.value=t};return(c,t)=>(l(),a(f,null,[e("button",{onClick:t[0]||(t[0]=s=>r(s,!0)),class:"cursor-pointer inline-flex items-center justify-center rounded-md border border-transparent bg-red-600 disabled:bg-red-400 disabled:cursor-not-allowed px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:w-auto"},"Delete Policy"),n.value?(l(),a("div",b,[h,e("div",y,[e("div",v,[e("div",w,[_,e("div",k,[e("a",{href:`/admin/staff-management/benefits/policy/${o.value}/delete`,class:"mt-3 cursor-pointer inline-flex items-center justify-center rounded-md border border-transparent bg-red-600 disabled:bg-red-400 disabled:cursor-not-allowed px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:w-auto"},"Delete",8,C),e("button",{type:"button",onClick:t[1]||(t[1]=s=>r(s,!1)),class:"mt-3 cursor-pointer inline-block bg-gray-300 text-gray-900 font-bold mr-2 py-2 px-3 text-sm rounded-lg cursor-pointermt"},"Cancel")])])])])])):u("",!0)],64))}}),j=async()=>x({render:()=>g(P)}).mount("#benefit-policy-container");j().then(()=>{console.log()});
//# sourceMappingURL=benefitPolicy.6ad0c866.js.map
