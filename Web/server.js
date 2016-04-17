var express = require('express');
var request = require('request');
var http = require('http');
var https = require('https');
var port = process.env.PORT || 1337;
var fs = require('fs');
//var server = express.createServer();
// express.createServer()  is deprecated.
var app = express(); // better instead

app.use(express.static(__dirname + '/website/dist'));


http.createServer(app).listen(port);
