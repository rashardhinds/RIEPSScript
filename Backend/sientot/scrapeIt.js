//This file did the actual scraping on the page, which included downloading the assests into a folder and taking the screenshot.

var fs = require('fs');
var system = require('system');
phantom.casperPath = '/usr/lib/node_modules/casperjs';
phantom.injectJs('/usr/lib/node_modules/casperjs/bin/bootstrap.js');


var casper = require('casper').create({
    clientScripts: ["/tmp/jquery.js", "/tmp/ping.js"],
    viewportSize: {
      width: 1024,
      height: 768
    },
    pageSettings: {
        javascriptEnabled: true,
        loadImages: false,
        loadPlugins: false
    }
});

var utils = require('utils');

var studentName = system.args[1];//casper.cli.get(0);
var studentPass = system.args[2];//casper.cli.get(1);
var url = system.args[3];//casper.cli.raw.get(2);
var folder = system.args[4];//casper.cli.get(3);

casper.start(url, function(){
    this.fill('form', {
        eid: studentName,
        pw: studentPass
    }, false);
    this.click('input#submit');
});
casper.wait(200, function(){
    casper.open(url);
});
casper.wait(200, function(){
    casper.thenEvaluate(function(){
        var newStyle = document.createElement('style');
        newStyle.innerHTML = 'body{ background-color: white !important; }';
        document.body.appendChild(newStyle);
    }); 
    casper.open(url);
});
casper.then(function(){
    var x = require("casper").selectXPath;
    var theStatus = casper.getElementInfo(x("/html/body/div/table/tbody/tr[4]/td")).html;
    //Had to make sure the task is worth scraping, which it checks here...
    if (theStatus.indexOf("Not Started") == -1 && theStatus.indexOf("Draft - In progress") == -1 && theStatus.indexOf("In progress") == -1) {
        var title = casper.getElementInfo(x("/html/body/div/table/tbody/tr[1]/td")).html;
        title = title.replace(/\s+/g, ' ').trim();
        if (title === ""){
            title = "Assignments"+Math.floor((Math.random() * 100) + 1);
        }
        var check = this.evaluate(function(){
            //Works via tmp folders, one is created for each assigment is scrapes then it gets renamed later.
            //System runs SUPER RAM intensive, if I could do it again, I'd definitly improve this... 
            var wsurl = "http://BACKEND-SERVER-LOCATION-HERE/tmpCreator.php";
              return __utils__.sendAJAX(wsurl, 'GET', null, false);
        });
        if (check !== "good") {
            folder = check;
        }
        else {
            folder = "";
        }
        //So this is the part that actually downloads all the files on the assignment page!
        var downloads = this.getElementsAttribute('li a', 'href');
        var i = 0;
        if (downloads.length !== 0) {
                casper.repeat(downloads.length, function() {
                    var extension = Math.floor((Math.random() * 100) + 1) + downloads[i].substring(downloads[i].lastIndexOf('/')+1);
                    //I LOVE CASPER, makes downloading and taking screenshots really easy, just two lines of code.
                    this.download(downloads[i], '/var/www/html/public/tmp'+folder+'/'+extension);
                    casper.captureSelector("/var/www/html/public/tmp"+folder+"/"+"AssignmentPage.jpg", 'html', {
                        quality: 100
                    });
                    ++i;
                });
        }
        this.thenEvaluate(function(title, folder){
            //Sanitizes the folder name for the assignment and sends it off to be created...
            folder = folder.split("/").pop();
            title = title.replace(/\'/g,'');
            title = title.replace(/\./g,'-');
            title = title.replace(/\ /g,'_');
            title = title.replace(/\//g,'-');
            var wsurl = "http://BACKEND-SERVER-LOCATION-HERE/makeFolder.php?folder="+encodeURIComponent(title)+"&name="+folder;
            __utils__.sendAJAX(wsurl, 'POST', null, false);
            }, title, folder);
    }
    else {
        this.echo("Not Valid");
    }
});

casper.run();
