if ("serviceWorker" in navigator) {
    navigator.serviceWorker.register('/service-worker.js')
        .then( (reg) => {
            //console.log("registered service worker");
        })
        .catch( (error) => {
            console.log("Error registering service worker", error)
        })
}