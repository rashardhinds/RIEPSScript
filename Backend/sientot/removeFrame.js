//The Purpose of this file is to remove the iframe that is around each of the pages that lists the assignments in that class
//Its a bit odd how Sakai built this...then again this is a super outdated version of the Sakai platform RIEPS was using!
var casper = require('casper').create({
    clientScripts: ["/tmp/jquery.js"],
    //verbose: true,
    logLevel: 'debug',
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

var studentName = casper.cli.get(0);
var studentPass = casper.cli.raw.get(1);
var url = casper.cli.get(2);

casper.start(url, function(){
    this.fill('form', {
        eid: studentName,
        pw: studentPass
    }, false);
    this.click('input#submit');
});

casper.waitForSelector('div#footerInfo', function() {
    this.clickLabel('Assignments');
});
casper.then(function() {
    var memPage = this.evaluate(function(){
        var memLink = document.getElementsByClassName("portletMainIframe")[0].src;
        console.log(memLink);
        return memLink;
    });
    this.echo(memPage);
});

casper.run();

