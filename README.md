# DevTools-for-TIM-HH132V1
DevTools with WebUi for TIM HH132V1. This script creates a webservice which allows you to query the api of your router and it gives you access to additional informatiosn (e.g. GB used) which are missing from the rounters original WebUi.

You can start the webservice with the following command: "php -S 192.168.1.58:8585"  Then go to the WebUi in your browser and provice the token. You can obtain the token using the console of your browser, the token is part of the header.

DO NOT EXPOSE THE WEB SERVICE TO THE WWW! Use a firewall to block the used port from unauthorized access!  Use this script at your own risk!
