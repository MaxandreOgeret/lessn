# LESSn

Lessn is available here : https://www.lessn.io/

It is a URL Shortening service intended to be as simple and as user friendly as possible.

### How to shorten a link

Just paste your link to shorten into the text area, press → or type enter.
A shortened link will be provided. Click on copy on the right to get the text on the clipboard.

The left ↻ icon will reset the page and you will be able to shorten a new link.

### Advanced usage

You can register to see all the links you shortened, and how many people visited your link.
You are also able to customize totally your new links !

### Phishing Links detection

A few malevolent users used the website as a way to spread phishing links.
LESSn now integrates a phishing link detector.

#### Update Phishing database
Run 

> $ php bin/console lessn:banlinks:update

To update the phishing database and to automatically start a link scan.