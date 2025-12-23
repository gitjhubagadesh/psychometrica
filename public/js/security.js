// security.js
app.controller('SecurityController', function ($scope, $window, $interval) {

    // ==== COPY / PASTE / RIGHT CLICK ====
    ['contextmenu', 'copy', 'cut', 'paste'].forEach(evt => {
        document.addEventListener(evt, e => {
            e.preventDefault();
            Swal.fire({
                title: "⚠️ Warning!",
                text: "Copy, Cut, Paste and Right-click are disabled.",
                icon: "warning",
                confirmButtonText: "OK"
            });
        });
    });

    document.addEventListener('dragstart', e => e.preventDefault());
    document.addEventListener('selectstart', e => e.preventDefault());

    // ==== KEYBOARD / DEVTOOLS SHORTCUTS ====
//    document.addEventListener('keydown', e => {
//        if (
//            (e.ctrlKey && ['c', 'v', 'x', 's', 'u', 'p', 'r'].includes(e.key.toLowerCase())) ||
//            (e.ctrlKey && e.shiftKey && ['i', 'j', 'c'].includes(e.key.toLowerCase())) ||
//            ['F12', 'F5', 'PrintScreen'].includes(e.key)
//        ) {
//            e.preventDefault();
//            Swal.fire({
//                title: "⚠️ Restricted!",
//                text: "Developer tools, refresh and screenshot actions are disabled.",
//                icon: "warning",
//                confirmButtonText: "OK"
//            });
//        }
//    });

    history.pushState(null, null, location.href);
    window.onpopstate = function () {
        history.go(1);
    };

//    // ==== SIMPLE DEVTOOLS DETECTION ====
//    (function () {
//        let devtools = /./;
//        devtools.toString = function () {
//            Swal.fire({
//                title: "🚫 Cheating Detected!",
//                text: "Developer tools detected. Please close them immediately.",
//                icon: "error"
//            });
//        };
//        console.log('%c', devtools);
//    })();

    // ==== INTERNET MONITORING ====
    $scope.isOnline = navigator.onLine;
    let offlineStart = null;

    $window.addEventListener('offline', function () {
        offlineStart = Date.now();
        $scope.$apply(() => {
            $scope.isOnline = false;
            Swal.fire({
                title: "⚠️ Connection Lost!",
                text: "You are offline. The test will resume automatically once reconnected.",
                icon: "warning"
            });
        });
        logEvent("Internet disconnected");
    });

    $window.addEventListener('online', function () {
        const offlineDuration = offlineStart ? Date.now() - offlineStart : 0;
        offlineStart = null;
        $scope.$apply(() => {
            $scope.isOnline = true;
            Swal.fire({
                title: "✅ Reconnected!",
                text: "Connection restored. Please continue your test.",
                icon: "success"
            });
        });
        logEvent("Internet reconnected after " + Math.round(offlineDuration / 1000) + "s");
    });

    // ==== TAB SWITCH / MINIMIZE DETECTION ====
    let tabSwitchCount = 0;
    document.addEventListener('visibilitychange', function () {
        if (document.hidden) {
            tabSwitchCount++;
            logEvent("Tab switch detected (" + tabSwitchCount + ")");
            Swal.fire({
                title: "⚠️ Focus Lost!",
                text: "You switched tabs or minimized the window. Please stay on the test screen.",
                icon: "warning"
            });
            if (tabSwitchCount >= 3) {
                Swal.fire({
                    title: "⚠️ Security Alert!",
                    text: "You have navigated away from the test multiple times. Continuing this behavior may result in your test being locked. Please remain on this page to continue.",
                    icon: "warning",
                    confirmButtonText: "I Understand",
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    backdrop: true
                });
            }
        }
    });

    // ==== SCREENSHOT DETERRENCE ====
    // 1️⃣ Detect PrintScreen key
    document.addEventListener('keyup', e => {
        if (e.key === 'PrintScreen')
            triggerScreenshotWarning();
    });

    // 2️⃣ Clear clipboard every few seconds (empties copied screenshots)
    $interval(() => {
        navigator.clipboard.writeText('').catch(() => {
        });
    }, 5000);

    // 3️⃣ Optional: dynamic watermark (makes screenshots traceable)
    const watermark = document.createElement('div');
    watermark.innerText = 'CONFIDENTIAL • ' + new Date().toLocaleString();
    Object.assign(watermark.style, {
        position: 'fixed',
        bottom: '10px',
        right: '10px',
        opacity: 0.1,
        fontSize: '20px',
        color: '#000',
        zIndex: 999999,
        pointerEvents: 'none'
    });
    document.body.appendChild(watermark);

    function triggerScreenshotWarning() {
        logEvent("Screenshot attempt detected");
        Swal.fire({
            title: "⚠️ Screenshot Blocked!",
            text: "Screenshots are not allowed during the test.",
            icon: "warning"
        });
        const overlay = document.createElement('div');
        Object.assign(overlay.style, {
            position: 'fixed',
            top: 0,
            left: 0,
            width: '100vw',
            height: '100vh',
            background: '#000',
            opacity: 1,
            zIndex: 999998
        });
        document.body.appendChild(overlay);
        setTimeout(() => document.body.removeChild(overlay), 1000);
    }

    // ==== LOGGING (PHP endpoint optional) ====
    function logEvent(event) {
        // Uncomment for server logging:
        /*
         fetch('log_event.php', {
         method: 'POST',
         headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
         body: 'event=' + encodeURIComponent(event)
         });
         */
        console.log("Event:", event);
    }

    console.log("✅ Security system initialized.");
});
