# LESSn

[![Build Status](https://travis-ci.org/MaxandreOgeret/lessn.svg?branch=master)](https://travis-ci.org/MaxandreOgeret/lessn)

Lessn is available here : https://lessn.io/

It is a URL Shortening service intended to be as simple and as user friendly as possible.

### How to shorten a link

Just paste your link to shorten into the text area, press → or type enter.
A shortened link will be provided. Click on copy on the right to get the text on the clipboard.

The left ↻ icon will reset the page and you will be able to shorten a new link.

### LinkCheck
One problem of URL shorteners is that you don't know where you will end up. With our __Link checker__ you can paste the 
link and know where you would get redirected. The __Link checker__ is at the bottom of the page.

### Advanced usage

You can register to see all the links you shortened, and how many people visited your link.
You are also able to customize the link unique ID.

## Set up

**To contribute it may be easier to check out my vagrant dev environment on [GitHub](https://github.com/MaxandreOgeret/lessn-dev-env).**

This projects **need** postgresql 11 for it's advanced hashing capabilities.

Otherwise here's how to set up the sebsite locally manually :

 - Make sure you have everything ready for a Symfony 4 project using PHP7.2 and Postgresql 11.

 - Fork then clone clone the project :
 `git clone https://github.com/MaxandreOgeret/lessn.git`
 
 - Copy the .env file to .env.local and set all env variables in the file. 
 
 - Install the vendors with composer :
 `composer install`
 
 - Set up the database :
 `php bin/console do:da:cr`
 `php bin/console do:sc:up --force`
 
### Phishing Links detection

A few malevolent users used the website as a way to spread phishing links.
LESSn now integrates a phishing link detector using Google Safebrowsing Update API v4.

#### Update Phishing database
Run 

> $ php bin/console lessn:safebrowsing:update

To update the phishing database.

Each time a link is visited a phishing verfification is performed before redirecting you.
Links that are performing a redirection to an external website are banned too.

I'm doing all I can to prevent phishing on this website.

__Please open an issue is you find anything wrong on the website or if you have a comment.__
