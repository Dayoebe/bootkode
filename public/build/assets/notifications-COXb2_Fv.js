document.addEventListener("livewire:initialized",()=>{Livewire.on("notify",n=>{const t=typeof n=="string"?n:n.message,e=typeof n=="string"?"info":n.type||"info";i(t,e)})});function i(n,t="info"){const e=document.createElement("div");e.className=`px-6 py-3 rounded-lg shadow-lg text-white transition-all duration-300 transform translate-x-full animate__animated animate__fadeInRight
        ${t==="success"?"bg-green-600":t==="error"?"bg-red-600":t==="warning"?"bg-yellow-600":"bg-blue-600"}`,e.innerHTML=`
        <div class="flex items-center">
            <i class="fas fa-${t==="success"?"check":t==="error"?"exclamation-triangle":t==="warning"?"exclamation-circle":"info"} mr-2"></i>
            <span>${n}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `,document.getElementById("global-notifications").appendChild(e),setTimeout(()=>{e.classList.remove("translate-x-full")},10),setTimeout(()=>{e.parentNode&&(e.classList.add("translate-x-full","animate__fadeOutRight"),setTimeout(()=>{e.parentNode&&e.remove()},300))},5e3)}
