'use strict';

var scripts = document.getElementsByTagName( "script" ) ;
var currentScriptUrl = ( document.currentScript || scripts[scripts.length] ).src ;
var scriptName = currentScriptUrl.length > 0 ? currentScriptUrl : scripts[scripts.length].baseURI.split( "/" ).pop() ;
var arr = scriptName.split('?');
var params = arr[1];

//Apple & GCM variator

if ('serviceWorker' in navigator) {

//navigator.serviceWorker.register('/wp-content/plugins/pushem-notifications-ru/js/sdk/sw.js').then(function(reg) {
navigator.serviceWorker.register(pushem_data.swpath).then(function(reg) {
        var str = reg.scope;
        var arr = str.split('/');
        console.log(':^)',arr[2]);

    //navigator.serviceWorker.getRegistration('/wp-content/plugins/pushem-notifications-ru/js/sdk/sw.js').then(function(reg) {
navigator.serviceWorker.register(pushem_data.swpath).then(function(reg) {        
        reg.pushManager.getSubscription(reg).then(function(sub) {

            var get_settings_line = 'https://pushem.ru/api/sources/get_settings/?'+params;

            //fetch(get_settings_line).then(function(response) {
                //response.json().then(function(data) {
                
                if (sub == null) {
                
                    reg.pushManager.subscribe({
                        userVisibleOnly: true
                    }).then(function(sub) {
                    
                    //get ip and lang
                    var geo_line = "https://pushem.ru/sdk/data.json.php/?ip=1&lang=1";

                        //var endpoint_line = "https://pushem.ru/api/subscribers/add/?"+params+"&gcm_id="+sub.endpoint;
                        //fetch(endpoint_line).then(function(response) {
                        //setTimeout(function() { window.close(); }, 1);

                    fetch(geo_line).then(function(response) {
                        if (response.status !== 200) {
                            var endpoint_line = "https://pushem.ru/api/subscribers/add/?"+params+"&gcm_id="+sub.endpoint;
                            fetch(endpoint_line).then(function(response) {
                                //setTimeout(function() { window.close(); }, 1);
                            });
                        }
                        response.json().then(function(data) {
                            if ( !data.error && data.ip ) {
                                var endpoint_line = "https://pushem.ru/api/subscribers/add/?"+params+"&ip="+data.ip+"&language="+data.lang+"&gcm_id="+sub.endpoint;
                                fetch(endpoint_line).then(function(response) {
                                    //setTimeout(function() { window.close(); }, 1);
                                });

                            }
                        });
                    });

                    });
            } else {
                //setTimeout(function() { window.close(); }, 1);
            }
            //});
            //});
        });
    });

}).catch(function(error) {
    console.log(':^(', error);
});

}

