document.addEventListener("livewire:initialized",()=>{Livewire.on("notify",e=>{const i=typeof e=="string"?e:e.message,t=typeof e=="string"?"info":e.type||"info";a(i,t)}),Livewire.on("delayed-redirect",e=>{setTimeout(()=>{window.location.href=e.url},2e3)})});function a(e,i="info"){let t=document.getElementById("global-notifications");t||(t=document.createElement("div"),t.id="global-notifications",t.className="fixed top-4 right-4 z-50 space-y-2",document.body.appendChild(t));const n=document.createElement("div");n.className=`px-6 py-4 rounded-lg shadow-lg text-white transition-all duration-300 transform translate-x-full animate__animated animate__fadeInRight max-w-md
        ${i==="success"?"bg-green-600":i==="error"?"bg-red-600":i==="warning"?"bg-yellow-600":"bg-blue-600"}`,n.innerHTML=`
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-${i==="success"?"check-circle":i==="error"?"exclamation-triangle":i==="warning"?"exclamation-circle":"info-circle"} text-xl"></i>
            </div>
            <div class="ml-3 flex-1">
                <p class="text-sm font-medium">${e}</p>
            </div>
            <div class="ml-4 flex-shrink-0">
                <button onclick="this.closest('.animate__animated').remove()" 
                        class="text-white hover:text-gray-200 focus:outline-none">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    `,t.appendChild(n),setTimeout(()=>{n.classList.remove("translate-x-full")},10),setTimeout(()=>{n.parentNode&&(n.classList.add("translate-x-full","animate__fadeOutRight"),setTimeout(()=>{n.parentNode&&n.remove()},300))},5e3)}window.showNotification=a;
