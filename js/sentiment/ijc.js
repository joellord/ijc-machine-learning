var twit = require("twit");
var keyword = "#ijs17";
var t = new twit(require("./credentials"));
var stream1 = t.stream("statuses/filter", {track: keyword});


var sentiment = require("sentiment");


stream1.on("tweet", function (tweet) {
    var score = sentiment(tweet.text);
    console.log("--- \n New Tweet\n" + tweet.text + "\n" + (score.score >= 0 ? "Positive" : "Negative"));
});
