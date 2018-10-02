window.addEventListener('DOMContentLoaded', function() {
    //find the cms support link
    // if it's there, add target attribute 
    //set attribute to blank
    
    const supportLink = document.querySelector('#toolbar-link-hNFqr7StwvKp3rPG');
    console.log('Found support link?? ', supportLink);
  
    if (supportLink) {
      supportLink.setAttribute('target', '_blank');
    }
  });
  