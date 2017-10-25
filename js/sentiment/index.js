var twit = require("twit");
var sentiment = require("sentiment");

var keywords = ["canada", "germany"];

//Twitter Stream listener
var t = new twit(require("./credentials"));

var stream1 = t.stream("statuses/filter", {track: keywords[0]});
var stream2 = t.stream("statuses/filter", {track: keywords[1]});

var totalScore1 = 0;
var count1 = 0;
var totalScore2 = 0;
var count2 = 0;

stream1.on("tweet", function (tweet) {
    var score = sentiment(tweet.text);
    count1++;
    totalScore1 += score.score;
    var avg = Math.round(totalScore1/count1*100)/100;
    console.log("Current score for keyword '" + keywords[0] + "': " + avg);
});

stream2.on("tweet", function (tweet) {
    var score = sentiment(tweet.text);
    count2++;
    totalScore2 += score.score;
    var avg = Math.round(totalScore2/count2*100)/100;
    console.log("Current score for keyword '" + keywords[1] + "': " + avg);
});