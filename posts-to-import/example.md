---
author: Jenn Schiffer
date: '2014-07-17 3:05:00'
slug: the-hassle-of-haskell
status: publish
title: 'The Hassle of Haskell'
category:
  - Haskell
  - Satire
  - Humor
  - Serious
---

Several hours ago I made a controversial decision to leave Node and go back to vanilla JavaScript. This proved to be an overly ambitious move that I sincerely regret, as vanilla JavaScript, unfortunately, is not backwards compatible from Node. This was quite a road block for me, since I have been spoiled by large efforts to retain backwards compatibility in open source projects like WordPress and My Library. I had to quickly change my route and go with another language that I knew had a large community in contrast to vanilla JavaScript — which was lacking in support in the local developer community.

![There is no vanilla JavaScript support in the greater NJ/NYC area, even near the capitol of JavaScript, Brooklyn.](https://d262ilb51hltx0.cloudfront.net/max/811/1*3Ok4jwLmqStG-TCLTZhtFQ.png)

As we developers have learned over the past few weeks, [Go](https://medium.com/code-adventures/farewell-node-js-4ba9e7f3e52b) is another replacement for Node. It turns out most languages can be, Haskell included, so I decided to give that a shot. I figured that if other people were blogging about their post-Node Go experiences, it would be more interesting for me and the Web to blog about my post-Node Haskell ones. Try to keep the Blogosphere diverse, you know?

> "Blogosphere diversity is the optimal metric for making high-success-potential 
> programming language decisions.” -Ev Williams, inventer of the weblog

But it turns out that Haskell is not as perfect as some of their seasoned developers had lead me and the Blogosphere to believe. For one, I had to install it, which means it’s not portable like vanilla JavaScript and also takes up space unlike vanilla JavaScript. The biggest problem, though, is inconsistency in the compiler, GHC.

When creating a range, for example — the only example, as this is all I’ve been able to do with the language without throwing my computer against a wall — the compiler works inconsistently when you introduce multiple negative numbers into the range.

    > [-5..0]
    [-5,-4,-3,-2,-1,0]

See, this is good behavior. I give it -5 and 0 and it returns the range. But what if I make the range all negative?

    >[-10..-5]
    <interactive>:6:5: Not in scope: `..-'

This is strange. Can we not get all-negative ranges? Actually we can, by adding spaces.

    >[-10.. -5]
   [-10,-9,-8,-7,-6,-5]

You may be thinking that I’m making this all up. How can Haskell not be on point when it comes to basically everything mathematical? Is this even math? Is math needed in programming ever? I took a screenshot of my terminal to show this inconsistency in action:

![Haskell being a hassle.](https://d262ilb51hltx0.cloudfront.net/max/824/1*5YGOPoVl6AHvEs8uULPxzg.png)

So it turns out that Haskell is not as “functional” a programming language as I would like it to be. It is inconsistent and cannot do basic math as proven by my previously mentioned evidence. In conclusion, I’m going to stick to Node and Rust until Haskell is further developed and at least out of beta.