CSS3 PIE for IE 6-9
-------------------
Version: 1.0beta5

Usage:
cssElement {
    -webkit-border-radius: 10px;
    -moz-border-radius: 10px;
    border-radius: 10px;
    behavior: url(path/to/PIE.htc);
}

(Path is either absolute or relative to the *HTML DOCUMENT*, not to the CSS file!)
Another way is to use the PHP wrapper:
    behavior: url(path/to/PIE.php);

Website: http://css3pie.com
Known issues: http://css3pie.com/documentation/known-issues/
