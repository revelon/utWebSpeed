var test = document.createElement('div');
test.innerHTML = '&nbsp;';
test.className = 'adsbox';
document.body.appendChild(test);
window.setTimeout(function() {
  if (test.offsetHeight === 0 && !sessionStorage.blkr) {
    sessionStorage.blkr = 1;
    dataLayer.push({'event':'ga.event','eCategory':'BlockOfAds','eAction':'Detected','eLabel':undefined,'eValue':undefined});
  }
  test.remove();
}, 100);