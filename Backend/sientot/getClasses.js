//This script grabbed the links to all the classes that the student was a member of excluding any that were not from high school

var casper = require('casper').create({
    clientScripts: ["/tmp/jquery.js"],
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
var utils = require('utils');

var studentName = casper.cli.get(0);
var studentPass = casper.cli.get(1);
var freshYr = casper.cli.raw.get(2) - 4;

//This is the IP link to the RIEPS Sakai Portal...The IP link is more realiable than a url
var url = 'http://72.44.195.10/xsl-portal/xlogin/';

casper.start(url, function(){
    this.fill('form', {
        eid: studentName,
        pw: studentPass
    }, false);
    this.click('input#submit');
});

casper.waitForUrl(/xsl-portal$/, function(){
    var nombre = this.evaluate(function() {
        var name = $('li.label').text();
        return name;
    });
});

casper.then(function() {
    this.clickLabel('Membership');
});

casper.waitForSelector('div#footerInfo', function(){
    var memPage = this.evaluate(function(){
        var memLink = document.getElementsByClassName("portletMainIframe")[0].src;
        console.log(memLink);
        return memLink;
    });
    casper.open(memPage);
});
var classLinks = [];
casper.then(function() {
      classLinks = this.evaluate(function(freshYr) {
          function linksOnPage() {
              var classLinks = $("tr td h4 a");
          
              var i, n = classLinks.length;
              for (i = 0; i < n; ++i) {
                  classLinks[i] = classLinks[i].href;
              }
              return classLinks;
          }
          
          var onThePage = linksOnPage();
          var classLinks = [];
          var aClass = $("tr td h4 a");

          var q, p = aClass.length;
          for (q = 0; q < p; ++q) {
              var currEle = aClass[q].innerText;
              var theYear = currEle.split(" ").filter(function(n) {
                  if ((n >= 2000) && (n <= 2099)) return n;
              });
              //excluded classes that were homeroom, gym, or any RIEPS specific classes
              if (currEle.indexOf(freshYr) == -1 && currEle.indexOf("homeroom") == -1 && currEle.indexOf("repository") == -1 && currEle.indexOf("site") == -1 && currEle.indexOf("Physical Education") == -1 && theYear > freshYr) {
                  classLinks.push(onThePage[q]);
              }
          }
            return classLinks;
       }, freshYr);
      
      classLinks.forEach(function(entry) {
          casper.echo(entry+"\n");
      });
});

casper.run();