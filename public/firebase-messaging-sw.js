/*
Give the service worker access to Firebase Messaging.
Note that you can only use Firebase Messaging here, other Firebase libraries are not available in the service worker.
*/
importScripts('https://www.gstatic.com/firebasejs/7.14.4/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/7.14.4/firebase-messaging.js');

/*
Initialize the Firebase app in the service worker by passing in the messagingSenderId.
* New configuration for app@pulseservice.com
*/
firebase.initializeApp({
   apiKey: "<api-key>",
   authDomain: "<xxxxx>.firebaseapp.com",
   databaseURL: "https://<xxxxx>.firebaseio.com",
   projectId: "<xxxxx>",
   storageBucket: "<xxxxx>.appspot.com",
   messagingSenderId: "<xxxxx>",
   appId: "<xxxxx>"
});

/*
Retrieve an instance of Firebase Messaging so that it can handle background messages.
*/
const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function(payload) {
  console.log('[firebase-messaging-sw.js] Received background message ', payload);
  // Customize notification here
  const notificationTitle = 'Background Message Title';
  const notificationOptions = {
    body: 'Background Message body.',
    icon: '/firebase-logo.png'
  };

  return self.registration.showNotification(notificationTitle,
      notificationOptions);
});

self.addEventListener('notificationclick', function(e) {
    const notification = e.notification
    // MARK 1 -> always takes first item
    const clickAction = notification.click_action
    const action = e.action
    if (action === 'close') {
        notification.close()
    } else {
        clients.openWindow(clickAction)
        notification.close()
    }
})
