'use strict';

//console.log('Started', self);
self.addEventListener('install', function(event) {
  self.skipWaiting();
  console.log('Installed', event);
});
self.addEventListener('activate', function(event) {
  console.log('Activated', event);
});

registration.pushManager.getSubscription().then(function(subscription) {
  //console.log("got subscription id: ", subscription.endpoint);

  if (subscription) {

//var api_url = registration.scope+'query.json.php'; //SDK CHANGE + KEY & UID & SOURCE & COMBINE_DOMAIN

//var endpoint_line = api_url+'?sub='+subscription.endpoint;
var endpoint_line = 'https://pushem.ru/sdk/query.json.php?sub='+subscription.endpoint;
//console.log(endpoint_line);

self.addEventListener('push', function(event) {
event.waitUntil(fetch(endpoint_line).then(function(response) {

  //console.log(response);
      if (response.status !== 200) {
        console.log('Looks like there was a problem. Status Code: ' + response.status);  
        throw new Error();
      }

      // Examine the text in the response  
      return response.json().then(function(data) {
        //console.log(data);
        
        if ( !data.error && data.notification ) {
        
        //console.log(data.notification.title);
        
        var title = data.notification.title;  
        var message = data.notification.body;
        var icon = data.notification.icon;
        var link = data.notification.link; //COMES WITH NOTIFICATION ID + SUBSCRIBER_ID + UTM
        var audio = data.notification.audio;
        var tag = data.notification.tag;
        var subid = data.notification.act_sub_id; //для кликов (кто)
        var id = data.notification._id; //для кликов (номер нотификации)

	
	if (title != 'undefined' && title != '') {
        return self.registration.showNotification(title, {  
          body: message,
          icon: icon,
          tag: tag,
          audio: audio,
          data : {
            link : link,
            subid : subid,
            id : id
            }
        });
	}

    	} else {
        throw new Error();
      }

      });
    })
);

});


self.addEventListener('notificationclick', function(event) {
  event.notification.close();
  //console.log(event);
  //CALL CLICK COUNTER + NOTIFICATION ID + SUBSCRIBER_ID
  var url = event.notification.data.link;
  var subid = event.notification.data.subid;
  var id = event.notification.data.id;

  event.waitUntil(
    clients.matchAll({
      type: 'window'
    })
    .then(function(windowClients) {
      //console.log('WindowClients', windowClients);
      for (var i = 0; i < windowClients.length; i++) {
        var client = windowClients[i];
        //console.log('WindowClient', client);
        if (client.url === url && 'focus' in client) {
          var focussuburl = registration.scope;
          focussuburl = focussuburl.substring(0, focussuburl.length - 5);
          //var focusclickurl = focussuburl+'/api/notifications/click/?id='+id+'&sub_id='+subid;
          var focusclickurl = 'https://pushem.ru/api/notifications/click/?id='+id+'&sub_id='+subid;
          //console.log(focusclickurl);
          fetch(focusclickurl).then(function(response) { });

          return client.focus();
        }
      }
      if (clients.openWindow) {
        //CALL OPEN SITE COUNTER + NOTIFICATION ID + SUBSCRIBER_ID (Encode TOKEN??)
        var suburl = registration.scope;
        suburl = suburl.substring(0, suburl.length - 5);
        //var clickurl = suburl+'/api/notifications/click//?id='+id+'&sub_id='+subid;
        var clickurl = 'https://pushem.ru/api/notifications/click/?id='+id+'&sub_id='+subid;
        //console.log(clickurl);
        fetch(clickurl).then(function(response) { });

        return clients.openWindow(url);
      }
    })
  );
});

}

});