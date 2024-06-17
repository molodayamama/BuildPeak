function togglePopup(element) {
  var popup = document.getElementById('popup');
  if (popup.style.display === 'none') {
      var rect = element.getBoundingClientRect();
      popup.style.top = rect.bottom + 'px'; 
      popup.style.left = rect.left + 'px'; 
      popup.style.display = 'block';
  } else {
      popup.style.display = 'none'; 
  }
}

window.onclick = function(event) {
  var popup = document.getElementById('popup');
  if (!event.target.matches('.avthor-info, .avthor-info *')) {
      popup.style.display = "none";
  }
}
