window.addEventListener('DOMContentLoaded', function() {
    //find the cms support link
    //if it's there, add target attribute 
    //set attribute to blank
    const supportLink = document.querySelector('#toolbar-item-workbench-tray .menu-item a[href="https://ibm-dte.slack.com/app_redirect?channel=cms"] ');
  
    if (supportLink) {
      supportLink.setAttribute('target', '_blank');
    }
  });
  