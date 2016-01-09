
//This script sent off each assignment to be further evaluated an scraped.
var casper = require('casper').create({
    clientScripts: ["/tmp/jquery.js"],
    verbose: true,
    logLevel: 'debug',
    viewportSize: {
      width: 1024,
      height: 768
    },
    pageSettings: {
        webSecurityEnabled: false,
        javascriptEnabled: true,
        loadImages: false,
        loadPlugins: false
    }
});
var utils = require('utils');

var studentName = casper.cli.get(0);
var studentPass = casper.cli.get(1);
var url = casper.cli.get(2);
var folder = casper.cli.get(3);

casper.start(url, function(){
    this.fill('form', {
        eid: studentName,
        pw: studentPass
    }, false);
    this.click('input#submit');
});

casper.waitForSelector('#attachments', function() {
    casper.thenEvaluate(function(){
        //white styling was added for the screenshot as the background was normally transparent
        var newStyle = document.createElement('style');
        newStyle.innerHTML = 'body{ background-color: white !important; }';
        document.body.appendChild(newStyle);
    });    
});
casper.then(function() {
    var links = this.getElementsAttribute('h4 a', 'href');
    links.forEach(function(el) {
        casper.open('http://BACKEND-SERVER-HERE/retrieve.php?link='+encodeURIComponent(el)+'&eid='+studentName+'&pass='+encodeURIComponent(studentPass)+'&path='+encodeURIComponent(folder));
        casper.waitForSelector('#report', function(){
            //this was just added here to give Casper something to do...
            this.echo("Hello");
        });
    });
});

casper.run();