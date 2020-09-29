// Fallback pro IE
if (isIE()) {
    document.getElementById('ie-fallback').style.cssText = 'display: block';
    document.getElementById('app').style.cssText = 'display: none';
}

// Vrátí true, pokud uživatel používá Internet Explorer
function isIE() { return navigator.userAgent.indexOf('MSIE ') > -1 || navigator.userAgent.indexOf('Trident/') > -1 };
